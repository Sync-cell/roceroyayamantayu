<?php


namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\KycModel;
use App\Models\CustomerModel;
use App\Models\NotificationModel;
use App\Models\SettingModel;

class Admin extends BaseController
{
    protected $loanModel;
    protected $kycModel;
    protected $customerModel;
    protected $notificationModel;
    protected $settingModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->loanModel         = new LoanModel();
        $this->kycModel          = new KycModel();
        $this->customerModel     = new CustomerModel();
        $this->notificationModel = new NotificationModel();
        $this->settingModel      = new SettingModel();
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $loanStatusFilter = $this->request->getGet('loan_status');
        $kycStatusFilter  = $this->request->getGet('kyc_status');

        // Loan Query
        $loanQuery = $this->loanModel->orderBy('created_at', 'DESC');
        if (!empty($loanStatusFilter) && strtolower($loanStatusFilter) !== 'all') {
            $loanQuery->where('status', $loanStatusFilter);
        }

        $recentLoans = $loanQuery->paginate(10);
        $loanPager   = $this->loanModel->pager;

        if (!empty($loanStatusFilter)) {
            $loanPager->setPath(current_url(true)->getPath() . '?loan_status=' . urlencode($loanStatusFilter));
        }

        // KYC Query
        $kycQuery = $this->kycModel->orderBy('created_at', 'DESC');
        if (!empty($kycStatusFilter) && strtolower($kycStatusFilter) !== 'all') {
            $kycQuery->where('status', $kycStatusFilter);
        } else {
            if (empty($kycStatusFilter)) {
                $kycQuery->where('status', 'Pending');
            }
        }

        $recentKyc = $kycQuery->paginate(10);
        $kycPager  = $this->kycModel->pager;

        if (!empty($kycStatusFilter)) {
            $kycPager->setPath(current_url(true)->getPath() . '?kyc_status=' . urlencode($kycStatusFilter));
        }

        // Get maintenance mode status
        $maintenanceMode = $this->settingModel->where('key', 'maintenance_mode')->first();
        $isMaintenanceOn = $maintenanceMode && $maintenanceMode['value'] === '1';

