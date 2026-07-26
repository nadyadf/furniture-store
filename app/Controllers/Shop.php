<?php

namespace App\Controllers;

class Shop extends BaseController
{
    public function index($slug = null)
    {
        $data = $this->data;
        $data['cari'] = $this->request->getGet('cari');
        $data['title'] = 'Katalog Produk';

        $data['produk'] = $this->func->getProduk(12,$data['cari'],$slug);
        $data['pager'] = $this->func->pager;
        $data['slug'] = $slug;

        return view('client/katalog', $data);
    }
    
}