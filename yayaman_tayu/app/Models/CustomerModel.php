<?php


namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table      = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'fullname',
        'email',
        'password',
        'contact_number',
        'birthdate',
        'gender',
        'civil_status',
        'address_current',
        'address_permanent',
        'profile_photo',
        'is_verified',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'fullname' => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[customers.email]',
        'password' => 'required|min_length[8]',
    ];

    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getCustomerWithLoans($customerId)
    {
        return $this->select('customers.*, COUNT(loans.id) as total_loans')
                    ->join('loans', 'loans.customer_id = customers.id', 'left')
                    ->where('customers.id', $customerId)
                    ->groupBy('customers.id')
                    ->first();
    }

    public function getActiveCustomers()
    {
        return $this->where('is_active', 1)
                    ->orderBy('fullname', 'ASC')
                    ->findAll();
    }

    public function countTotalCustomers()
    {
        return $this->countAllResults();
    }

    public function countVerifiedCustomers()
    {
        return $this->where('is_verified', 1)
                    ->countAllResults();
    }
}