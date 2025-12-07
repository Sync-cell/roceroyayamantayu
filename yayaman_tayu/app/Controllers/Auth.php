<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\AdminModel;

class Auth extends BaseController
{
    protected $customerModel;
    protected $adminModel;
    protected $db;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->customerModel = new CustomerModel();
        $this->adminModel = new AdminModel();
        $this->db = \Config\Database::connect();
    }

    public function register()
    {
        helper(['form']);
        return view('auth/register');
    }

    public function store()
    {
        helper(['form', 'url']);
        $session = session();
        $model = new CustomerModel();

        $rules = [
            'fullname' => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[customers.email]',
            'password' => 'required|min_length[6]',
            'contact_number' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profile';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = null;
        $img = $this->request->getFile('profile_photo');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $fileName = $img->getRandomName();
            $img->move($uploadPath, $fileName);
        }

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'birthdate' => $this->request->getPost('birthdate') ?? null,
            'gender' => $this->request->getPost('gender') ?? null,
            'civil_status' => $this->request->getPost('civil_status') ?? null,
            'address_current' => $this->request->getPost('address_current') ?? null,
            'address_permanent' => $this->request->getPost('address_permanent') ?? null,
            'contact_number' => $this->request->getPost('contact_number'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'profile_photo' => $fileName,
            'is_verified' => false,
            'balance' => 0,
        ];

        $model->insert($data);
        return redirect()->to('/login')->with('success', 'Account created successfully! Please login.');
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/customer/dashboard');
        }
        return view('auth/login');
    }

    public function doLogin()
    {
        $modelAdmin = new AdminModel();
        $modelCustomer = new CustomerModel();
        $session = session();

        $emailOrUser = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        // Try admin login first by username
        $admin = $modelAdmin->where('username', $emailOrUser)->first();

        if ($admin) {
            if (password_verify($password, $admin['password'])) {
                $session->set([
                    'isLoggedIn' => true,
                    'role' => 'admin',
                    'admin_id' => $admin['id'],
                    'admin_name' => $admin['fullname'],
                    'username' => $admin['username'],
                ]);
                return redirect()->to('/admin/dashboard')->with('success', 'Welcome Admin! 👋');
            }
        }

        // Try admin login by email
        $admin = $modelAdmin->where('email', $emailOrUser)->first();
        if ($admin) {
            if (password_verify($password, $admin['password'])) {
                $session->set([
                    'isLoggedIn' => true,
                    'role' => 'admin',
                    'admin_id' => $admin['id'],
                    'admin_name' => $admin['fullname'],
                    'username' => $admin['username'],
                ]);
                return redirect()->to('/admin/dashboard')->with('success', 'Welcome Admin! 👋');
            }
        }

        // Try customer login
        $cust = $modelCustomer->where('email', $emailOrUser)->first();
        if ($cust) {
            if (password_verify($password, $cust['password'])) {
                $session->set([
                    'isLoggedIn' => true,
                    'role' => 'customer',
                    'customer_id' => $cust['id'],
                    'customer_name' => $cust['fullname'],
                    'customer_email' => $cust['email'],
                ]);
                return redirect()->to('/customer/dashboard')->with('success', 'Welcome back! 👋');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Invalid username/email or password.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
  public function forgotPassword()
    {
        helper('form');
        return view('auth/forgot_password', ['title' => 'Forgot Password']);
    }

    /**
     * Handle submitted email. If email not found -> error.
     * If found -> create token and redirect to the reset form (simulates emailed link).
     */
    public function sendReset()
    {
        $email = trim($this->request->getPost('email'));
        if (empty($email)) {
            return redirect()->back()->with('error', 'Please provide your email')->withInput();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Invalid email address')->withInput();
        }

        // find user in customers or admins
        $user = $this->customerModel->where('email', $email)->first();
        $userType = 'customer';
        if (!$user) {
            $user = $this->adminModel->where('email', $email)->first();
            $userType = $user ? 'admin' : null;
        }

        if (!$user) {
            // user asked explicitly to show "email not exist"
            return redirect()->back()->with('error', 'Email does not exist in our records.')->withInput();
        }

        // create token and store
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        $this->db->table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiry,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // For now redirect to the reset form (simulates clicking email link)
        return redirect()->to('/reset-password/' . $token)
            ->with('success', 'A reset link has been generated. Use it to set a new password.');
    }

    /**
     * Show reset form given token.
     */
    public function resetPassword($token = null)
    {
        if (empty($token)) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid or missing token.');
        }

        // find token record and check expiry
        $row = $this->db->table('password_resets')->where('token', $token)->get()->getRowArray();
        if (!$row) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid reset token.');
        }

        if (strtotime($row['expires_at']) < time()) {
            // remove expired token
            $this->db->table('password_resets')->where('token', $token)->delete();
            return redirect()->to('/forgot-password')->with('error', 'Reset token has expired. Please request again.');
        }

        // pass email and token to view
        return view('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => $row['email']
        ]);
    }

    /**
     * Process new password submission.
     * Expects: token, password, password_confirm
     */
    public function submitReset()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $password_confirm = $this->request->getPost('password_confirm');

        if (empty($token)) {
            return redirect()->to('/forgot-password')->with('error', 'Missing token.');
        }

        // validate passwords
        $rules = [
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // find token
        $row = $this->db->table('password_resets')->where('token', $token)->get()->getRowArray();
        if (!$row) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid or used token.');
        }

        if (strtotime($row['expires_at']) < time()) {
            $this->db->table('password_resets')->where('token', $token)->delete();
            return redirect()->to('/forgot-password')->with('error', 'Reset token expired.');
        }

        $email = $row['email'];

        // find user (customer then admin)
        $user = $this->customerModel->where('email', $email)->first();
        $isAdmin = false;
        if (!$user) {
            $user = $this->adminModel->where('email', $email)->first();
            $isAdmin = $user ? true : false;
        }

        if (!$user) {
            // very unlikely because token record existed, but handle
            $this->db->table('password_resets')->where('token', $token)->delete();
            return redirect()->to('/forgot-password')->with('error', 'Account not found for that email.');
        }

        // update password
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        if ($isAdmin) {
            $this->adminModel->update($user['id'], ['password' => $hashed]);
        } else {
            $this->customerModel->update($user['id'], ['password' => $hashed]);
        }

        // remove token
        $this->db->table('password_resets')->where('token', $token)->delete();

        return redirect()->to('/login')->with('success', 'Password updated. You may now login.');
    }}