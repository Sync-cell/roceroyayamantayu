<?php


namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    protected $table = 'loans';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'customer_id',
        'amount',
        'purpose',
        'term',
        'term_days',
        'term_months',
        'interest_rate',
        'penalty_rate',
        'payment_method',
        'disbursement_method',
        'account_holder_name',
        'bank_gcash_account',
        'supporting_documents',
        'status',
        'admin_notes',
        'final_amount',
        'release_date',
        'released_amount',
        'next_payment_date',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'customer_id' => 'required|integer',
        'amount' => 'required|numeric|greater_than[4999]|less_than[500001]',
        'term_months' => 'required|integer|in_list[3,6,12,18,24]',
        'purpose' => 'required|string|min_length[10]|max_length[500]',
        'payment_method' => 'required|in_list[weekly,bi-weekly,monthly]',
        'disbursement_method' => 'required|in_list[bank_transfer,gcash]',
        'account_holder_name' => 'required|min_length[3]|max_length[100]',
        'bank_gcash_account' => 'required|min_length[4]|max_length[50]',
        'interest_rate' => 'numeric',
    ];

    protected $validationMessages = [
        'amount' => [
            'required' => 'Loan amount is required.',
            'numeric' => 'Loan amount must be a valid number.',
            'greater_than' => 'Minimum loan amount is ₱5,000.',
            'less_than' => 'Maximum loan amount is ₱500,000.',
        ],
        'term_months' => [
            'required' => 'Loan term is required.',
            'integer' => 'Loan term must be a whole number.',
            'in_list' => 'Please select a valid loan term (3, 6, 12, 18, or 24 months).',
        ],
        'purpose' => [
            'required' => 'Loan purpose is required.',
            'min_length' => 'Please provide a detailed purpose (at least 10 characters).',
            'max_length' => 'Loan purpose cannot exceed 500 characters.',
        ],
        'account_holder_name' => [
            'required' => 'Account holder name is required.',
            'min_length' => 'Account holder name must be at least 3 characters.',
        ],
        'bank_gcash_account' => [
            'required' => 'Account number is required.',
            'min_length' => 'Account number must be at least 4 characters.',
            'max_length' => 'Account number cannot exceed 50 characters.',
        ],
    ];

    /**
     * Get loan with customer details
     */
    public function getLoanWithCustomer($loanId)
    {
        return $this->select('loans.*, customers.fullname as customer_name, customers.email as customer_email, customers.contact_number as customer_phone')
                    ->join('customers', 'customers.id = loans.customer_id', 'left')
                    ->where('loans.id', $loanId)
                    ->first();
    }

    /**
     * Compatibility alias used by controllers
     */
    public function getWithCustomer($loanId)
    {
        return $this->getLoanWithCustomer($loanId);
    }

    /**
     * Get all loans with customer details
     */
    public function getAllLoansWithCustomer()
    {
        return $this->select('loans.*, customers.fullname as customer_name, customers.email as customer_email, customers.contact_number as customer_phone')
                    ->join('customers', 'customers.id = loans.customer_id', 'left')
                    ->orderBy('loans.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get customer's loans
     */
    public function getCustomerLoans($customerId)
    {
        if (empty($customerId)) {
            return [];
        }

        return $this->where('customer_id', (int) $customerId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get loans by status
     */
    public function getLoansByStatus($status)
    {
        return $this->select('loans.*, customers.fullname, customers.email, customers.contact_number')
                    ->join('customers', 'customers.id = loans.customer_id', 'left')
                    ->where('loans.status', $status)
                    ->orderBy('loans.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending loans for admin review
     */
    public function getPendingLoans()
    {
        return $this->getLoansByStatus('Pending');
    }

    /**
     * Get approved loans
     */
    public function getApprovedLoans()
    {
        return $this->getLoansByStatus('Approved');
    }

    /**
     * Get released loans
     */
    public function getReleasedLoans()
    {
        return $this->getLoansByStatus('Released');
    }

    /**
     * Get rejected loans
     */
    public function getRejectedLoans()
    {
        return $this->getLoansByStatus('Rejected');
    }

    /**
     * Update loan status
     */
    public function updateStatus($loanId, $status)
    {
        return $this->update($loanId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Approve a loan
     */
    public function approveLoan($loanId, $interestRate, $penaltyRate, $adminNotes = null)
    {
        $loan = $this->find($loanId);
        if (!$loan) {
            return false;
        }

        $finalAmount = $loan['amount'] + ($loan['amount'] * $interestRate / 100);

        return $this->update($loanId, [
            'status' => 'Approved',
            'interest_rate' => $interestRate,
            'penalty_rate' => $penaltyRate,
            'final_amount' => $finalAmount,
            'admin_notes' => $adminNotes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject a loan
     */
    public function rejectLoan($loanId, $adminNotes = null)
    {
        return $this->update($loanId, [
            'status' => 'Rejected',
            'admin_notes' => $adminNotes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get loan statistics
     */
    public function getLoanStats()
    {
        $db = \Config\Database::connect();
        
        $totalAmount = $db->table('loans')->selectSum('amount')->get()->getRow()->amount ?? 0;
        $releasedAmount = $db->table('loans')->where('status', 'Released')->selectSum('released_amount')->get()->getRow()->released_amount ?? 0;
        
        return [
            'total_loans' => $this->countAllResults(),
            'pending_loans' => $this->where('status', 'Pending')->countAllResults(),
            'approved_loans' => $this->where('status', 'Approved')->countAllResults(),
            'released_loans' => $this->where('status', 'Released')->countAllResults(),
            'rejected_loans' => $this->where('status', 'Rejected')->countAllResults(),
            'total_amount' => $totalAmount,
            'released_amount' => $releasedAmount,
        ];
    }

    /**
     * Get loans with filters
     */
    public function getLoansWithFilters($status = 'all', $limit = 0, $offset = 0)
    {
        $query = $this->select('loans.*, customers.fullname as customer_name, customers.email as customer_email, customers.contact_number as customer_phone')
                      ->join('customers', 'customers.id = loans.customer_id', 'left');
        
        if ($status !== 'all' && !empty($status)) {
            $query->where('loans.status', $status);
        }
        
        $query->orderBy('loans.created_at', 'DESC');
        
        if ($limit > 0) {
            $query->limit($limit, $offset);
        }
        
        return $query->findAll();
    }

    /**
     * Release a loan (update status and set release date)
     */
    public function releaseLoan($loanId, $releasedAmount = null)
    {
        $loan = $this->find($loanId);
        if (!$loan) {
            return false;
        }

        if ($releasedAmount === null) {
            $releasedAmount = $loan['final_amount'] ?? $loan['amount'];
        }

        $nextPaymentDate = $this->calculateNextPaymentDate(date('Y-m-d'));

        return $this->update($loanId, [
            'status' => 'Released',
            'release_date' => date('Y-m-d H:i:s'),
            'released_amount' => $releasedAmount,
            'next_payment_date' => $nextPaymentDate,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Calculate next payment date (based on payment method)
     */
    public function calculateNextPaymentDate($releaseDate, $paymentMethod = 'monthly')
    {
        $releaseDateTime = strtotime($releaseDate);
        
        switch ($paymentMethod) {
            case 'weekly':
                return date('Y-m-d', strtotime($releaseDate . ' + 7 days'));
            case 'bi-weekly':
                return date('Y-m-d', strtotime($releaseDate . ' + 14 days'));
            case 'monthly':
            default:
                return date('Y-m-d', strtotime($releaseDate . ' + 30 days'));
        }
    }

    /**
     * Get loan by ID with full details
     */
    public function getLoanDetails($loanId)
    {
        return $this->select('loans.*')
                    ->where('loans.id', $loanId)
                    ->first();
    }

    /**
     * Check if customer owns this loan
     */
    public function isCustomerLoan($loanId, $customerId)
    {
        return $this->where('id', $loanId)
                    ->where('customer_id', $customerId)
                    ->first() !== null;
    }

    /**
     * Get loan documents
     */
    public function getLoanDocuments($loanId)
    {
        $loan = $this->find($loanId);
        if (!$loan || empty($loan['supporting_documents'])) {
            return [];
        }

        return explode(',', $loan['supporting_documents']);
    }

    /**
     * Calculate total amount due (principal + interest)
     */
    public function calculateTotalAmount($amount, $interestRate)
    {
        return $amount + ($amount * $interestRate / 100);
    }

    /**
     * Get loan statistics by customer
     */
    public function getCustomerLoanStats($customerId)
    {
        return [
            'total_loans' => $this->where('customer_id', $customerId)->countAllResults(),
            'pending_loans' => $this->where('customer_id', $customerId)->where('status', 'Pending')->countAllResults(),
            'approved_loans' => $this->where('customer_id', $customerId)->where('status', 'Approved')->countAllResults(),
            'released_loans' => $this->where('customer_id', $customerId)->where('status', 'Released')->countAllResults(),
            'rejected_loans' => $this->where('customer_id', $customerId)->where('status', 'Rejected')->countAllResults(),
        ];
    }

    /**
     * Search loans by customer name or email
     */
    public function searchLoans($searchTerm)
    {
        return $this->select('loans.*, customers.fullname as customer_name, customers.email as customer_email')
                    ->join('customers', 'customers.id = loans.customer_id', 'left')
                    ->groupStart()
                        ->like('customers.fullname', $searchTerm)
                        ->orLike('customers.email', $searchTerm)
                        ->orLike('loans.id', $searchTerm)
                    ->groupEnd()
                    ->orderBy('loans.created_at', 'DESC')
                    ->findAll();
    }
}