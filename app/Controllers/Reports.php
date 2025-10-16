<?php

namespace App\Controllers;

use App\Models\AccessLogModel;

class Reports extends BaseController
{
    public function accessLogs()
    {
        $model = new AccessLogModel();
        $email = $this->request->getPost('email');
        $action = $this->request->getPost('action');
        $status = $this->request->getPost('status');

        if ($email) {
            $model->where('email', $email);
        }
        if ($action) {
            $model->where('action', $action);
        }
        if ($status) {
            $model->where('status', $status);
        }

        $logs = $model->orderBy('created_at', 'DESC')->findAll(200);
        return $this->response->setJSON($logs);
    }

    public function exportCsv()
    {
        $logs = (new AccessLogModel())->orderBy('created_at', 'DESC')->findAll(1000);
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['id','application_id','email','ip_address','user_agent','action','status','meta','created_at']);
        foreach ($logs as $row) {
            fputcsv($csv, [
                $row['id'], $row['application_id'], $row['email'], $row['ip_address'],
                $row['user_agent'], $row['action'], $row['status'], $row['meta'], $row['created_at'],
            ]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        return $this->response->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="access_logs.csv"')
            ->setBody($content);
    }
}