        $data = [
            'pendingLoans'      => $this->loanModel->where('status', 'Pending')->countAllResults(),
            'approvedLoans'     => $this->loanModel->where('status', 'Approved')->countAllResults(),
            'releasedLoans'     => $this->loanModel->where('status', 'Released')->countAllResults(),
            'pendingKyc'        => $this->kycModel->where('status', 'Pending')->countAllResults(),
            'totalCustomers'    => $this->customerModel->countAllResults(),
            'totalLoans'        => $this->loanModel->countAllResults(),
            'releasedLoanAmount'=> $this->loanModel->selectSum('amount')->where('status', 'Released')->first()['amount'] ?? 0,
            'recentLoans'       => $recentLoans,
            'loanPager'         => $loanPager,
            'loanStatusFilter'  => $loanStatusFilter,
            'recentKyc'         => $recentKyc,
            'kycPager'          => $kycPager,
            'kycStatusFilter'   => $kycStatusFilter,
            'maintenanceOn'     => $isMaintenanceOn,
            'title'             => 'Dashboard',
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Toggle Maintenance Mode
     * Ensures the admin's current IP is added to whitelist when enabling
     */
    public function toggleMaintenance()
    {
        $session = session();
        $adminId = $session->get('admin_id');
        if (! $adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $modeRec = $this->settingModel->where('key', 'maintenance_mode')->first();
        $isOn = $modeRec && $modeRec['value'] === '1';
        $newValue = $isOn ? '0' : '1';

        $this->saveSetting('maintenance_mode', $newValue);

        // If we just enabled maintenance, ensure current admin IP is whitelisted
        if ($newValue === '1') {
            $currentIp = $this->request->getIPAddress();
            $wlRec = $this->settingModel->where('key', 'maintenance_whitelist')->first();
            $whitelist = [];
            if ($wlRec && ! empty($wlRec['value'])) {
                $decoded = json_decode($wlRec['value'], true);
                if (is_array($decoded)) $whitelist = $decoded;
            }
            if (! in_array($currentIp, $whitelist, true)) {
                $whitelist[] = $currentIp;
                $this->saveSetting('maintenance_whitelist', json_encode(array_values(array_unique($whitelist))));
            }
        }

        return redirect()->back()->with('success', 'Maintenance mode ' . ($newValue === '1' ? 'enabled' : 'disabled') . ' successfully!');
    }

    /**
     * Show maintenance settings page
     */
    public function maintenanceSettings()
    {
        $adminId = session()->get('admin_id');
        if (! $adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $modeRec = $this->settingModel->where('key', 'maintenance_mode')->first();
        $wlRec   = $this->settingModel->where('key', 'maintenance_whitelist')->first();

        $isOn = $modeRec && $modeRec['value'] === '1';
        $whitelist = [];
        if ($wlRec && ! empty($wlRec['value'])) {
            $decoded = json_decode($wlRec['value'], true);
            if (is_array($decoded)) $whitelist = $decoded;
        }

        return view('admin/maintenance_settings', [
            'title' => 'Maintenance Settings',
            'isMaintenanceOn' => $isOn,
            'whitelist' => $whitelist
        ]);
    }

    /**
     * Handle whitelist add/remove via POST
     */
    public function maintenanceSettingsSave()
    {
        $adminId = session()->get('admin_id');
        if (! $adminId) {
            return redirect()->to('/login')->with('error', 'Admin access only');
        }

        $action = $this->request->getPost('action');

        $wlRec = $this->settingModel->where('key', 'maintenance_whitelist')->first();
        $whitelist = [];
        if ($wlRec && ! empty($wlRec['value'])) {
            $decoded = json_decode($wlRec['value'], true);
            if (is_array($decoded)) $whitelist = $decoded;
        }

        if ($action === 'add') {
            $newIp = trim($this->request->getPost('new_ip'));
            if (filter_var($newIp, FILTER_VALIDATE_IP) === false) {
                return redirect()->back()->with('error', 'Invalid IP address');
            }
            if (! in_array($newIp, $whitelist, true)) {
                $whitelist[] = $newIp;
                $this->saveSetting('maintenance_whitelist', json_encode(array_values(array_unique($whitelist))));
            }
            return redirect()->back()->with('success', 'IP added to whitelist');
        }

        if ($action === 'remove') {
            $ip = $this->request->getPost('ip');
            $whitelist = array_values(array_filter($whitelist, function($v) use ($ip) { return $v !== $ip; }));
            $this->saveSetting('maintenance_whitelist', json_encode($whitelist));
            return redirect()->back()->with('success', 'IP removed from whitelist');
        }

        return redirect()->back();
    }

    /**
     * List loans (admin)
     */
    public function loans()
    {
        $db = \Config\Database::connect();

        $loans = $db->table('loans')
            ->select('loans.*, customers.fullname as customer_name, customers.email as customer_email, kyc.status as kyc_status')
            ->join('customers', 'customers.id = loans.customer_id', 'left')
            ->join('kyc', 'kyc.customer_id = loans.customer_id', 'left')
            ->orderBy('loans.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/loan_list', [
            'loans' => $loans,
            'title' => 'Loans'
        ]);
    }

    /**
     * Review Loan
     */
    public function reviewLoan($id = null)
    {
        $loan = $this->loanModel->find($id);
        if (!$loan) {
            return redirect()->to('/admin/loans')->with('error', 'Loan not found');
        }

        $customer = $this->customerModel->find($loan['customer_id']);
        $kyc = $this->kycModel->where('customer_id', $loan['customer_id'])->first();

        return view('admin/review_loan', [
            'loan' => $loan,
            'customer' => $customer,
            'kyc' => $kyc,
            'title' => 'Review Loan',
        ]);
    }

    /**
     * Process Loan (Approve/Reject)
     */
    public function processLoan($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $loan = $this->loanModel->find($id);
        if (!$loan) {
            return redirect()->back()->with('error', 'Loan not found');
        }

        $action = $this->request->getPost('action');
        $interestRate = $this->request->getPost('interest_rate') ?? 5;
        $penaltyRate = $this->request->getPost('penalty_rate') ?? 2;
        $adminNotes = $this->request->getPost('admin_notes');

        if ($action === 'approve') {
            $finalAmount = $loan['amount'] + ($loan['amount'] * $interestRate / 100);

            $this->loanModel->update($id, [
                'status' => 'Approved',
                'interest_rate' => $interestRate,
                'penalty_rate' => $penaltyRate,
                'final_amount' => $finalAmount,
                'admin_notes' => $adminNotes,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification
            $this->notificationModel->insert([
                'customer_id' => $loan['customer_id'],
                'type' => 'loan_approved',
                'title' => 'Loan Approved! ✓',
                'message' => 'Your loan application of ₱' . number_format($loan['amount'], 2) . ' has been approved with ' . $interestRate . '% interest.',
                'related_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin')->with('success', 'Loan approved successfully!');
        } elseif ($action === 'reject') {
            $this->loanModel->update($id, [
                'status' => 'Rejected',
                'admin_notes' => $adminNotes,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification
            $this->notificationModel->insert([
                'customer_id' => $loan['customer_id'],
                'type' => 'loan_rejected',
                'title' => 'Loan Rejected',
                'message' => 'Your loan application has been rejected. Please contact support for more information.',
                'related_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin')->with('success', 'Loan rejected successfully!');
        }

        return redirect()->back()->with('error', 'Invalid action');
    }

    /**
     * Release Loan
     */
    public function releaseLoan($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $loan = $this->loanModel->find($id);
        if (!$loan || $loan['status'] !== 'Approved') {
            return redirect()->back()->with('error', 'Loan cannot be released');
        }

        $releaseDate = date('Y-m-d');
        $nextPaymentDate = date('Y-m-d', strtotime("+30 days", strtotime($releaseDate)));

        $this->loanModel->update($id, [
            'status' => 'Released',
            'release_date' => $releaseDate,
            'released_amount' => $loan['final_amount'],
            'next_payment_date' => $nextPaymentDate,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create notification
        $this->notificationModel->insert([
            'customer_id' => $loan['customer_id'],
            'type' => 'loan_released',
            'title' => 'Loan Disbursed! 💰',
            'message' => 'Your loan of ₱' . number_format($loan['final_amount'], 2) . ' has been released. First payment due on ' . date('M d, Y', strtotime($nextPaymentDate)),
            'related_id' => $id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin')->with('success', 'Loan released successfully!');
    }

    /**
     * KYC List
     */
    public function kycList()
    {
        $kycs = $this->kycModel->orderBy('created_at', 'DESC')->paginate(10);
        $pager = $this->kycModel->pager;

        return view('admin/kyc_list', [
            'kycs' => $kycs,
            'pager' => $pager,
            'title' => 'KYC Verification',
        ]);
    }

    /**
     * Review KYC
     */
    public function reviewKyc($id)
    {
        $kyc = $this->kycModel->find($id);
        if (!$kyc) {
            return redirect()->to('/admin')->with('error', 'KYC record not found.');
        }

        $customer = $this->customerModel->find($kyc['customer_id']);

        return view('admin/review_kyc', [
            'kyc' => $kyc,
            'customer' => $customer,
            'title' => 'Review KYC',
        ]);
    }

    /**
     * Process KYC (Approve/Reject)
     */
    public function processKyc($id)
    {
        $kyc = $this->kycModel->find($id);
        if (!$kyc) {
            return redirect()->to('/admin')->with('error', 'KYC not found.');
        }

        $session = session();
        $action = $this->request->getPost('action');
        $notes = $this->request->getPost('notes');

        if ($action === 'approve') {
            $this->kycModel->update($id, [
                'status' => 'Approved',
                'notes' => $notes,
                'reviewed_by' => $session->get('admin_id') ?? null,
                'reviewed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->customerModel->update($kyc['customer_id'], [
                'is_verified' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification
            $this->notificationModel->insert([
                'customer_id' => $kyc['customer_id'],
                'type' => 'kyc_approved',
                'title' => 'KYC Verified! ✓',
                'message' => 'Your KYC has been approved. You can now apply for loans.',
                'related_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin')->with('success', 'KYC approved and customer verified.');
        }

        if ($action === 'reject') {
            $this->kycModel->update($id, [
                'status' => 'Rejected',
                'notes' => $notes,
                'reviewed_by' => $session->get('admin_id') ?? null,
                'reviewed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->customerModel->update($kyc['customer_id'], [
                'is_verified' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create notification
            $this->notificationModel->insert([
                'customer_id' => $kyc['customer_id'],
                'type' => 'kyc_rejected',
                'title' => 'KYC Rejected',
                'message' => 'Your KYC has been rejected. Please contact support or resubmit your documents.',
                'related_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin')->with('success', 'KYC rejected.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    /**
     * Request KYC from Customer
     */
    public function requestKyc($customerId = null)
    {
        $customerId = (int)$customerId;
        if (!$customerId) {
            return redirect()->back()->with('error', 'Invalid customer id.');
        }

        $customer = $this->customerModel->find($customerId);
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        $loanId = (int)$this->request->getGet('loan');

        // Create notification
        $this->notificationModel->insert([
            'customer_id' => $customerId,
            'type' => 'kyc_request',
            'title' => 'Please Complete Your KYC',
            'message' => 'Please submit your KYC documents so we can continue processing your loan.',
            'related_id' => $loanId ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'KYC reminder sent to customer.');
    }

    /**
     * Customer List
     */
    public function customerList()
    {
        $customers = $this->customerModel->orderBy('created_at', 'DESC')->paginate(10);
        $pager = $this->customerModel->pager;

        return view('admin/customer_list', [
            'customers' => $customers,
            'pager' => $pager,
            'title' => 'Customers',
        ]);
    }

    /**
     * Reports Page
     */
    public function reports()
    {
        $db = \Config\Database::connect();

        // Get filter status from query string
        $loanStatus = $this->request->getGet('loan_status') ?? 'all';
        $kycStatus  = $this->request->getGet('kyc_status') ?? 'all';

        // Build loan query with all details
        $loanQuery = $db->table('loans')
            ->select('loans.id, loans.customer_id, loans.amount, loans.term, loans.interest_rate, loans.status, loans.created_at, customers.fullname as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = loans.customer_id', 'left');

        if ($loanStatus !== 'all') {
            $loanQuery->where('loans.status', $loanStatus);
        }

        $loans = $loanQuery->orderBy('loans.created_at', 'DESC')
                           ->get()
                           ->getResultArray();

        // Build KYC query
        $kycQuery = $db->table('kyc')
            ->select('kyc.*')
            ->join('customers', 'customers.id = kyc.customer_id', 'left');

        if ($kycStatus !== 'all') {
            $kycQuery->where('kyc.status', $kycStatus);
        }

        $kycs = $kycQuery->orderBy('kyc.created_at', 'DESC')
                         ->get()
                         ->getResultArray();

        // Calculate totals
        $totalLoans    = count($loans);
        $totalAmount   = array_sum(array_column($loans, 'amount'));
        $releasedAmount = array_sum(array_filter(array_map(function($loan) {
            return strtolower($loan['status'] ?? '') === 'released' ? $loan['amount'] : 0;
        }, $loans)));

        $customerModel = new \App\Models\CustomerModel();
        $totalCustomers = $customerModel->countAll();

        return view('admin/reports', [
            'totalLoans' => $totalLoans,
            'totalAmount' => $totalAmount,
            'releasedAmount' => $releasedAmount,
            'totalCustomers' => $totalCustomers,
            'loans' => $loans,
            'kycs' => $kycs,
            'loanStatus' => $loanStatus,
            'kycStatus' => $kycStatus,
            'title' => 'Reports',
        ]);
    }

    public function settings()
    {
        if ($this->request->getMethod() === 'post') {
            $appName = $this->request->getPost('app_name');
            $supportEmail = $this->request->getPost('support_email');
            $supportPhone = $this->request->getPost('support_phone');
            $description = $this->request->getPost('description');

            $this->saveSetting('app_name', $appName);
            $this->saveSetting('support_email', $supportEmail);
            $this->saveSetting('support_phone', $supportPhone);
            $this->saveSetting('app_description', $description);

            return redirect()->back()->with('success', 'Settings updated successfully!');
        }

        $appName = $this->settingModel->where('key', 'app_name')->first();
        $supportEmail = $this->settingModel->where('key', 'support_email')->first();
        $supportPhone = $this->settingModel->where('key', 'support_phone')->first();
        $appDescription = $this->settingModel->where('key', 'app_description')->first();

        $data = [
            'appName' => $appName['value'] ?? 'Yayaman Tayu',
            'supportEmail' => $supportEmail['value'] ?? '',
            'supportPhone' => $supportPhone['value'] ?? '',
            'appDescription' => $appDescription['value'] ?? '',
            'title' => 'Settings',
        ];

        return view('admin/settings', $data);
    }

    /**
     * Admin Profile Page
     */
     public function profile()
    {
        $session = session();
        $adminId = $session->get('admin_id');

        // Handle POST (save)
        if ($this->request->getMethod() === 'post') {
            $fullname = $this->request->getPost('fullname');
            $email    = $this->request->getPost('email');
            $phone    = $this->request->getPost('phone');
            $password = $this->request->getPost('password');

            // Basic validation
            if (empty($fullname) || empty($email)) {
                return redirect()->back()->with('error', 'Name and email are required.');
            }

            // Try to update AdminModel if available
            try {
                $adminModelClass = '\\App\\Models\\AdminModel';
                if ($adminId && class_exists($adminModelClass)) {
                    $adminModel = new $adminModelClass();
                    $update = [
                        'name'       => $fullname,
                        'email'      => $email,
                        'phone'      => $phone,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    if (!empty($password)) {
                        $update['password'] = password_hash($password, PASSWORD_DEFAULT);
                    }
                    $adminModel->update($adminId, $update);

                    // Refresh session values (keep other session keys)
                    $session->set('admin_name', $fullname);
                    $session->set('admin_email', $email);
                    $session->set('admin_phone', $phone);
                } else {
                    // No model — update session only
                    $session->set('admin_name', $fullname);
                    $session->set('admin_email', $email);
                    $session->set('admin_phone', $phone);
                }

                return redirect()->back()->with('success', 'Profile updated successfully!');
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Unable to update profile.');
            }
        }

        // GET: load data to show in form
        $fullname = $session->get('admin_name') ?? $session->get('fullname') ?? '';
        $email    = $session->get('admin_email') ?? $session->get('email') ?? '';
        $phone    = $session->get('admin_phone') ?? $session->get('phone') ?? '';

        // Try to load fresh record from AdminModel if exists
        try {
            $adminModelClass = '\\App\\Models\\AdminModel';
            if ($adminId && class_exists($adminModelClass)) {
                $adminModel = new $adminModelClass();
                $admin = $adminModel->find($adminId);
                if ($admin) {
                    $fullname = $admin['name'] ?? $fullname;
                    $email    = $admin['email'] ?? $email;
                    $phone    = $admin['phone'] ?? $phone;
                    // if profile photo is stored in admin record, push to session for layout
                    if (!empty($admin['photo'])) {
                        $session->set('profile_photo', $admin['photo']);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return view('admin/profile', [
            'title'    => 'Profile',
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
        ]);
    }
    protected function saveSetting(string $key, $value)
    {
        $rec = $this->settingModel->where('key', $key)->first();
        $payload = [
            'key' => $key,
            'value' => (string)$value,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($rec) {
            // update
            $this->settingModel->update($rec['id'], $payload);
        } else {
            $payload['created_at'] = date('Y-m-d H:i:s');
            $this->settingModel->insert($payload);
        }
    }
}
