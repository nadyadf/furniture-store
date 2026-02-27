<?php

namespace App\Controllers;

class Home extends BaseController {
    public function index() {
        $set = $this->func->globalset('semua');

        // login check (CI3-compatible)
        $usrid = $this->func->cekLogin();  // <-- panggil model
        $isLogin = $usrid > 0;

        // keranjang & wishlist
        $keranjang = $isLogin ? $this->func->getKeranjang() : 0;

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
        // dd($produkTerbaru);

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

            // $pass  = service('func')->decode($user->password);
            $pass  = $user->password;

            // ✅ password cocok
            if ($passInput == $pass) {

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

        return view('auth/signup', $data);
    }

}
