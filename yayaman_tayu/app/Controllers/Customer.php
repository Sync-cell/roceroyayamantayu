<?php


namespace App\Controllers;
use App\Models\KycModel;
use App\Models\CustomerModel;
use App\Models\LoanModel;
use CodeIgniter\Controller;

class Customer extends BaseController
{
    protected $customerModel;
    protected $loanModel;
 protected $kycModel;
    public function __construct()
    {
        helper(['url', 'form']);
        $this->customerModel = new CustomerModel();
        $this->loanModel = new LoanModel();
         $this->kycModel     = new KycModel();
    }

    protected function currentCustomerId()
    {
        return session()->get('customer_id') ?? null;
    }
   public function submitKyc()
    {
        $customerId = session()->get('customer_id');
        if (!$customerId) return redirect()->to('/login');

        $rules = [
            'id_type' => 'required',
            'account_holder_name' => 'required|min_length[3]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // handle uploads
        $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $customerId;
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

        $saved = [];
        $fileFields = ['id_front', 'id_back', 'selfie'];

        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $saved[$field] = $newName;
            }
        }

        // Prepare data (update if exists)
        $data = [
            'customer_id' => $customerId,
            'id_type' => $this->request->getPost('id_type'),
            'account_holder_name' => $this->request->getPost('account_holder_name'),
            'status' => 'Pending',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($saved['id_front']))  $data['id_front']  = $saved['id_front'];
        if (!empty($saved['id_back']))   $data['id_back']   = $saved['id_back'];
        if (!empty($saved['selfie']))    $data['selfie']    = $saved['selfie'];

        $existing = $this->kycModel->where('customer_id', $customerId)->first();
        if ($existing) {
            $this->kycModel->update($existing['id'], $data);
            $message = 'KYC updated and submitted for review.';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->kycModel->insert($data);
            $message = 'KYC submitted for review.';
        }

        return redirect()->to('/customer/dashboard')->with('success', $message);
    }
    
      public function kycForm()
    {
        $customerId = session()->get('customer_id');
        if (!$customerId) return redirect()->to('/login');

        $existing = $this->kycModel->where('customer_id', $customerId)->first();
        return view('customer/kyc_form', [
            'title' => 'KYC Verification',
            'kyc'   => $existing
        ]);
    }
   public function dashboard()
{
    $customerId = $this->currentCustomerId();
    if (!$customerId) {
        return redirect()->to('/login');
    }

    $customer = $this->customerModel->find($customerId);
    if (!$customer) {
        return redirect()->to('/login');
    }

    $db = \Config\Database::connect();
    $totalLoans = $db->table('loans')->where('customer_id', $customerId)->countAllResults();
    $totalBorrowed = $db->table('loans')->where('customer_id', $customerId)->selectSum('amount')->get()->getRowArray()['amount'] ?? 0;
    $pendingLoans = $db->table('loans')->where('customer_id', $customerId)->where('status', 'pending')->countAllResults();

    // Changed: use model first() or builder->get()->getRowArray()
    if (isset($this->kycModel) && method_exists($this->kycModel, 'where')) {
        $kyc = $this->kycModel->where('customer_id', $customerId)->first();
    } else {
        $kyc = $db->table('kyc')->where('customer_id', $customerId)->get()->getRowArray();
    }
    $kycStatus = $kyc ? ($kyc['status'] ?? 'pending') : 'pending';

    return view('customer/dashboard', [
        'title' => 'Dashboard',
        'customer' => $customer,
        'kyc_status' => $kycStatus,
        'total_loans' => $totalLoans,
        'total_borrowed' => $totalBorrowed,
        'pending_loans' => $pendingLoans
    ]);
}
    public function storeLoan()
    {
        $customerId = $this->currentCustomerId();
        if (!$customerId) {
            return redirect()->to('/login');
        }

        $customer = $this->customerModel->find($customerId);

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
            mkdir($uploadPath, 0755, true);
        }

        $documentFiles = [];
        $files = $this->request->getFiles('supporting_documents');

        if ($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $documentFiles[] = $newName;
                }
            }
        }

        // Calculate total amount
        $amount = $this->request->getPost('amount');
        $termMonths = $this->request->getPost('term_months');
        $baseRate = 5;
        $monthlyRate = 0.5;
        $totalRate = $baseRate + ($monthlyRate * $termMonths);
        $interest = ($amount * $totalRate) / 100;
        $finalAmount = $amount + $interest;

        $loanData = [
            'customer_id' => $customerId,
            'amount' => $amount,
            'purpose' => $this->request->getPost('purpose'),
            'term_months' => $termMonths,
            'payment_method' => $this->request->getPost('payment_method'),
            'disbursement_method' => $this->request->getPost('disbursement_method'),
            'account_holder_name' => $this->request->getPost('account_holder_name'),
            'bank_gcash_account' => $this->request->getPost('bank_gcash_account'),
            'interest_rate' => $totalRate,
            'final_amount' => $finalAmount,
            'supporting_documents' => json_encode($documentFiles),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->loanModel->insert($loanData);
        $loanId = $this->loanModel->getInsertID();

        return redirect()->to('/customer/loans')->with('success', 'Loan application submitted successfully! Loan ID: #' . $loanId);
    }

    public function myLoans()
    {
        $customerId = $this->currentCustomerId();
        if (!$customerId) {
            return redirect()->to('/login');
        }

        $loans = $this->loanModel->where('customer_id', $customerId)->orderBy('created_at', 'DESC')->findAll();

        return view('customer/loan/myloans', [
            'title' => 'My Loans',
            'loans' => $loans
        ]);
    }

    public function loanDetails($id = null)
    {
        $customerId = $this->currentCustomerId();
        if (!$customerId || !$id) {
            return redirect()->to('/customer/loans');
        }

        $loan = $this->loanModel->find($id);
        if (!$loan || $loan['customer_id'] != $customerId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('customer/loan/details', [
            'title' => 'Loan Details',
            'loan' => $loan
        ]);
    }
}