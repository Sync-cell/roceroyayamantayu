<?php

namespace App\Controllers;

use App\Models\AdminModel;

class AdminAuth extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        helper(['form']);
        $this->adminModel = new AdminModel();
    }

    public function createAdminForm()
    {
        return view('admin/create_admin');
    }

    public function storeAdmin()
    {
        $rules = [
            'fullname'         => 'required|min_length[3]|max_length[100]',
            'username'         => 'required|min_length[3]|max_length[50]|is_unique[admin.username]',
            'email'            => 'required|valid_email|is_unique[admin.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->adminModel->insert([
            'fullname'   => $this->request->getPost('fullname'),
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/login')->with('success', 'Admin account created. Please login.');
    }
}