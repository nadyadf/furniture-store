<?php

namespace App\Controllers;

class Home extends BaseController {
    public function index() {
        $set = $this->func->globalset('semua');

        // login check (CI3-compatible)
        $usrid = $this->func->cekLogin();  // <-- panggil model
        $isLogin = $usrid > 0;

        // keranjang & wishlist
        $session = session();
        // dd(session()->get());

        $keranjang = $this->func->getKeranjang();

        $kategori = $this->func->getKategori();

        $keywords = '';
        foreach ($kategori as $k) {
            $keywords .= ',' . $k->nama;
        }

        $cari = esc($this->request->getGet('cari') ?? '', 'url');
        $promo = $this->func->getPromoAktif();

        $jmlProdukPerKategori = $this->func->getJumlahProdukPerKategori();
        $data['jmlProdukPerKategori'] = [];
        foreach ($jmlProdukPerKategori as $item) {
            $data['jmlProdukPerKategori'][$item->id] = $item->jumlah_produk;
        }

        $produkUnggulan = $this->func->getProdukUnggulan();
        $produkTerbaru = $this->func->getProdukTerbaru();

        // data ke view
        $data = [
            'set'       => $set,
            'nama'      => $set->nama . ' – ' . $set->slogan,
            'desc'      => 'Web toko furnitur ' . $set->nama,
            'cari' => $cari,
            'keywords'  => ltrim($keywords, ','),
            'img' => base_url('cdn/assets/img/' . $set->favicon),
            'url' => site_url(),
            'isLogin'   => $isLogin,
            'keranjang' => $keranjang,
            'kategori'  => $kategori,
            'promo'     => $promo,
            'jmlProdukPerKategori' => $data['jmlProdukPerKategori'] ?? [],
            'produkUnggulan' => $produkUnggulan,
            'produkTerbaru' => $produkTerbaru
        ];

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

        $tema = (isset($set->tema)) ? $set->tema: 0;

        // $total = 0;
        // foreach ($dataKeranjang as $k) {
        //     $total += $k->harga * $k->jumlah;
        // }

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

}
