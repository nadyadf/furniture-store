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

    public function getKab()
    {
        $idProvinsi = $this->request->getPost('id') ?? 0;

        $rajaOngkir = $this->func->getProvinsi($idProvinsi, 'rajaongkir');

        $kabupaten = $this->db
            ->table('kab')
            ->where('idprov', $idProvinsi)
            ->orderBy('tipe', 'DESC')
            ->get()
            ->getResult();

        $html = "<option value=''>Pilih Kabupaten/Kota</option>\n";

        foreach ($kabupaten as $item) {
            $html .= sprintf(
                "<option value='%s' data-rajaongkir='%s'>%s %s</option>\n",
                $item->id,
                $item->rajaongkir,
                $item->tipe,
                $item->nama
            );
        }

        return $this->response->setJSON([
            'rajaongkir' => $rajaOngkir,
            'html'       => $html,
            'token'      => csrf_hash(),
        ]);
    }

    public function getKec()
    {
        $idKab = $this->request->getPost('id') ?? 0;

        $rajaOngkir = $this->func->getKabupaten($idKab, 'rajaongkir');

        $kabupaten = $this->db
            ->table('kec')
            ->where('idkab', $idKab)
            ->orderBy('nama', 'DESC')
            ->get()
            ->getResult();

        $html = "<option value=''>Pilih Kecamatan</option>\n";

        foreach ($kabupaten as $item) {
            $html .= sprintf(
                "<option value='%s' data-rajaongkir='%s'>%s</option>\n",
                $item->id,
                $item->rajaongkir,
                $item->nama
            );
        }

        return $this->response->setJSON([
            'rajaongkir' => $rajaOngkir,
            'html'       => $html,
            'token'      => csrf_hash(),
        ]);
    }

    public function updatePesanan()
    {
        $paymentId = (int) $this->request->getPost('id');
        $paymentMethod = (int) $this->request->getPost('metode');

        if (!$paymentId || !$paymentMethod) {
            return $this->response->setJSON([
                'success' => false,
                'token'   => csrf_hash(),
            ]);
        }

        $status = $paymentMethod === 1 ? 1 : 0;

        if ($this->request->getPost('status') !== null) {
            $status = (int) $this->request->getPost('status');
        }

        $paymentData = [
            'status'        => $status,
            'metode_bayar'  => $paymentMethod,
            'tgl_update'     => date('Y-m-d H:i:s'),
        ];

        $transactionData = [
            'status'    => $status,
            'tgl_update' => date('Y-m-d H:i:s'),
        ];

        if ($status === 1 && $paymentMethod === 1) {
            $codFee = (float) $this->request->getPost('biaya');

            $paymentData['biaya_cod'] = $codFee;

            $transactionData['cod'] = 1;
            $transactionData['biaya_cod'] = $codFee;
        }

        $this->func->updateData(
            'pembayaran',
            $paymentData,
            ['id' => $paymentId]
        );

        $this->func->updateData(
            'transaksi',
            $transactionData,
            ['idbayar' => $paymentId]
        );

        return $this->response->setJSON([
            'success' => true,
            'token'   => csrf_hash(),
        ]);
    }

    public function pesanan()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to(base_url('manage/pesanan'));
        }
        
        $status = $this->request->getGet('status') ?? 'belumbayar';
        $page   = $this->request->getGet('page') ?? 1;

        if ($status === 'belumbayar') {
            $listPesanan = $this->func->getUnpaidPayments($page);
            $data = [
                'unpaidPayments' => $listPesanan,
                'pager'          => $this->func->pager,
                'page'           => $page
            ];
            return view('pesanan/unpaid', $data); 

        } else if ($status === 'dikemas') {
            $listPesanan = $this->func->getTransactionsByStatus(1, $page);
            $data = [
                'packedOrders' => $listPesanan,
                'pager'          => $this->func->pager,
                'page'           => $page
            ];
            return view('pesanan/packed', $data);

        } else if ($status === 'dikirim') {
            $listPesanan = $this->func->getShippedTransactions();
            $data = [
                'shippedOrders' => $listPesanan,
                'pager'          => $this->func->pager,
                'page'           => $page
            ];
            return view('pesanan/shipped', $data);

        } else if ($status === 'selesai') {
            $listPesanan = $this->func->getTransactionsByStatus(3, $page, 'selesai');
            $data = [
                'completedOrders' => $listPesanan,
                'pager'          => $this->func->pager,
                'page'           => $page
            ];
            return view('pesanan/completed', $data);

        } else if ($status === 'dibatalkan') {
            $listPesanan = $this->func->getTransactionsByStatus(4, $page);
            $data = [
                'cancelledOrders' => $listPesanan,
                'pager'          => $this->func->pager,
                'page'           => $page
            ];
            return view('pesanan/cancelled', $data);
        }
    }

    public function batalkanPesanan($by = "user")
    {
        // 1. Ambil data POST menggunakan request helper CI4
        $idPesanan = $this->request->getPost('pesanan');

        if ($idPesanan) {
            $waktuSekarang = date("Y-m-d H:i:s");

            $dataPembayaran = [
                "tgl_update" => $waktuSekarang,
                "status"    => 3
            ];

            $updatePembayaran = $this->func->updateData(
                'pembayaran', 
                $dataPembayaran, 
                ['id' => $idPesanan]
            );

            if ($updatePembayaran) {
                $batal = ($by == "penjual") ? "dibatalkan oleh penjual." : "dibatalkan oleh pembeli.";

                $this->func->updateData(
                    'transaksi',
                    [
                        "status"     => 4,
                        "tgl_update" => $waktuSekarang,
                        "selesai"    => $waktuSekarang,
                        "keterangan" => $batal
                    ],
                    ['idbayar' => $idPesanan]
                );
                
                $trx = $this->func->getTransaksiByPaymentId($idPesanan);
                
                $transaksiProdukList = $this->func->getTransaksiProdukByIdTransaksi($trx[0]->id);

                foreach ($transaksiProdukList as $r) {
                    $pro = $this->func->getProdukById($r->idproduk, "semua");

                    if ($r->variasi != 0) {
                        $var = $this->func->getVariasi($r->variasi, "semua", "id");
                        $stok = $var->stok + $r->jumlah;
                        $prostok = $pro->stok + $r->jumlah;

                        $this->func->updateData(
                            'produk',
                            ['stok' => $prostok, 'tgl_update' => $waktuSekarang],
                            ['id' => $r->idproduk]
                        );


                        $this->func->updateData(
                            'produk_variasi',
                            ['stok' => $stok, 'tgl' => $waktuSekarang],
                            ['id' => $r->variasi]
                        );
                        
                        // Catat histori stok variasi
                        $dataHistori = [
                            'usrid'       => session()->get('usrid'), 
                            'stok_awal'   => $var->stok,
                            'stok_akhir'  => $stok,
                            'variasi'     => $r->variasi,
                            'jumlah'      => $r->jumlah,
                            'tgl'         => $waktuSekarang,
                            'idtransaksi' => $trx[0]->id
                        ];

                        $this->func->insertData(
                            'histori_stok',
                            $dataHistori
                        );

                    } else {
                        $stok = $pro->stok + $r->jumlah;

                        $this->func->updateData(
                            'produk',
                            ["stok" => $stok, "tgl_update" => $waktuSekarang],
                            ['id' => $r->idproduk]
                        );

                        // Catat histori stok non-variasi
                        $dataHistori = [
                            "usrid"       => session()->get('usrid'),
                            "stok_awal"   => $pro->stok,
                            "stok_akhir"  => $stok,
                            "variasi"     => 0,
                            "jumlah"      => $r->jumlah,
                            "tgl"         => $waktuSekarang,
                            "idtransaksi" => $trx[0]->id
                        ];
                        $this->func->insertData('histori_stok', $dataHistori);
                    }
                }

                // Kembalikan response sukses dalam format JSON
                return $this->response->setJSON([
                    "success" => true,
                    "message" => "berhasil membatalkan pesanan",
                    "token"   => csrf_hash()
                ]);

            } else {
                return $this->response->setJSON([
                    "success" => false,
                    "message" => "gagal membatalkan pesanan, coba ulangi beberapa saat lagi",
                    "token"   => csrf_hash()
                ]);
            }
        } else {
            return $this->response->setJSON([
                "success" => false,
                "message" => "Forbidden Access",
                "token"   => csrf_hash()
            ]);
        }
    }

    public function lacakiriman()
    {
        $orderid = $this->request->getGet('orderid');

        if ($orderid) {
            $trx = $this->func->getTransaksiByOrderId($orderid)[0];
            $apikey = $this->func->globalset("rajaongkir");

            // Format nama kurir dan no resi/awb
            $courier = strtolower($trx->kurir_rajaongkir);
            $awb = $trx->resi;

            // Inisialisasi HTTP Client bawaan CI4
            $client = \Config\Services::curlrequest([
                'timeout'     => 30,
                'http_errors' => false, // Mencegah Exception otomatis jika HTTP status 4xx/5xx
            ]);

            // URL API RajaOngkir Komerce
            $url = "https://rajaongkir.komerce.id/api/v1/track/waybill?awb={$awb}&courier={$courier}";

            try {
                $response = $client->request('POST', $url, [
                    'headers' => [
                        'key' => $apikey
                    ]
                ]);

                $statusCode = $response->getStatusCode();
                $body = json_decode($response->getBody());

                // Pengecekan HTTP status 200 dan status sukses dari API Komerce
                if ($statusCode === 200 && isset($body->status) && $body->status === true) {
                    $result = $body->data;
                    $manifest = $result->manifest ?? [];

                    // 1. Status Pengiriman
                    if (isset($result->delivered) && $result->delivered == true) {
                        $penerima = !empty($result->delivery_status->pod_receiver) 
                            ? strtoupper(strtolower($result->delivery_status->pod_receiver)) 
                            : '-';
                        
                        $tglDiterima = $this->func->ubahTgl(
                            "d M Y H:i", 
                            $result->delivery_status->pod_date . " " . $result->delivery_status->pod_time
                        );

                        echo "
                            <div class='mb-4'>
                                Status: <b style='color:#28a745;'>PAKET TELAH DITERIMA</b><br/>
                                Penerima: <b>{$penerima}</b><br/>
                                Tgl diterima: {$tglDiterima} WIB
                            </div>
                        ";
                    } else {
                        echo "<div class='mb-4'>Status: <b style='color:#c0392b;'>PAKET SEDANG DIKIRIM</b></div>";
                    }

                    // 2. Header Tabel Track
                    echo "
                        <div class='row py-2 fw-bold border-bottom'>
                            <div class='col-md-3'>TANGGAL</div>
                            <div class='col-md-9'>STATUS</div>
                        </div>
                    ";

                    // 3. Menampilkan riwayat POD (Diterima) jika bukan JNE
                    if (isset($result->delivered) && $result->delivered == true && $courier !== "jne") {
                        $tglPod = $this->func->ubahTgl(
                            "d/m/Y H:i", 
                            $result->delivery_status->pod_date . " " . $result->delivery_status->pod_time
                        );
                        $penerimaPod = strtoupper(strtolower($result->delivery_status->pod_receiver));

                        echo "
                            <div class='row py-2 border-bottom border-dashed'>
                                <div class='col-md-3'>
                                    <i>{$tglPod} WIB</i>
                                </div>
                                <div class='col-md-9'>
                                    <i>Diterima oleh {$penerimaPod}</i>
                                </div>
                            </div>
                        ";
                    }

                    // 4. Looping Manifest / Riwayat Perjalanan
                    foreach ($manifest as $item) {
                        $tglManifest = $this->func->ubahTgl(
                            "d/m/Y H:i", 
                            $item->manifest_date . " " . $item->manifest_time
                        );
                        $desc = $item->manifest_description ?? '';
                        $city = $item->city_name ?? '';

                        echo "
                            <div class='row py-2 border-bottom border-dashed'>
                                <div class='col-md-3'>
                                    <i>{$tglManifest} WIB</i>
                                </div>
                                <div class='col-md-9'>
                                    <i>{$desc}</i> <i>{$city}</i>
                                </div>
                            </div>
                        ";
                    }

                } else {
                    // Jika Resi Tidak Ditemukan / Error dari API
                    echo "
                        <div class='row py-2 border-bottom border-dashed'>
                            <div class='col-md-12'>
                                Nomor Resi tidak ditemukan, coba ulangi beberapa saat lagi sampai resi diperbarui di sistem ekspedisi.
                            </div>
                        </div>
                    ";
                }

            } catch (\Exception $e) {
                echo "<span class='text-danger'>Terjadi kendala saat menghubungi pihak ekspedisi, cobalah beberapa saat lagi.</span>";
            }

        } else {
            echo "<span class='badge bg-danger'><i class='fa fa-exclamation-triangle'></i> Terjadi kesalahan sistem, silakan ulangi beberapa saat lagi.</span>";
        }
    }

    public function pesananterakhir()
    {
        $usrid = session('usrid') ?? 0;

        if (!$usrid) {
            return "<div class='text-muted small py-3'>Silakan login terlebih dahulu.</div>";
        }

        // Ambil data transaksi yang sudah di-join dengan tabel pembayaran
        $pesanan = $this->func->getPesananTerakhir($usrid, 5);

        if (empty($pesanan)) {
            echo "
                <div class='card border-0 shadow-sm p-4 text-center my-3 rounded-3'>
                    <div class='text-muted'>
                        <i class='fas fa-box-open fa-2x mb-2 opacity-50'></i>
                        <p class='mb-0'>Belum ada riwayat pesanan.</p>
                    </div>
                </div>
            ";
            return;
        }

        $html = '<div class="d-flex flex-column gap-3 mb-4">';

        foreach ($pesanan as $row) {
            // Status Badge Bootstrap 5
            switch ($row->status) {
                case 0:
                    $statusBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Belum Bayar</span>';
                    break;
                case 1:
                    $statusBadge = '<span class="badge bg-info text-white"><i class="fas fa-box me-1"></i> Diproses</span>';
                    break;
                case 2:
                    $statusBadge = '<span class="badge bg-primary"><i class="fas fa-truck me-1"></i> Dikirim</span>';
                    break;
                case 3:
                    $statusBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Selesai</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i> Dibatalkan</span>';
                    break;
            }

            $tgl = date("d M Y H:i", strtotime($row->tgl));
            // Mengambil total nominal dari hasil JOIN tabel pembayaran
            $total = "Rp " . number_format($row->total ?? 0, 0, ",", ".");

            $html .= "
                <div class='card border-0 shadow-sm rounded-3 p-3'>
                    <div class='d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom'>
                        <div>
                            <span class='fw-bold text-primary me-2'>#{$row->orderid}</span>
                            <small class='text-muted me-2'>• {$tgl} WIB</small>
                        </div>
                        <div>{$statusBadge}</div>
                    </div>
                    
                    <div class='row align-items-center g-2'>
                        <div class='col-md-8'>
                            <small class='text-muted d-block'>Total Pembayaran:</small>
                            <span class='fw-bold fs-5 text-success'>{$total}</span>
                        </div>
                        <div class='col-md-4 text-md-end'>
                            <a href='" . site_url("manage/detailpesanan?orderid=" . $row->orderid) . "' class='btn btn-outline-primary btn-sm fw-semibold'>
                                <i class='fas fa-eye me-1'></i> Detail Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            ";
        }

        $html .= '</div>';

        echo $html;
    }

    public function tambahalamat()
    {
        $db      = db_connect();
        $usrid   = $this->session->get('usrid');

        $idkec   = $this->request->getPost('idkec');
        $kodepos = $this->request->getPost('kodepos');
        $status  = $this->request->getPost('status');
        $id      = $this->request->getPost('id');

        if ($idkec && $kodepos) {
            
            // ----------------------------------------------------
            // 1. VALIDASI KODE POS DAN KECAMATAN VIA GLOBALDATA
            // ----------------------------------------------------
            // Pastikan model/library GlobalData sudah di-load (misal: $this->globaldata)
            $rajaongkirId = $this->func->validateKel((int)$idkec, (string)$kodepos);

            if (!$rajaongkirId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kode pos ' . $kodepos . ' tidak cocok dengan kecamatan yang dipilih!',
                    'token'   => csrf_hash()
                ]);
            }

            // ----------------------------------------------------
            // 2. PROSES SIMPAN / UPDATE ALAMAT
            // ----------------------------------------------------
            $builder = $db->table('alamat');

            // Jika diset sebagai Alamat Utama (status = 1)
            if ($status == 1) {
                $builder->where('usrid', $usrid)->update(['status' => 0]);
            }

            $data = [
                'usrid'   => $usrid,
                'idkec'   => $idkec,
                'nama'    => $this->request->getPost('nama'),
                'judul'   => $this->request->getPost('judul'),
                'alamat'  => $this->request->getPost('alamat'),
                'kodepos' => $kodepos,
                'no_hp'    => $this->request->getPost('nohp'),
                'status'  => $status
                // Jika kamu menyimpan id rajaongkir/kelurahan di tabel alamat, bisa tambahkan di sini:
                // 'idkel' => $rajaongkirId 
            ];

            if ($id && $id > 0) {
                $simpan = $builder->where('id', $id)->update($data);
                $insertedId = $id;
                $message = 'Alamat berhasil diperbarui!';
            } else {
                $simpan = $builder->insert($data);
                $insertedId = $db->insertID();
                $message = 'Alamat baru berhasil ditambahkan!';
            }

            if ($simpan) {
                return $this->response->setJSON([
                    'success' => true,
                    'id'      => $insertedId,
                    'message' => $message,
                    'token'   => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data ke database.',
                    'token'   => csrf_hash()
                ]);
            }
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kecamatan dan Kode Pos wajib diisi!',
                'token'   => csrf_hash()
            ]);
        }
    }

    public function loadalamat()
    {
        $usrid   = $this->session->get('usrid');

        // Ambil data alamat user dari DB
        $db     = db_connect();
        $alamat = $db->table('alamat')
                    ->where('usrid', $usrid)
                    ->orderBy('status', 'DESC') // Alamat utama di paling atas
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getResult();

        // Kirim data ke partial view khusus atau kirim via view langsung
        return view('partials/alamat', ['alamat' => $alamat]);
    }

    public function getAlamat()
    {
        // 1. Ambil request POST & Session CI4
        $rekId   = $this->request->getPost('rek');
        $usrid   = $this->session->get('usrid');

        if ($rekId) {
            // Ambil data alamat dari helper/model $this->func
            $rek = $this->func->getAlamatById($rekId);

            // Validasi data dan pastikan alamat milik user yang sedang login
            if ($rek && isset($rek->id) && $rek->usrid == $usrid) {
                
                // Ambil idkab & idprov via helper/model kamu
                $kab  = $this->func->getKecamatan($rek->idkec, 'idkab');
                $prov = $this->func->getKabupaten($kab)->idprov;

                return $this->response->setJSON([
                    'success' => true,
                    'kab'     => $kab,
                    'prov'    => $prov,
                    'idkec'   => $rek->idkec,
                    'nama'    => $rek->nama,
                    'judul'   => $rek->judul,
                    'alamat'  => $rek->alamat,
                    'kodepos' => $rek->kodepos,
                    'nohp'    => $rek->no_hp,
                    'status'  => $rek->status,
                    'token'   => csrf_hash()
                ]);
            }
        }

        // Jika id tidak dikirim atau alamat tidak ditemukan/bukan milik user
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Data alamat tidak ditemukan atau akses ditolak.',
            'token'   => csrf_hash()
        ]);
    }

    public function hapusAlamat()
    {
        $rek = $this->request->getPost('rek');

        if ($rek) {
            $db = db_connect();
            $db->table('alamat')->where('id', $rek)->delete();

            return $this->response->setJSON([
                'success' => true,
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'token'   => csrf_hash()
        ]);
    }

    public function updateProfil()
    {
        // 1. Ambil instance request & response service
        $request = \Config\Services::request();

        // 2. Pengecekan Login (Sesuaikan metode session/auth kamu)
        if ($this->session->get('usrid')) {
            $nama    = $request->getPost('nama');
            $email   = $request->getPost('email');
            $nohpInput = $request->getPost('nohp');

            if ($nama !== null) {
                $nohp = preg_replace('/[^0-9]/', '', $nohpInput);
                
                // Format nomor HP
                $no1 = (substr($nohp, 0, 2) !== "62") ? "62" . $nohp : $nohp;
                $no2 = (substr($nohp, 0, 2) !== "62") ? "0" . $nohp : "0" . substr($nohp, 2);

                // 3. Query Builder CI 4 (Menggunakan Prepared Statements / Parameter Binding agar aman dari SQL Injection)
                $db = db_connect();
                $builder = $db->table('user_data');
                
                $usrid = $this->session->get('usrid');

                $builder->select('id')
                        ->where('id !=', $usrid)
                        ->groupStart()
                            ->whereIn('no_hp', [$no1, $no2])
                            ->orWhere('username', $email)
                        ->groupEnd();

                $query = $builder->get();

                // 4. Cek apakah email / nohp sudah digunakan user lain
                if ($query->getNumRows() === 0) {
                    // Update tabel profil
                    $db->table('profil')
                    ->where('usrid', $usrid)
                    ->update([
                        'nama'    => $nama,
                        'no_hp'    => $nohpInput,
                    ]);

                    // Update tabel userdata
                    $db->table('user_data')
                    ->where('id', $usrid)
                    ->update([
                        'username' => $email,
                        'nama'     => $nama
                    ]);

                    return $this->response->setJSON([
                        'success' => true,
                        'msg'     => 'Berhasil mengupdate profil',
                        'token'   => csrf_hash() // CSRF hash baru di CI 4
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'msg'     => 'Nomor Whatsapp atau Alamat Email sudah terdaftar, silahkan menggunakan nomor lain',
                        'token'   => csrf_hash()
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Forbidden!',
                    'token'   => csrf_hash()
                ]);
            }
        } else {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Forbidden!',
                'token'   => csrf_hash()
            ]);
        }
    }

    public function updatePass()
    {
        $session = session();
        $request = \Config\Services::request();

        // 1. Cek Login
        if ($this->func->cekLogin() > 0) {

            $password = $request->getPost('password');

            if (!empty($password)) {
                $userId = $session->get('usrid');

                // Enkripsi password menggunakan BCRYPT (Sama dengan fungsi signup)
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Update ke tabel user_data
                $db = \Config\Database::connect();
                $db->table('user_data')
                ->where('id', $userId)
                ->update([
                    'password' => $passwordHash
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'msg'     => 'Berhasil mengupdate password',
                    'token'   => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Password tidak boleh kosong!',
                    'token'   => csrf_hash()
                ]);
            }
        } else {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Forbidden!',
                'token'   => csrf_hash()
            ]);
        }
    }
    
}