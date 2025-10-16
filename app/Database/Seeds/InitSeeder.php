<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->table('applications')->insertBatch([
            [
                'name' => 'Intranet Banque',
                'type' => 'web',
                'url'  => 'https://intranet.example.cm',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Sandbox Windows - Outil Risque',
                'type' => 'windows',
                'exe_path' => 'writable/uploads/risque.exe',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
