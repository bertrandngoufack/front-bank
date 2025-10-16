<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\ApplicationModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class AdminApps extends BaseController
{
    private AuditLogger $audit;

    public function __construct()
    {
        $this->audit = new AuditLogger();
    }

    private function isAuthorized(): bool
    {
        $token = $this->request->getHeaderLine('X-Admin-Token');
        return $token !== '' && hash_equals((string) env('auth.adminToken'), $token);
    }

    public function create()
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $name = trim((string) $this->request->getPost('name'));
        $type = trim((string) $this->request->getPost('type'));
        $url  = trim((string) $this->request->getPost('url'));

        if ($name === '' || ! in_array($type, ['web','windows'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Paramètres invalides']);
        }

        $data = ['name' => $name, 'type' => $type, 'url' => $url, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
        $id = (new ApplicationModel())->insert($data, true);

        $this->audit->write('admin_app_create', ['id' => $id] + $data);
        return $this->response->setJSON(['id' => $id]);
    }

    public function list()
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $apps = (new ApplicationModel())->orderBy('updated_at', 'DESC')->findAll(100);
        return $this->response->setJSON($apps);
    }

    public function update()
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $id = (int) $this->request->getPost('id');
        if ($id <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'ID invalide']);
        }
        $fields = ['name','type','url','is_active'];
        $data = [];
        foreach ($fields as $f) {
            if (null !== $this->request->getPost($f)) {
                $data[$f] = $this->request->getPost($f);
            }
        }
        if ($data === []) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Aucune modification']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        (new ApplicationModel())->update($id, $data);
        $this->audit->write('admin_app_update', ['id' => $id] + $data);
        return $this->response->setJSON(['ok' => true]);
    }

    public function delete()
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $id = (int) $this->request->getPost('id');
        if ($id <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'ID invalide']);
        }
        (new ApplicationModel())->delete($id);
        $this->audit->write('admin_app_delete', ['id' => $id]);
        return $this->response->setJSON(['ok' => true]);
    }

    public function uploadExe()
    {
        if (! $this->isAuthorized()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        $id = (int) $this->request->getPost('application_id');
        /** @var UploadedFile|null $file */
        $file = $this->request->getFile('exe');
        if ($id <= 0 || ! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Paramètres invalides']);
        }
        $ext = strtolower($file->getExtension() ?? '');
        if (! in_array($ext, ['exe','msi','zip'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Extension non autorisée']);
        }
        $targetDir = WRITEPATH . 'uploads';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $newName = 'app_' . $id . '_' . time() . '.' . $ext;
        $file->move($targetDir, $newName, true);
        $path = $targetDir . DIRECTORY_SEPARATOR . $newName;
        (new ApplicationModel())->update($id, ['exe_path' => $path, 'updated_at' => date('Y-m-d H:i:s')]);
        $this->audit->write('admin_app_upload_exe', ['id' => $id, 'exe_path' => $path]);
        return $this->response->setJSON(['ok' => true, 'path' => $path]);
    }
}
