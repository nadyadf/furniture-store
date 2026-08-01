<?php

namespace App\Controllers\Client;
use App\Controllers\BaseController;

class Home extends BaseController {
    public function index() {
        $data = $this->data;

        $jmlProdukPerKategori = $this->func->getJumlahProdukPerKategori();
        $data['jmlProdukPerKategori'] = [];
        foreach ($jmlProdukPerKategori as $item) {
            $data['jmlProdukPerKategori'][$item->id] = $item->jumlah_produk;
        }

        // $data['cari'] = esc($this->request->getGet('cari') ?? '', 'url');
        $data['promo'] = $this->func->getPromoAktif();
        $data['jmlProdukPerKategori'] = $data['jmlProdukPerKategori'] ?? [];
        $data['produkUnggulan'] = $this->func->getProdukUnggulan();
        $data['produkTerbaru'] = $this->func->getProdukTerbaru();

        return view('client/home', $data);
    }

    public function signin($pwreset = 'none')
    {

        $session = session();
        $db      = \Config\Database::connect();

        $url = $session->get('url') ?? site_url();

        $email = $this->request->getPost('email');
        $passInput = $this->request->getPost('pass');

        if ($email && $pwreset === "none") {

            // query builder CI4
            $builder = $db->table('user_data');
            $builder->groupStart()
                        ->where('username', $email)
                        ->orWhere('no_hp', $email)
                     ->groupEnd()
                     ->limit(1);

            $user = $builder->get()->getRow();

            // ❌ user tidak ditemukan
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'token'   => csrf_hash()
                ]);
            }

            $pass  = $user->password;
        
            // ✅ password cocok
            if (password_verify($this->request->getPost('pass'), $pass)) {

                $session->set([
                    'usrid' => $user->id,
                ]);

                return $this->response->setJSON([
                    'success'  => true,
                    'redirect' => $url,
                    'token'    => csrf_hash()
                ]);
            }

            // ❌ password salah
            return $this->response->setJSON([
                'success' => false,
                'redirect'=> $url,
                'msg'     => $email . " - " . $pass,
                'token'   => csrf_hash()
            ]);
        }
    

        $set = $this->func->globalset('semua');
        
        $data = [
            'set'         => $set,
            'nama'        => $set->nama . ' – ' . $set->slogan,
            'title'       => 'Masuk',
            'google_url'  => '#',
            'desc'      => 'Web toko furnitur ' . $set->nama,
            'img' => base_url('cdn/assets/img/' . $set->favicon),
            'url' => site_url(),
        ];

        return view('auth/signin', $data);
    }

    public function signup($pwreset = 'none')
    {
        $db      = \Config\Database::connect();
        $session = session();
        $request = $this->request;

        /* ===============================
        KIRIM ULANG VERIFIKASI
        =================================*/
        if ($request->getPost('id') && $pwreset == "kirimulang") {

            $id = service('func')->decode($request->getPost('id'));

            if (service('func')->verifEmail($id)) {
                service('func')->verifWA($id);

                return $this->response->setJSON([
                    'success'=>true,
                    'message'=>'',
                    'token'=>csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success'=>false,
                'message'=>'alamat email sudah terdaftar',
                'token'=>csrf_hash()
            ]);
        }

        /* ===============================
        CEK EMAIL AJAX
        =================================*/
        elseif ($request->getPost('email') && $pwreset == "cekemail") {

            $builder = $db->table('user_data');
            $user = $builder->groupStart()
                            ->where('username',$request->getPost('email'))
                            ->orWhere('no_hp',$request->getPost('email'))
                            ->groupEnd()
                            ->get()
                            ->getRow();

            return $this->response->setJSON([
                'success'=> $user ? false : true,
                'message'=> $user ? 'alamat email/no handphone sudah terdaftar' : '',
                'token'=> csrf_hash()
            ]);
        } 

        /* ===============================
        PROSES DAFTAR
        =================================*/
        elseif ($request->getPost('email') && $pwreset == "none") {

            $email = $request->getPost('email');

            // ambil password asli dari form
            $passwordHash = password_hash(
                $request->getPost('pass'),
                PASSWORD_DEFAULT
            );

            $usd = $db->table('user_data')
                    ->where('username',$email)
                    ->get()
                    ->getRow();

            if (!$usd) {

                // $upline = $session->get('aff') ?? 0;

                $db->table('user_data')->insert([
                    'username' => $email,
                    'nama'     => $request->getPost('nama'),
                    'no_hp'     => $request->getPost('nohp'),
                    'password' => $passwordHash
                ]);

                $usrid = $db->insertID();

                $db->table('profil')->insert([
                    'usrid'   => $usrid,
                    'no_hp'    => $request->getPost('nohp'),
                    'nama'    => $request->getPost('nama'),
                    'foto'    => 'user.png'
                ]);

                // service('func')->verifEmail($usrid);
                // service('func')->verifWA($usrid);

                $result = view('client/selesai_daftar', [
                    'email'=>$email,
                    'nowa'=>$request->getPost('nohp')
                ]);

                return $this->response->setJSON([
                    'success'=>true,
                    'result'=>$result,
                    'token'=>csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success'=>false,
                'result'=>'Email sudah terdaftar',
                'token'=>csrf_hash()
            ]);
        }

        /* ===============================
        HALAMAN SIGNUP
        =================================*/
        else {

            $set = $this->func->globalset('semua');
            
            $data = [
                'set'         => $set,
                'nama'        => $set->nama . ' – ' . $set->slogan,
                'title'       => 'Masuk',
                'desc'      => 'Web toko furnitur ' . $set->nama,
                'img' => base_url('cdn/assets/img/' . $set->favicon),
                'url' => site_url(),
            ];

            return view('auth/signup', $data);

        }
        
    }

    public function signout()
    {
        session()->destroy();
        return redirect()->to('/signin');
    }

    public function formatc($id)
    {
        $prod = $this->func->getProdukById($id,"semua");
        $kategoriNama = $this->func->getKategori($prod->idcat, 'nama');

        if(!$prod || $prod->id == 0){
            return "Invalid Parameter: ID Produk";
        }

        $variasiData = $this->func->getProdukVariasi($prod->id);
        $warna = $this->func->getWarnaVariasi($prod->id);

        return view('client/formatc',[
            'prod' => $prod,
            'kategoriNama' => $kategoriNama,
            'variasiData' => $variasiData,
            'warna' => $warna
        ]);
    }

    public function keranjang()
    {
        $session = session();

        $kategori = $this->func->getKategori();
        $keranjang =  $this->func->getKeranjang();
        $set = $this->func->globalset('semua');
        $usrid = $this->func->cekLogin();
        $isLogin = $usrid > 0;

        $dataKeranjang = $this->func->getKeranjangFull();

        return view('client/keranjang', [
            'set' => $set,
            'desc'      => 'Web toko furnitur ' . $set->nama,
            'img' => base_url('cdn/assets/img/' . $set->favicon),
            'nama' => $set->nama . ' – ' . $set->slogan,
            'tema' => $this->func->tema(),
            'dataKeranjang' => $dataKeranjang,
            'keranjang' => $keranjang,
            // 'total' => $total,
            'url' => site_url(),
            'kategori' => $kategori,
            'isLogin' => $isLogin,
        ]);
    }

    public function _404()
    {
        $data = $this->data;
        $data['title'] = 'Halaman tidak ditemukan';
        return view('errors/html/error_404', $data);
    }

    public function invoice()
    {
        $session = session();

        if (!($this->func->cekLogin() || $session->has('usrid_temp'))) {
            return redirect()->to(site_url('signin'));
        }

        $data = $this->data;

        $paymentId = (int) $this->request->getGet('inv');

        if ($paymentId <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Revoke invoice
        if ($this->request->getGet('revoke')) {
            $payment = $this->func->getPembayaran($paymentId);

            $this->func->updateData(
                'pembayaran',
                [
                    'invoice'    => $payment->invoice . date('Hi'),
                ],
                [
                    'id' => $paymentId
                ]
            );
        }

        // Data pembayaran
        $payment = $this->func->getPembayaran($paymentId);
        $data['data'] = $payment;

        // Data transaksi
        $transactions = $this->func->getTransaksiByPaymentId($paymentId);

        if (empty($transactions)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Data user
        $user = $session->has('usrid')
            ? $this->func->getUser($payment->usrid)
            : $this->func->getUserTemp($payment->usrid_temp);

        $data['user'] = $user;

        // Data alamat
        $address = $this->func->getAlamatById($transactions[0]->alamat);

        // Rekening bank
        $banks = $this->func->getRekeningAdmin();
        

        $data['data'] = $payment;
        $data['transaksi'] = $transactions;
        $data['alamat'] = $address;
        $data['bank'] = $banks;

        $session = session();

        $data['namaUser'] = $session->has('usrid')
            ? $this->func->getProfil($user->id, 'nama', 'usrid')
            : $user->nama;

        $data['ubahMetode'] = $this->request->getGet('ubahmetode');

        if ($payment->transfer > 0) {
            $data['bayarTotal'] = $payment->transfer + $payment->kode_bayar;
            $data['biayaCod'] = $data['set']->biaya_cod > 100
                ? $data['set']->biaya_cod
                : $data['bayarTotal'] * ((int) $data['set']->biaya_cod / 100);
        }

        return view('client/detail_pembayaran', $data);
    }

}
