<?php


namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table      = 'admin';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'name',
        'fullname',
        'username',
        'email',
        'password',
        'phone',
        'contact',
        'photo',
        'profile_photo',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[admin.username]',
        'email'    => 'required|valid_email|is_unique[admin.email]',
        'password' => 'required|min_length[8]',
    ];

    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getActiveAdmins()
    {
        return $this->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function countTotalAdmins()
    {
        return $this->countAllResults();
    }
}