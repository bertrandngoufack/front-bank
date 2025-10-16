<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table            = 'applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'type', 'url', 'exe_path', 'is_active', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected array $validationRules   = [
        'name' => 'required|min_length[2]|max_length[150]',
        'type' => 'required|in_list[web,windows]'
    ];
}
