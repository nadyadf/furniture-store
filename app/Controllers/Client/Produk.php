<?php

namespace App\Controllers\Client;
use App\Controllers\BaseController;

class Produk extends BaseController {
  public function index($url=null){
    
    if (isset($url) && $url != null) {
        $data = $this->data;

      session()->set('url', site_url('produk/' . $url));

      $db = db_connect();

      $db->table('produk');
      $res = $db->table('produk')
          ->where('url', $url)
          ->limit(1)
          ->get();

      if ($res->getNumRows() > 0) {
          $dbproduk = $res->getRow();
      } else {
          return redirect()->to('/404_notfound');
      }

      $data['kategoriproduk'] = $this->func->getKategori($dbproduk->idcat);
      $data['data'] = $this->func->getProdukById($dbproduk->id);
      $data['produkterkait'] = $this->func->getProdukTerkait($dbproduk->id, $dbproduk->idcat);

      $data['img'] = $this->func->getFoto($dbproduk->id, 'utama');
      $data['nama'] = $dbproduk->nama;
      $data['desc'] = strip_tags($dbproduk->deskripsi);
      $data['url'] = site_url('produk/' . $dbproduk->url);


      $data['varproduk'] = $this->func->getWarnaVariasi($dbproduk->id);

      $idkab = $data['data']->gudang ? $this->func->getGudang($data['data']->gudang, 'idkab') : $this->func->globalset('kota');
      $data['kota'] = $this->func->getKabupaten($idkab);
    

      echo view('client/produk', $data);
  } else {
      return redirect()->to('/404_index');
  }
  }
}