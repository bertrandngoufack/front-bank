<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitSchema extends Migration
{
    public function up(): void
    {
        // applications table: business app links and Windows executables
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['web', 'windows'],
                'default' => 'web',
            ],
            'url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'exe_path' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Local path to uploaded .exe',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('applications');

        // access_logs table: OTP requests and access audits
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'comment' => 'otp_request, otp_verify, link_open, exe_download',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'comment' => 'success, fail',
            ],
            'meta' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['application_id']);
        $this->forge->createTable('access_logs');

        // otps table: store OTP tokens issued
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 190,
            ],
            'otp_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['application_id', 'email']);
        $this->forge->createTable('otps');
    }

    public function down(): void
    {
        $this->forge->dropTable('otps', true);
        $this->forge->dropTable('access_logs', true);
        $this->forge->dropTable('applications', true);
    }
}
