<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AuditArchive extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'audit:archive';
    protected $description = 'Archive les logs d’audit quotidiens en ZIP';

    public function run(array $params)
    {
        $logDir = rtrim(env('audit.logDir', WRITEPATH . 'logs/audit'), '/');
        $archiveDir = rtrim(env('audit.archiveDir', WRITEPATH . 'archive'), '/');

        if (! is_dir($archiveDir)) {
            mkdir($archiveDir, 0775, true);
        }

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $logFile = $logDir . '/' . $yesterday . '.log';
        if (! is_file($logFile)) {
            CLI::write("Aucun log pour $yesterday", 'yellow');
            return;
        }

        $zipPath = $archiveDir . "/audit-$yesterday.zip";
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            CLI::error('Impossible de créer l’archive');
            return;
        }
        $zip->addFile($logFile, basename($logFile));
        $zip->close();
        CLI::write("Archivé: $zipPath", 'green');
    }
}
