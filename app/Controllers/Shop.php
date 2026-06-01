<?php

namespace App\Controllers;

class Shop extends BaseController
{
    public function index($slug = null)
    {
        $cari = $this->request->getGet('cari');

        $kategori = $this->func->getKategori();

        $produk = $this->func->getProduk(12,$cari,$slug);

        $set = $this->func->globalset('semua');

        $usrid = $this->func->cekLogin();
        $isLogin = $usrid > 0;

        $keranjang = $this->func->getKeranjang();

        return view('client/katalog', [
            'set' => $set,
            'desc'      => 'Web toko furnitur ' . $set->nama,
            'title' => 'Katalog Produk',
            'nama' => $set->nama . ' – ' . $set->slogan,
            'img' => base_url('cdn/assets/img/' . $set->favicon),
            'kategori' => $kategori,
            'produk' => $produk,
            'pager' => $this->func->pager,
            'slug' => $slug,
            'cari' => $cari,
            'keranjang' => $keranjang,
            'isLogin' => $isLogin,
            'url' => site_url(),
        ]);
    }
    
}