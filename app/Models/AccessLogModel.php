<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessLogModel extends Model
{
    protected $table            = 'access_logs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['application_id', 'email', 'ip_address', 'user_agent', 'action', 'status', 'meta', 'created_at'];
}
