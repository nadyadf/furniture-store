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
        $wishlist  = $isLogin ? $this->func->getWishlistCount() : 0;

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
            'wishlist'  => $wishlist,
            'kategori'  => $kategori,
            'promo'     => $promo,
            'jmlProdukPerKategori' => $data['jmlProdukPerKategori'] ?? [],
            'produkUnggulan' => $produkUnggulan,
            'produkTerbaru' => $produkTerbaru
        ];

        return view('client/home', $data);
    }

}
