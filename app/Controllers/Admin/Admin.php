<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminBaseController;

class Admin extends AdminBaseController
{
    // Halaman Dashboard
    public function index()
    {
        // $this->func->cekSession();
        // 🔒 CEK LOGIN DILAKUKAN DI SINI
        if ($this->data['adminId'] === 0) {
            return redirect()->to(base_url('admin/login'));
        }

        $data = $this->data;
        
        
        $data['menu']       = 1;
        
        // $data['user']       = $this->func->getUser($usrid);
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
                'admin_id'   => $admin->id,
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

    public function pesanan()
    {
        if ($this->data['adminId'] === 0) {
            return redirect()->to(base_url('admin/login'));
        }

        $data         = $this->data;
        $data['menu'] = 2; 

        return view('admin/pesanan', $data);
    }

    public function logout()
    {
        $session = session();

        // Hapus HANYA session milik Admin agar session Client/User di tab lain tidak ikut terhapus
        $session->remove([
            'isLoggedIn',
            'admin_id',
            'username'
        ]);

        // Redirect ke halaman login admin
        return redirect()->to('admin/login');
    }

    public function slider()
    {
        // Cek Session login CI4
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('admin/logout'); // Sesuaikan dengan route/URL logout Anda
        }

        $page = (int) ($this->request->getGet('page') ?? 1);

        $data         = $this->data;
        $data['menu'] = 3;

        $data['data'] = $this->func->getPromoData($page);
        $data['pager'] = $this->func->pager;

        // Render View berturut-turut di CI4
        return view('admin/slider', $data);
    }

    public function sliderform($id = 0)
    {
        // 1. Cek Session Login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = $this->request;
        $data    = $this->data;

        // 2. Jika BUKAN Submit Form (Tampilkan View)
        if (!$request->getPost('tgl')) {
            $promo = null;
            if ($id != 0) {
                $promo = $this->func->getData('promo', (int)$id);
            }

            $data['menu'] = 3;
            $data['id']   = $id;
            $data['r']    = $promo;

            return view('admin/slider_form', $data);
        }

        // 3. Validasi File Gambar (Ekstensi, Tipe MIME, dan Ukuran)
        $file = $request->getFile('gambar');

        if ($file && $file->getName() !== '') {
            $validationRules = [
                'gambar' => [
                    'label'  => 'Gambar Promo',
                    'rules'  => 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/gif,image/webp]|ext_in[gambar,jpg,jpeg,png,gif,webp]|max_size[gambar,4096]',
                    'errors' => [
                        'uploaded' => 'Gagal mengunggah file gambar.',
                        'is_image' => 'File yang dipilih bukan berupa gambar.',
                        'mime_in'  => 'Format tipe file tidak didukung.',
                        'ext_in'   => 'Ekstensi file tidak diizinkan. Gunakan JPG, JPEG, PNG, GIF, atau WEBP.',
                        'max_size' => 'Ukuran file gambar terlalu besar (Maksimal 4 MB).'
                    ]
                ]
            ];

            if (!$this->validate($validationRules)) {
                return redirect()->back()->withInput()->with('error', $this->validator->getError('gambar'));
            }
        }

        $idPost = (int) $request->getPost('id');

        // Format datetime-local (ganti 'T' menjadi spasi untuk database)
        $tgl         = str_replace('T', ' ', $request->getPost('tgl'));
        $tgl_selesai = str_replace('T', ' ', $request->getPost('tgl_selesai'));

        // Persiapan Data Text (Struktur & nama field ASLI sesuai struktur kamu)
        $postData = [
            'admin_id'    => session()->get('admin_id'),
            'judul'       => $request->getPost('judul'),
            'sub_judul'   => $request->getPost('sub_judul'),
            'url'         => $request->getPost('link'),
            'status'      => $request->getPost('status'),
            'tgl'         => $tgl,
            'tgl_selesai' => $tgl_selesai,
        ];

        // Handle Upload & Pemindahan Gambar
        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext        = strtolower($file->getClientExtension());
            $fileName   = 'promo_upl' . date('YmdHis') . '.' . $ext;
            $uploadPath = FCPATH . 'cdn/promo/';

            // Buat folder jika belum ada di server
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $fileName);
        }

        // Aksi Insert / Update Data Database
        if ($idPost === 0) {
            // --- TAMBAH PROMO BARU ---
            if ($fileName) {
                $postData['gambar'] = $fileName;
            }

            $this->func->insertData('promo', $postData);
            session()->setFlashdata('success', 'Promo slider baru berhasil ditambahkan.');
        } else {
            // --- EDIT PROMO ---
            if ($fileName) {
                $postData['gambar'] = $fileName;
            }

            $this->func->updateData('promo', $postData, ['id' => $idPost]);
            session()->setFlashdata('success', 'Data promo slider berhasil diperbarui.');
        }

        return redirect()->to('admin/slider');
    }

    public function hapusslider()
    {
        // 1. Cek Session Login
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Akses ditolak, silakan login kembali.',
                'token'   => csrf_hash()
            ]);
        }

        $id = (int) $this->request->getPost('pro');

        if ($id > 0) {

            // Cari data promo berdasarkan ID
            $promo = $this->func->getData('promo', $id);

            if ($promo) {
                // Hapus file gambar dari folder public/cdn/promo/ jika ada
                if (!empty($promo->gambar)) {
                    $filePath = FCPATH . 'cdn/promo/' . $promo->gambar;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $this->func->deleteData('promo', $id);

                return $this->response->setJSON([
                    'success' => true,
                    'token'   => csrf_hash()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'Promo tidak ditemukan.',
            'token'   => csrf_hash()
        ]);
    }
}