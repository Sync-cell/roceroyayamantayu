<?php

namespace App\Controllers;

use App\Models\CustomerModel;

class ProfileController extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'customer') {
            return redirect()->to('/login');
        }

        $customerId = $session->get('customer_id');
        $customer = $this->customerModel->find($customerId);

        if (!$customer) {
            return redirect()->to('/login')->with('error', 'Customer not found');
        }

        return view('customer/profile', ['customer' => $customer]);
    }

    public function update()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'customer') {
            return redirect()->to('/login');
        }

        $customerId = $session->get('customer_id');

        $rules = [
            'fullname'          => 'required|min_length[3]|max_length[100]',
            'email'             => 'required|valid_email|is_unique[customers.email,id,' . $customerId . ']',
            'contact_number'    => 'permit_empty|regex_match[/^[0-9\+\-\s()]+$/]',
            'birthdate'         => 'permit_empty|valid_date[Y-m-d]',
            'gender'            => 'permit_empty|in_list[male,female,other]',
            'civil_status'      => 'permit_empty|in_list[single,married,divorced,widowed]',
            'address_current'   => 'permit_empty',
            'address_permanent' => 'permit_empty',
            'profile_photo'     => 'permit_empty|uploaded[profile_photo]|max_size[profile_photo,2048]|mime_in[profile_photo,image/jpeg,image/png,image/gif]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'fullname'          => $this->request->getPost('fullname'),
            'email'             => $this->request->getPost('email'),
            'contact_number'    => $this->request->getPost('contact_number') ?? '',
            'birthdate'         => $this->request->getPost('birthdate') ?? null,
            'gender'            => $this->request->getPost('gender') ?? '',
            'civil_status'      => $this->request->getPost('civil_status') ?? '',
            'address_current'   => $this->request->getPost('address_current') ?? '',
            'address_permanent' => $this->request->getPost('address_permanent') ?? '',
        ];

        // Handle profile photo upload to writable folder
        $file = $this->request->getFile('profile_photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Use writable folder for file uploads
            $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profile';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['profile_photo'] = $newName;
        }

        $this->customerModel->update($customerId, $data);

        // Update session
        session()->set([
            'customer_name'    => $data['fullname'],
            'customer_email'   => $data['email'],
            'customer_contact' => $data['contact_number'],
        ]);

        return redirect()->to('/customer/profile')->with('success', 'Profile updated successfully!');
    }

    public function changePassword()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'customer') {
            return redirect()->to('/login');
        }

        $customerId = $session->get('customer_id');

        $rules = [
            'current_password' => 'required',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $customer = $this->customerModel->find($customerId);
        if (!$customer || !password_verify($this->request->getPost('current_password'), $customer['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        $this->customerModel->update($customerId, [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/customer/profile')->with('success', 'Password updated successfully!');
    }
}