<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

class AuditLogger
{
    private string $logDir;

    public function __construct()
    {
        $this->logDir = rtrim(env('audit.logDir', WRITEPATH . 'logs/audit'), '/');
        if (! is_dir($this->logDir)) {
            mkdir($this->logDir, 0775, true);
        }
    }

    public function write(string $category, array $data = []): void
    {
        $timestamp = Time::now('UTC')->toDateTimeString('microsecond');
        $payload = [
            'ts' => $timestamp,
            'category' => $category,
            'ip' => service('request')->getIPAddress(),
            'ua' => service('request')->getUserAgent()->getAgentString(),
            'data' => $data,
        ];
        $file = $this->logDir . '/' . date('Y-m-d') . '.log';
        file_put_contents($file, json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}
