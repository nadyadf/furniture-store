<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Assync extends BaseController
{
    protected $db;
    protected $session;

    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);

        $this->db = Database::connect();
        $this->session = session();

        $set = $this->func->globalset('semua');

        // $production = (strpos($set->midtrans_snap, "sandbox") !== false) ? false : true;

        // \Midtrans\Config::$serverKey = $set->midtrans_server;
        // \Midtrans\Config::$isProduction = $production;
        // \Midtrans\Config::$isSanitized = true;
        // \Midtrans\Config::$is3ds = true;
    }

    public function index()
    {
        return redirect()->to('/404_notfound');
    }

    public function prosesbeli()
    {
      $post = $this->request->getPost();

      if (!$this->session->get("usrid") && !$this->session->get("usrid_temp")) {

          $upline = $this->session->get("aff") ?? 0;

          $this->db->table("user_temp")->insert([
              "tgl" => date("Y-m-d H:i:s"),
              "upline" => $upline
          ]);

          $usrid = $this->db->insertID();

          $this->session->set("usrid_temp", $usrid);
      }

      if ($post && ($this->session->get("usrid") || $this->session->get("usrid_temp"))) {

          $usrid = $this->session->get("usrid") ?? $this->session->get("usrid_temp");
          $jenis = $this->session->get("usrid") ? 1 : 2;

          $prod = $this->func->getProdukById($post["idproduk"]);

          $keterangan = $post["keterangan"] ?? "";
          $variasi = $post["variasi"] ?? 0;

          $var = $this->func->getVariasi($variasi, "semua");

          $update = false;
          $id = 0;
          $harga = $post["harga"];

          // cek keranjang
          $builder = $this->db->table("transaksi_produk");

          $builder->where("idproduk", $prod->id);
          $builder->where("variasi", $variasi);
          $builder->where("idtransaksi", 0);

          if ($jenis == 1) {
              $builder->where("usrid", $this->session->get("usrid"));
          } else {
              $builder->where("usrid_temp", $this->session->get("usrid_temp"));
          }

          $db = $builder->get()->getResult();

          if ($var->id > 0) {

              $harga = $var->harga;

              if (intval($post["jumlah"]) > $var->stok) {
                  return $this->response->setJSON([
                      "success" => false,
                      "msg" => "Stok tidak mencukupi, stok tersedia hanya {$var->stok} pcs",
                      "token" => csrf_hash()
                  ]);
              }

              foreach ($db as $r) {

                  $jumlah = intval($post["jumlah"]) + $r->jumlah;

                  if ($jumlah > $var->stok) {
                      return $this->response->setJSON([
                          "success" => false,
                          "msg" => "Stok tidak mencukupi, stok tersedia hanya {$var->stok} pcs",
                          "token" => csrf_hash()
                      ]);
                  } else {
                      $update = true;
                      $id = $r->id;
                  }
              }

          } else {

              if (intval($post["jumlah"]) > $prod->stok) {
                  return $this->response->setJSON([
                      "success" => false,
                      "msg" => "Stok tidak mencukupi, stok tersedia hanya {$prod->stok} pcs",
                      "token" => csrf_hash()
                  ]);
              }

              $harga = $prod->harga;

              foreach ($db as $r) {

                  $jumlah = intval($post["jumlah"]) + $r->jumlah;

                  if ($jumlah > $prod->stok) {
                      return $this->response->setJSON([
                          "success" => false,
                          "msg" => "Stok tidak mencukupi, stok tersedia hanya {$prod->stok} pcs",
                          "token" => csrf_hash()
                      ]);
                  } else {
                      $update = true;
                      $id = $r->id;
                  }
              }
          }

          $total = intval($post["jumlah"]) * $harga;

          if (!$update) {

              $data = [
                  "gudang" => $prod->gudang,
                  "idproduk" => $post["idproduk"],
                  "tgl" => date("Y-m-d H:i:s"),
                  "jumlah" => $post["jumlah"],
                  "harga" => $harga,
                  "keterangan" => $keterangan,
                  "variasi" => $variasi,
                  "idtransaksi" => 0
              ];

              if ($jenis == 1) {
                  $data["usrid"] = $usrid;
              } else {
                  $data["usrid_temp"] = $this->session->get("usrid_temp");
              }

              $this->db->table("transaksi_produk")->insert($data);

              $id = $this->db->insertID();

          } else {

              $this->db->table("transaksi_produk")
                  ->where("id", $id)
                  ->update([
                      "jumlah" => $jumlah,
                      "harga" => $harga,
                      "tgl" => date("Y-m-d H:i:s"),
                      "keterangan" => $keterangan
                  ]);
          }

          return $this->response->setJSON([
              "success" => true,
              "produk" => $id,
              "total" => $total,
              "token" => csrf_hash()
          ]);
      }

      return $this->response->setJSON([
          "success" => false,
          "token" => csrf_hash()
      ]);
  }

  public function updateKeranjang()
    {
        $id = $this->request->getPost('update');

        if ($id && $id > 0) {

            $jumlah = (int) $this->request->getPost('jumlah');

            $db = db_connect();

            $trx = $this->func->getKeranjangFull([
                'id' => $id
            ])[0] ?? null;

            $stok = ($trx->variasi > 0)
                ? $this->func->getVariasi($trx->variasi, "stok")
                : $this->func->getProduk($trx->idproduk, "stok");

            if ($stok >= $jumlah) {

                $db->table('transaksi_produk')
                    ->where('id', $id)
                    ->update([
                        'jumlah' => $jumlah
                    ]);

                return $this->response->setJSON([
                    'success' => true,
                    'token'   => csrf_hash()
                ]);

            } else {

                $db->table('transaksi_produk')
                    ->where('id', $id)
                    ->update([
                        'jumlah' => $stok
                    ]);

                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Stok produk tidak mencukupi, maksimal pemesanan ' . $stok . ' pcs',
                    'token'   => csrf_hash()
                ]);
            }

        } else {

            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Produk tidak tersedia',
                'token'   => csrf_hash()
            ]);
        }
    }

    public function hapusKeranjang()
    {
        $id = $this->request->getPost('hapus');

        if ($id && $id > 0) {

            $this->db->table('transaksi_produk')
                ->where('id', $id)
                ->delete();

            return $this->response->setJSON([
                'success' => true
            ]);

        } else {

            return $this->response->setJSON([
                'success' => false
            ]);
        }
    }
}