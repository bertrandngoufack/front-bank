<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\AccessLogModel;
use App\Models\ApplicationModel;
use App\Models\OtpModel;
use CodeIgniter\I18n\Time;
use Config\Services;

class Apps extends BaseController
{
    private AuditLogger $audit;

    public function __construct()
    {
        $this->audit = new AuditLogger();
    }

    public function search()
    {
        $q = trim($this->request->getPost('q') ?? '');
        $apps = [];
        if ($q !== '') {
            $apps = (new ApplicationModel())
                ->like('name', $q)
                ->where('is_active', 1)
                ->findAll(20);
        }
        return $this->response->setJSON(['query' => $q, 'results' => $apps]);
    }

    public function requestOtp()
    {
        $applicationId = (int) ($this->request->getPost('application_id') ?? 0);
        $email = trim($this->request->getPost('email') ?? '');

        $this->audit->write('otp_request', ['application_id' => $applicationId, 'email' => $email]);

        if ($applicationId <= 0 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Paramètres invalides']);
        }

        $otp = random_int(100000, 999999);
        $expires = Time::now('UTC')->addMinutes(10)->toDateTimeString();

        (new OtpModel())->insert([
            'application_id' => $applicationId,
            'email' => $email,
            'otp_code' => (string) $otp,
            'expires_at' => $expires,
            'created_at' => Time::now('UTC')->toDateTimeString(),
        ]);

        $this->sendOtpEmail($email, $otp);

        (new AccessLogModel())->insert([
            'application_id' => $applicationId,
            'email' => $email,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'action' => 'otp_request',
            'status' => 'success',
            'meta' => json_encode(['expires_at' => $expires], JSON_UNESCAPED_UNICODE),
            'created_at' => Time::now('UTC')->toDateTimeString(),
        ]);

        return $this->response->setJSON(['message' => 'OTP envoyé']);
    }

    public function verifyOtp()
    {
        $applicationId = (int) ($this->request->getPost('application_id') ?? 0);
        $email = trim($this->request->getPost('email') ?? '');
        $otp = trim($this->request->getPost('otp') ?? '');

        if ($applicationId <= 0 || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $otp === '') {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Paramètres invalides']);
        }

        $otpModel = new OtpModel();
        $record = $otpModel->where('application_id', $applicationId)
            ->where('email', $email)
            ->where('otp_code', $otp)
            ->orderBy('id', 'DESC')
            ->first();

        if (! $record) {
            (new AccessLogModel())->insert([
                'application_id' => $applicationId,
                'email' => $email,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'action' => 'otp_verify',
                'status' => 'fail',
                'meta' => json_encode(['reason' => 'not_found'], JSON_UNESCAPED_UNICODE),
                'created_at' => Time::now('UTC')->toDateTimeString(),
            ]);
            return $this->response->setStatusCode(401)->setJSON(['error' => 'OTP invalide']);
        }

        if ($record['used_at'] !== null || strtotime($record['expires_at']) < time()) {
            (new AccessLogModel())->insert([
                'application_id' => $applicationId,
                'email' => $email,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'action' => 'otp_verify',
                'status' => 'fail',
                'meta' => json_encode(['reason' => 'expired_or_used'], JSON_UNESCAPED_UNICODE),
                'created_at' => Time::now('UTC')->toDateTimeString(),
            ]);
            return $this->response->setStatusCode(401)->setJSON(['error' => 'OTP expiré ou déjà utilisé']);
        }

        $otpModel->update($record['id'], ['used_at' => Time::now('UTC')->toDateTimeString()]);

        $app = (new ApplicationModel())->find($applicationId);

        (new AccessLogModel())->insert([
            'application_id' => $applicationId,
            'email' => $email,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'action' => 'otp_verify',
            'status' => 'success',
            'meta' => json_encode(['app_type' => $app['type'] ?? null], JSON_UNESCAPED_UNICODE),
            'created_at' => Time::now('UTC')->toDateTimeString(),
        ]);

        return $this->response->setJSON([
            'ok' => true,
            'application' => [
                'id' => $applicationId,
                'type' => $app['type'] ?? null,
                'url' => $app['url'] ?? null,
                'exe' => $app['exe_path'] ?? null,
            ],
        ]);
    }

    public function getExe()
    {
        $applicationId = (int) ($this->request->getPost('application_id') ?? 0);
        $email = trim($this->request->getPost('email') ?? '');
        $otp = trim($this->request->getPost('otp') ?? '');

        $app = (new ApplicationModel())->find($applicationId);
        if (! $app || ($app['type'] ?? '') !== 'windows' || empty($app['exe_path'])) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Exécutable introuvable']);
        }

        $otpModel = new OtpModel();
        $record = $otpModel->where('application_id', $applicationId)
            ->where('email', $email)
            ->where('otp_code', $otp)
            ->orderBy('id', 'DESC')
            ->first();
        if (! $record || strtotime($record['expires_at']) < time()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'OTP invalide']);
        }

        (new AccessLogModel())->insert([
            'application_id' => $applicationId,
            'email' => $email,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'action' => 'exe_download',
            'status' => 'success',
            'meta' => json_encode([], JSON_UNESCAPED_UNICODE),
            'created_at' => Time::now('UTC')->toDateTimeString(),
        ]);

        return $this->response->download($app['exe_path'], null);
    }

    private function sendOtpEmail(string $email, int $otp): void
    {
        $emailSvc = Services::email();
        $emailSvc->setTo($email);
        $emailSvc->setSubject('Votre OTP de connexion');
        $emailSvc->setMessage('Votre code OTP est: <b>' . $otp . '</b>. Il expire dans 10 minutes.');
        @$emailSvc->send();
    }
}
