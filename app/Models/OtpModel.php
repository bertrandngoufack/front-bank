<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table         = 'otps';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['application_id', 'email', 'otp_code', 'expires_at', 'used_at', 'created_at'];
}
