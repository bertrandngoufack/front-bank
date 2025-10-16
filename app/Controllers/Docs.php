<?php

namespace App\Controllers;

class Docs extends BaseController
{
    public function index()
    {
        $openapi = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'KissAI Front API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/apps/search' => [
                    'post' => [
                        'summary' => "Recherche d'applications",
                        'requestBody' => ['required' => true],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                ],
                '/apps/request-otp' => [
                    'post' => [
                        'summary' => "Demande d'OTP par email",
                        'requestBody' => ['required' => true],
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                ],
                '/admin/reports/access-logs' => [
                    'post' => [
                        'summary' => "Liste filtrée des logs d'accès",
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                ],
                '/admin/reports/export-csv' => [
                    'post' => [
                        'summary' => 'Export CSV des logs',
                        'responses' => ['200' => ['description' => 'CSV']],
                    ],
                ],
                '/admin/stats/summary' => [
                    'post' => [
                        'summary' => 'Résumé statistiques',
                        'responses' => ['200' => ['description' => 'OK']],
                    ],
                ],
            ],
        ];

        return $this->response->setJSON($openapi);
    }
}
