<?php


namespace App\Models;

use CodeIgniter\Model;

class KycModel extends Model
{
    protected $table      = 'kyc';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'customer_id',
        'id_type',
        'id_number',
        'id_photo',
        'employment_status',
        'monthly_income',
        'proof_of_income',
        'bank_name',
        'bank_account',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'customer_id'       => 'required|integer',
        'id_type'           => 'required|in_list[passport,drivers_license,national_id,nbi]',
        'id_number'         => 'required|string|max_length[100]',
        'id_photo'          => 'required|string',
        'employment_status' => 'required|in_list[employed,self_employed,unemployed,student]',
        'monthly_income'    => 'required|numeric',
        'proof_of_income'   => 'required|string',
        'bank_name'         => 'string|max_length[100]',
        'bank_account'      => 'string|max_length[100]',
        'status'            => 'in_list[Pending,Approved,Rejected]',
    ];

    protected $validationMessages = [
        'id_type' => [
            'in_list' => 'Please select a valid ID type.',
        ],
        'employment_status' => [
            'in_list' => 'Please select a valid employment status.',
        ],
    ];

    public function getByCustomer($customer_id)
    {
        return $this->where('customer_id', $customer_id)->first();
    }

    public function getPending()
    {
        return $this->where('status', 'Pending')->findAll();
    }

    public function getApproved()
    {
        return $this->where('status', 'Approved')->findAll();
    }

    public function getRejected()
    {
        return $this->where('status', 'Rejected')->findAll();
    }
}