<?php

namespace App\Controllers;

use App\Models\AccessLogModel;

class Stats extends BaseController
{
    public function summary()
    {
        $db = \Config\Database::connect();
        $stats = [];

        $stats['total_logs'] = $db->query('SELECT COUNT(*) AS c FROM access_logs')->getRowArray()['c'] ?? 0;
        $stats['otp_success'] = $db->query("SELECT COUNT(*) AS c FROM access_logs WHERE action='otp_request' AND status='success'")->getRowArray()['c'] ?? 0;
        $stats['otp_fail'] = $db->query("SELECT COUNT(*) AS c FROM access_logs WHERE action='otp_request' AND status='fail'")->getRowArray()['c'] ?? 0;
        $stats['by_app'] = $db->query('SELECT application_id, COUNT(*) c FROM access_logs GROUP BY application_id ORDER BY c DESC LIMIT 10')->getResultArray();

        return $this->response->setJSON($stats);
    }
}
