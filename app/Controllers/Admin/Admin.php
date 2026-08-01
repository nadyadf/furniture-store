<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Admin extends BaseController
{
    public function index()
    {
        $data = $this->data;
        // Pengecekan status otentikasi session
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('admin/login');
        }
        
        $data['menu'] = 1;

        return view('admin/home', $data);
    }

    public function showNotFound()
    {
        return view('errors/html/error_404');
    }

    public function login()
    {
        if ($this->request->is('post')) {
            return redirect()->to('404_nf');
        }

        session()->destroy();

        $data = $this->data;

        return view('admin/login', $data);
    }

    
    public function auth()
    {
        // 1. Cek apakah request menggunakan method POST
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

        // 2. Ambil data admin
        $admin = $this->func->getData('admin', $username, 'username');

        if ($admin) {
            // ✅ 3. Verifikasi password murni dengan password_verify
            if (password_verify($password, $admin->password)) {

                // Set session admin
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
        }

        // ❌ Jika username tidak ditemukan atau password salah
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Username atau password salah!',
            'token'   => csrf_hash()
        ]);
    }
}