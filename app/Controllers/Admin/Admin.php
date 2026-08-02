<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminBaseController;

class Admin extends AdminBaseController
{
    // Halaman Dashboard
    public function index()
    {
        // 🔒 CEK LOGIN DILAKUKAN DI SINI
        if ($this->data['adminId'] === 0) {
            return redirect()->to(base_url('admin/login'));
        }

        $data = $this->data;
        $usrid = session('usrid') ?? 0;
        
        $data['menu']       = 1;
        $data['mainsite']   = $this->func->mainsite_url();
        $data['user']       = $this->func->getUser($usrid);
        $data['jmlPesanan'] = $this->func->getJmlPesanan();
        $data['isLogin']    = $data['adminId'];
        $data['data']       = $this->func->getDashboardData();

        return view('admin/home', $data);
    }

    // Halaman Login (Aman dipanggil karena AdminBaseController sudah tidak memblokir)
    public function login()
    {
        if ($this->request->is('post')) {
            return redirect()->to('404_nf');
        }

        // Jika ternyata SUDAH login, lempar ke dashboard
        if ($this->data['adminId'] > 0) {
            return redirect()->to(base_url('admin'));
        }

        session()->destroy();

        $data = $this->data;

        return view('admin/login', $data);
    }

    // Process Auth Ajax Login
    public function auth()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses tidak diizinkan',
                'token'   => csrf_hash()
            ]);
        }

        $session  = session();
        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('pass');

        $admin = $this->func->getData('admin', $username, 'username');

        if ($admin && password_verify($password, $admin->password)) {
            $session->set([
                'isLoggedIn' => true,
                'usrid'      => $admin->id,
                'username'   => $admin->username,
            ]);

            return $this->response->setJSON([
                'success'  => true,
                'name'     => $admin->username,
                'redirect' => site_url('admin'),
                'token'    => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Username atau password salah!',
            'token'   => csrf_hash()
        ]);
    }

    public function showNotFound()
    {
        return view('errors/html/error_404');
    }
}