<?php


namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\CustomerModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Loan extends BaseController
{
    protected $loanModel;
    protected $customerModel;
    protected $notificationModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->loanModel = new LoanModel();
        $this->customerModel = new CustomerModel();
        $this->notificationModel = new NotificationModel();
    }

    protected function isCustomerLoggedIn()
    {
        return session()->get('customer_id') ?? null;
    }

    // Customer: Show apply loan form
    public function apply()
    {
        $customerId = $this->isCustomerLoggedIn();
        if (!$customerId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        return view('customer/loan/apply', [
            'title' => 'Apply for Loan'
        ]);
    }

    // Customer: Store loan application
    public function store()
    {
        $customerId = $this->isCustomerLoggedIn();
        if (!$customerId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $rules = [
            'amount' => 'required|numeric|greater_than[4999]|less_than[500001]',
            'purpose' => 'required|min_length[10]|max_length[500]',
            'term_months' => 'required|in_list[3,6,12,18,24]',
            'payment_method' => 'required|in_list[weekly,bi-weekly,monthly]',
            'disbursement_method' => 'required|in_list[bank_transfer,gcash]',
            'account_holder_name' => 'required|min_length[3]',
            'bank_gcash_account' => 'required|min_length[4]|max_length[50]',
            'agree_terms' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file uploads
        $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'loan_documents' . DIRECTORY_SEPARATOR . $customerId;
        if (!is_dir($uploadPath)) {
            @mkdir($uploadPath, 0755, true);
        }

        $documentFiles = [];
        $uploadedFiles = $this->request->getFileMultiple('supporting_documents');

        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    if ($file->move($uploadPath, $newName)) {
                        $documentFiles[] = $newName;
                    }
                }
            }
        }

        // Calculate interest and final amount
        $amount = (float) $this->request->getPost('amount');
        $termMonths = (int) $this->request->getPost('term_months');
        
        $baseRate = 5.00;
        $monthlyRate = 0.50;
        $totalRate = $baseRate + ($monthlyRate * $termMonths);
        $interest = ($amount * $totalRate) / 100;
        $finalAmount = $amount + $interest;

        // Prepare loan data
        $loanData = [
            'customer_id' => $customerId,
            'amount' => $amount,
            'purpose' => trim($this->request->getPost('purpose')),
            'term_months' => $termMonths,
            'payment_method' => $this->request->getPost('payment_method'),
            'disbursement_method' => $this->request->getPost('disbursement_method'),
            'account_holder_name' => trim($this->request->getPost('account_holder_name')),
            'bank_gcash_account' => trim($this->request->getPost('bank_gcash_account')),
            'interest_rate' => $totalRate,
            'final_amount' => $finalAmount,
            'supporting_documents' => !empty($documentFiles) ? implode(',', $documentFiles) : null,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert loan
        if ($this->loanModel->insert($loanData)) {
            $loanId = $this->loanModel->getInsertID();
            
            // Get customer info
            $customer = $this->customerModel->find($customerId);
            
            // Send notification to all active admins
            $this->sendAdminNotifications(
                'New Loan Application',
                'A new loan application has been submitted by ' . ($customer['fullname'] ?? 'a customer') . ' for ₱' . number_format($amount, 2),
                'loan_request',
                (string) $loanId,
                ['customer_id' => $customerId, 'loan_id' => $loanId, 'amount' => $amount]
            );
            
            return redirect()->to('/customer/loans')->with('success', 'Loan application submitted successfully! Application ID: #' . $loanId);
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to submit loan application. Please try again.');
        }
    }

    // Customer: View their loans
    public function myLoans()
    {
        $customerId = $this->isCustomerLoggedIn();
        if (!$customerId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $loans = $this->loanModel
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('customer/loan/myloans', [
            'title' => 'My Loans',
            'loans' => $loans
        ]);
    }

    // Customer: View loan details
    public function details($id)
    {
        $customerId = $this->isCustomerLoggedIn();
        if (!$customerId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $loan = $this->loanModel->find($id);

        if (!$loan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($loan['customer_id'] != $customerId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('customer/loan/details', [
            'title' => 'Loan Details',
            'loan' => $loan
        ]);
    }

    // ============ ADMIN METHODS ============

    // Admin: View all loans
    public function adminList()
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $status = $this->request->getGet('status') ?? 'Pending';
        
        $loans = $this->loanModel
            ->select('loans.*, customers.fullname, customers.email, customers.contact_number')
            ->join('customers', 'customers.id = loans.customer_id', 'left')
            ->where('loans.status', $status)
            ->orderBy('loans.created_at', 'DESC')
            ->findAll();

        return view('admin/loan/list', [
            'title' => 'Loan Applications',
            'loans' => $loans,
            'status' => $status
        ]);
    }

    // Admin: Review loan
    public function adminReview($id)
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $loan = $this->loanModel
            ->select('loans.*, customers.fullname, customers.email, customers.contact_number')
            ->join('customers', 'customers.id = loans.customer_id', 'left')
            ->where('loans.id', $id)
            ->first();

        if (!$loan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/loan/review', [
            'title' => 'Review Loan Application',
            'loan' => $loan
        ]);
    }

    // Admin: Process loan (approve/reject)
    public function adminProcess($id)
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $action = $this->request->getPost('action');
        $interestRate = (float) $this->request->getPost('interest_rate') ?? 5.00;
        $penaltyRate = (float) $this->request->getPost('penalty_rate') ?? 2.00;
        $adminNotes = $this->request->getPost('admin_notes');

        $loan = $this->loanModel->find($id);
        if (!$loan) {
            return redirect()->back()->with('error', 'Loan not found');
        }

        $customer = $this->customerModel->find($loan['customer_id']);

        if ($action === 'approve') {
            $finalAmount = $loan['amount'] + ($loan['amount'] * $interestRate / 100);

            $updateData = [
                'status' => 'Approved',
                'interest_rate' => $interestRate,
                'penalty_rate' => $penaltyRate,
                'final_amount' => $finalAmount,
                'admin_notes' => $adminNotes,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->loanModel->update($id, $updateData);
            
            // Send notification to customer
            $this->notificationModel->createCustomerNotification(
                $loan['customer_id'],
                'Loan Approved',
                'Great news! Your loan application for ₱' . number_format($loan['amount'], 2) . ' has been approved.',
                'loan_approved',
                (string) $id,
                ['loan_id' => $id, 'amount' => $loan['amount'], 'interest_rate' => $interestRate]
            );
            
            return redirect()->to('/admin/loans?status=Approved')->with('success', 'Loan approved successfully! Customer notified.');
        } elseif ($action === 'reject') {
            $updateData = [
                'status' => 'Rejected',
                'admin_notes' => $adminNotes,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->loanModel->update($id, $updateData);
            
            // Send notification to customer
            $reason = !empty($adminNotes) ? 'Reason: ' . $adminNotes : 'Please contact support for more information.';
            $this->notificationModel->createCustomerNotification(
                $loan['customer_id'],
                'Loan Application Rejected',
                'Your loan application for ₱' . number_format($loan['amount'], 2) . ' has been rejected. ' . $reason,
                'loan_rejected',
                (string) $id,
                ['loan_id' => $id, 'reason' => $adminNotes]
            );
            
            return redirect()->to('/admin/loans?status=Rejected')->with('success', 'Loan rejected successfully! Customer notified.');
        }

        return redirect()->back()->with('error', 'Invalid action');
    }

    // Admin: Release loan (disburse funds)
    public function adminRelease($id)
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $loan = $this->loanModel->find($id);
        if (!$loan) {
            return redirect()->back()->with('error', 'Loan not found');
        }

        if ($loan['status'] !== 'Approved') {
            return redirect()->back()->with('error', 'Only approved loans can be released');
        }

        $releaseDate = date('Y-m-d');
        $nextPaymentDate = date('Y-m-d', strtotime("+{$loan['term_months']} months"));

        $updateData = [
            'status' => 'Released',
            'release_date' => $releaseDate,
            'released_amount' => $loan['final_amount'],
            'next_payment_date' => $nextPaymentDate,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->loanModel->update($id, $updateData);
        
        // Send notification to customer
        $this->notificationModel->createCustomerNotification(
            $loan['customer_id'],
            'Loan Released',
            'Your loan of ₱' . number_format($loan['amount'], 2) . ' has been released. The total amount to be repaid is ₱' . number_format($loan['final_amount'], 2) . ' with first payment due on ' . date('M d, Y', strtotime($nextPaymentDate)),
            'loan_released',
            (string) $id,
            ['loan_id' => $id, 'amount' => $loan['amount'], 'final_amount' => $loan['final_amount'], 'next_payment_date' => $nextPaymentDate]
        );
        
        return redirect()->to('/admin/loans?status=Released')->with('success', 'Loan released successfully! Customer notified.');
    }

    // ============ HELPER METHODS ============
    
    /**
     * Send notification to all active admins
     */
    protected function sendAdminNotifications($title, $message, $type = null, $relatedId = null, $meta = null)
    {
        try {
            $adminModel = new \App\Models\AdminModel();
            $admins = $adminModel->where('is_active', 1)->findAll();

            foreach ($admins as $admin) {
                $this->notificationModel->createAdminNotification(
                    $admin['id'],
                    $title,
                    $message,
                    $type,
                    $relatedId,
                    $meta
                );
            }

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send admin notifications: ' . $e->getMessage());
            return false;
        }
    }
}