<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminBaseController;

class Api extends AdminBaseController
{
    protected $func;

    public function __construct()
    {
        
    }

    public function pesanan()
    {
      // 1. Validasi Akses Admin
      if ($this->func->cekLogin('admin') === 0) {
          return $this->response->setStatusCode(401)->setJSON([
              'success' => false,
              'message' => 'Unauthorized access'
          ]);
      }

      // 2. Ambil parameter Request (GET / POST)
      $load  = $this->request->getGet('load') ?? 'bayar';
      $page  = $this->request->getGet('page') ?? 1;
      $cari  = $this->request->getPost('cari') ?? $this->request->getGet('cari') ?? '';

      $res  = '';
      $data = [];

      // 3. Switch-Case berdasarkan status 'load' tab
      switch ($load) {
          case 'bayar':
              // Ambil data pembayaran belum terkonfirmasi / unpaid
              $listPesanan = $this->func->getAdminUnpaidPayments($page, $cari);
              $data = [
                  'unpaidPayments' => $listPesanan,
                  'pager'          => $this->func->pager,
                  'page'           => $page,
                  'cari'           => $cari
              ];
              $res = view('admin/pesanan_unpaid', $data);
              break;

          case 'dikemas':
              $listPesanan = $this->func->getPendingShipmentOrders($cari, $page);
              $data = [
                  'packedOrders' => $listPesanan,
                  'pager'        => $this->func->pager,
                  'page'         => $page,
                  'cari'         => $cari
              ];
              $res = view('admin/pesanan_packed', $data);
              break;

          case 'dikirim':
              // Status 2: Dikirim
              $listPesanan = $this->func->getShippedOrders($cari, $page);
              $data = [
                  'shippedOrders' => $listPesanan,
                  'pager'         => $this->func->pager,
                  'page'          => $page,
                  'cari'          => $cari
              ];
              $res = view('admin/pesanan_shipped', $data);
              break;

          case 'selesai':
              // Status 3: Selesai
              $listPesanan = $this->func->getCompletedOrders($cari, $page);
              $data = [
                  'completedOrders' => $listPesanan,
                  'pager'           => $this->func->pager,
                  'page'            => $page,
                  'cari'            => $cari
              ];
              $res = view('admin/pesanan_completed', $data);
              break;

          case 'batal':
          case 'dibatalkan':
              $listPesanan = $this->func->getCanceledOrders($cari, $page);
              $data = [
                  'cancelledOrders' => $listPesanan,
                  'pager'           => $this->func->pager,
                  'page'            => $page,
                  'cari'            => $cari
              ];
              $res = view('admin/pesanan_canceled', $data);
              break;

          default:
              return $this->response->setJSON([
                  'success' => false,
                  'message' => 'Status tidak valid'
              ]);
      }

      // 4. Return Response JSON berisi HTML View dan CSRF Token CI4 Terbaru
      return $this->response->setJSON([
          'result' => $res,
          'token'  => csrf_hash()
      ]);
  }

  public function updatepesanan()
  {
      // 1. Cek Sesi Login Admin CI4
      if (!session()->has('isLoggedIn')) {
          return redirect()->to('admin/login');
      }

      $request = \Config\Services::request();
      $id      = $request->getPost('id');

      if (!empty($id)) {
          $id          = (int)$id;
          $status      = $request->getPost('status') ? (int)$request->getPost('status') : 1;
          $statusbayar = $request->getPost('statusbayar');

          // Ambil data transaksi berdasarkan idbayar
          $trx = $this->func->getTransaksiByPaymentId($id);

          // 2. Update Tabel Pembayaran jika ada input statusbayar
          if ($statusbayar !== null) {

              $this->func->updateData('pembayaran', [
                      'status'    => (int)$statusbayar,
                      'tgl_update' => date('Y-m-d H:i:s')
                  ], ['id' => $id]);
          }

          // 3. Tentukan Data Update Transaksi
          $now = date('Y-m-d H:i:s');
          if ($status >= 3) {
              $data = [
                  'status'    => $status,
                  'tgl_update' => $now,
                  'selesai'   => $now
              ];
          } elseif ($status === 1) {
              $data   = [
                  'status'    => $status,
                  'tgl_update' => $now
              ];
          } else {
              $data = [
                  'status'    => $status,
                  'tgl_update' => $now
              ];
          }

          $this->func->updateData('transaksi', $data, ['idbayar' => $id]);

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

  public function detailpesanan()
  {
      // 1. Cek Sesi Login Admin
      if (!session()->has('isLoggedIn')) {
          return redirect()->to('admin/login');
      }

      $request = \Config\Services::request();
      $theid   = $request->getGet('theid');

      if (!empty($theid)) {
          // Ambil data yang sudah siap saji dari GlobalData
          $data = $this->func->getDetailPesanan((int)$theid);

          if (!$data) {
              return $this->response->setStatusCode(404)->setBody("Data transaksi tidak ditemukan.");
          }

          // Kirim data ke View
          return view('admin/pesanan_detail', $data);
      }

      return $this->response->setStatusCode(404)->setBody("404 - Request Not Found");
  }

  public function batalkanpesanan()
    {
        // 1. Cek Sesi Login Admin
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = \Config\Services::request();
        $id      = $request->getPost('id');

        if (!empty($id)) {
            $id  = (int)$id;
            $now = date('Y-m-d H:i:s');

            // Ambil data transaksi berdasarkan idbayar
            $trx = $this->func->getTransaksiByPaymentId($id)[0];

            if (!$trx) {
                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Data transaksi tidak ditemukan',
                    'token'   => csrf_hash()
                ]);
            }

            // Inisialisasi DB Connection untuk Transaction
            $db = \Config\Database::connect();
            
            // --- MULAI DATABASE TRANSACTION ---
            $db->transStart();

            // Ambil item produk transaksi
            $dbProduk = $this->func->getData('transaksi_produk', $trx->id, 'idtransaksi', false);

            $variasiList = [];
            $stockList   = [];
            $stokAwal    = [];
            $jmlList     = [];

            foreach ($dbProduk as $r) {
                $idVariasi = (int)($r->variasi ?? 0);

                if ($idVariasi > 0) {
                    // Produk Variasi
                    $var = $this->func->getVariasi($idVariasi, "semua", "id");
                    if (isset($var->stok)) {
                        $variasiList[] = $idVariasi;
                        $stockList[]   = $var->stok + $r->jumlah;
                        $stokAwal[]    = $var->stok;
                        $jmlList[]     = $r->jumlah;
                    }
                } else {
                    // Produk Non-Variasi
                    $pro = $this->func->getProdukById($r->idproduk);
                    if (is_object($pro)) {
                        $stokBaru = $pro->stok + $r->jumlah;

                        $this->func->updateData('produk', [
                            'stok'       => $stokBaru,
                            'tgl_update' => $now
                        ], ['id' => $r->idproduk]);

                        $this->func->insertData('histori_stok', [
                            'usrid'       => $trx->usrid,
                            'stok_awal'    => $pro->stok,
                            'stok_akhir'   => $stokBaru,
                            'variasi'     => 0,
                            'jumlah'      => $r->jumlah,
                            'tgl'         => $now,
                            'idtransaksi' => $trx->id
                        ]);
                    }
                }
            }

            // Update stok variasi & simpan histori
            for ($i = 0; $i < count($variasiList); $i++) {
                $this->func->updateData('produk_variasi', [
                    'stok' => $stockList[$i],
                    'tgl'  => $now
                ], ['id' => $variasiList[$i]]);

                $this->func->insertData('histori_stok', [
                    'usrid'       => $trx->usrid,
                    'stok_awal'   => $stokAwal[$i],
                    'stok_akhir'  => $stockList[$i],
                    'variasi'     => $variasiList[$i],
                    'jumlah'      => $jmlList[$i],
                    'tgl'         => $now,
                    'idtransaksi' => $trx->id
                ]);
            }

            // Update status pembayaran & transaksi
            $this->func->updateData('pembayaran', [
                'status'     => 3,
                'tgl_update' => $now
            ], ['id' => $id]);

            $this->func->updateData('transaksi', [
                'status'     => 4,
                'tgl_update' => $now,
                'selesai'    => $now
            ], ['idbayar' => $id]);

            // --- SELESAIKAN TRANSACTION ---
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Gagal memproses pembatalan pesanan.',
                    'token'   => csrf_hash()
                ]);
            }

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

    public function cetakInvoice()
    {
        // 1. Cek Sesi Login Admin
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = \Config\Services::request();
        // Tangkap parameter 'id' atau 'inv' dari URL
        $trxid = (int)($request->getGet('id') ?? $request->getGet('inv') ?? 0);

        if ($trxid <= 0) {
            return $this->response->setStatusCode(404)->setBody("ID Invoice tidak valid.");
        }

        // 6. Susun Array Data Siap Kirim ke View
        $data = $this->func->getInvoiceData($trxid);

        return view('admin/invoice', $data);
    }

    public function inputresi()
    {
        // 1. Cek Sesi Login Admin
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = \Config\Services::request();
        $theid   = $request->getPost('theid');

        if (!empty($theid)) {
            $theid = (int)$theid;
            $now   = date('Y-m-d H:i:s');

            // 2. Ambil Data Transaksi
            $trx = $this->func->getTransaksiById($theid, true);
            if (is_array($trx)) {
                $trx = $trx[0] ?? null;
            }

            if (!$trx) {
                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Data transaksi tidak ditemukan',
                    'token'   => csrf_hash()
                ]);
            }

            // 3. Ambil Data User/Pembeli
            $isMember = ((int)($trx->usrid ?? 0) > 0);
            $user     = $isMember 
                ? $this->func->getUser($trx->usrid) 
                : $this->func->getUserTemp($trx->usrid_temp ?? 0);

            // 4. Penentuan Status Pengiriman (COD / Non-COD)
            $status = strtolower($trx->kurir ?? '') !== 'cod' ? 2 : 3;
            $resi   = trim($request->getPost('resi') ?? '');

            $dataUpdate = [
                'resi'       => $resi,
                'tgl_update' => $now,
                'kirim'      => $now,
                'status'     => $status
            ];

            if ($status === 3) {
                $dataUpdate['selesai'] = $now;
            }

            // 5. Update Status Transaksi
            $this->func->updateData('transaksi', $dataUpdate, ['id' => $theid]);

            // 6. Notifikasi Email, WhatsApp, & Push Notification
            $set       = $this->func->globalset("semua");
            $namatoko  = $set->nama ?? 'Toko Kami';
            $userEmail = $user->username ?? $user->email ?? '';
            $userNoHp  = $user->no_hp ?? '';

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

    public function cetakLabel()
    {
        // 1. Cek Sesi Login Admin
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = \Config\Services::request();
        $trxid   = (int)($request->getGet('id') ?? $request->getGet('inv') ?? 0);

        if ($trxid > 0) {
            // Ambil data detail transaksi/label menggunakan GlobalData
            $data = $this->func->getInvoiceData($trxid);

            if (!$data) {
                return $this->response->setStatusCode(404)->setBody("Data transaksi label tidak ditemukan.");
            }

            // Render View Label
            return view('admin/label', $data);
        }

        return $this->response->setStatusCode(404)->setBody("404 - ID Transaksi Tidak Valid");
    }

    public function terimapesanan()
    {
        // 1. Cek Sesi Login Admin
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = \Config\Services::request();
        $id      = (int)$request->getPost('id');

        if ($id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Transaksi tidak valid',
                'token'   => csrf_hash()
            ]);
        }

        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // Mulai Database Transaction untuk keamanan mutasi data
        $db->transStart();

        $this->func->updateData('transaksi', [
            'status'    => 3,
            'tgl_update' => $now,
            'selesai'   => $now
        ], ['id' => $id]);

        $trx = $this->func->getData('transaksi', $id);

        if (!$trx) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
                'token'   => csrf_hash()
            ]);
        }

        // Selesaikan Database Transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses transaksi',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pesanan berhasil diselesaikan',
            'token'   => csrf_hash()
        ]);
    }

    public function lacakiriman()
    {
        // 1. Cek Sesi Login Admin
        if (!session()->has('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        $request = \Config\Services::request();
        $orderId = $request->getGet('orderid');

        if (empty($orderId)) {
            return $this->response->setBody("
                <div class='alert alert-danger py-2 px-3 fs-7 mb-0'>
                    <i class='fas fa-exclamation-triangle me-1'></i> Order ID tidak ditemukan.
                </div>
            ");
        }

        $db = \Config\Database::connect();

        // 2. Ambil Setting API Key
        $apiKey = $this->func->globalset("rajaongkir") ?? '';

        // 3. Ambil Detail Transaksi
        $trxArray = $this->func->getTransaksiByOrderId($orderId, true);
        $trx      = $trxArray[0] ?? null;

        if (!$trx || empty($trx->resi)) {
            return $this->response->setBody("
                <div class='alert alert-warning py-2 px-3 fs-7 mb-0'>
                    <i class='fas fa-info-circle me-1'></i> Resi pengiriman belum diinput untuk pesanan ini.
                </div>
            ");
        }

        // Ambil Kode Kurir Ekspedisi
        $kodeKurir  = strtolower($this->func->getKurir($trx->kurir, 'rajaongkir') ?? 'jne');
        $airwayBill = trim($trx->resi);

        // Build URL Komerce/RajaOngkir Sandbox (Ubah domain ke api.komerce.id jika sudah Production)
        $apiUrl = "https://api-sandbox.collaborator.komerce.id/order/api/v1/orders/history-airway-bill?" . http_build_query([
            'shipping'    => $kodeKurir,
            'airway_bill' => $airwayBill
        ]);

        // 4. Hit API Komerce/RajaOngkir (GET Method)
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => [
                "x-api-key: " . $apiKey,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $this->response->setBody("
                <div class='alert alert-danger py-2 px-3 fs-7 mb-0'>
                    <i class='fas fa-exclamation-circle me-1'></i> Terjadi kendala saat menghubungi server ekspedisi. Silakan coba lagi.
                </div>
            ");
        }

        $res = json_decode($response);

        // 5. Render Output HTML Pelacakan
        $isSuccess  = $res->status ?? false;
        $data       = $res->data ?? null;

        if ($isSuccess && !empty($data)) {
            $history     = $data->history ?? $data->manifest ?? [];
            $summary     = $data->summary ?? null;
            $isDelivered = (strtolower($summary->status ?? $data->status ?? '') === 'delivered') || ($data->is_delivered ?? false);

            $html = "
                <div class='mb-2 pb-2 border-bottom' style='font-size: 0.85rem;'>
                    <div class='d-flex justify-content-between align-items-center mb-1'>
                        <span>Nomor Resi: <strong class='text-primary'>" . esc($airwayBill) . "</strong></span>
                        <span class='badge bg-light text-dark border'>" . strtoupper(esc($kodeKurir)) . "</span>
                    </div>
            ";

            // Auto-update status transaksi jika paket sudah sampai / diterima
            if ($isDelivered) {
                $now = date('Y-m-d H:i:s');
                if ((int)$trx->status < 3) {
                    $this->func->updateData('transaksi', [
                        'status'     => 3,
                        'tgl_update' => $now,
                        'selesai'    => $now
                    ], ['id' => $trx->id]);
                }

                $penerima    = strtoupper(esc($summary->receiver ?? $data->receiver ?? '-'));
                $tglDiterima = !empty($summary->date) ? esc($summary->date) : '-';

                $html .= "
                    <div class='alert alert-success p-2 mt-2 mb-0' style='font-size: 0.825rem;'>
                        <div class='fw-bold text-success mb-1'><i class='fas fa-check-circle me-1'></i> PAKET TELAH DITERIMA</div>
                        <div>Penerima: <strong>{$penerima}</strong></div>
                        <div>Tanggal: {$tglDiterima}</div>
                    </div>
                ";
            } else {
                $statusText = strtoupper(esc($summary->status_description ?? $summary->status ?? 'PAKET SEDANG DALAM PENGIRIMAN'));
                $html .= "
                    <div class='alert alert-info p-2 mt-2 mb-0 fw-semibold text-info' style='font-size: 0.825rem;'>
                        <i class='fas fa-truck me-1'></i> {$statusText}
                    </div>
                ";
            }

            $html .= "</div>"; // End Top Info

            // Timeline Riwayat Manifest / Tracking
            $html .= "
                <div class='timeline-container mt-3' style='font-size: 0.825rem;'>
                    <div class='row fw-bold bg-light py-2 px-1 border-bottom me-0 ms-0 mb-2'>
                        <div class='col-4 col-md-3'>Tanggal</div>
                        <div class='col-8 col-md-9'>Keterangan</div>
                    </div>
            ";

            if (!empty($history)) {
                foreach ($history as $m) {
                    $tgl  = esc($m->date ?? $m->manifest_date ?? '-');
                    $desc = esc($m->description ?? $m->manifest_description ?? '-');
                    $city = esc($m->location ?? $m->city_name ?? '');

                    $html .= "
                        <div class='row py-2 px-1 border-bottom me-0 ms-0 text-muted'>
                            <div class='col-4 col-md-3'><small>{$tgl}</small></div>
                            <div class='col-8 col-md-9'>
                                <div class='text-dark'>{$desc}</div>
                                " . (!empty($city) ? "<small class='text-secondary'>[{$city}]</small>" : "") . "
                            </div>
                        </div>
                    ";
                }
            } else {
                $html .= "
                    <div class='text-center py-3 text-muted'>
                        Belum ada riwayat pergerakan paket.
                    </div>
                ";
            }

            $html .= "</div>";

            return $this->response->setBody($html);
        }

        // Jika Nomor Resi Tidak Ditemukan / Gagal Dari API
        $pesanGagal = esc($res->message ?? "Nomor resi tidak ditemukan. Silakan tunggu beberapa saat hingga status diperbarui oleh sistem ekspedisi.");

        return $this->response->setBody("
            <div class='p-3 text-center' style='font-size: 0.85rem;'>
                <div class='mb-2'>
                    Nomor Resi: <strong class='text-danger'>" . esc($airwayBill) . "</strong>
                </div>
                <div class='text-muted small'>
                    {$pesanGagal}
                </div>
            </div>
        ");
    }

    public function uploadFotoResult($idpro = 0)
    {
        $session = session();

        // Cek Session Autentikasi
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('admin/login'));
        }

        // Ambil data fotoCopy dan fotoProduk dari Session
        $sessionFotoCopy   = $session->get('fotoCopy');
        $sessionFotoProduk = $session->get('fotoProduk');
        $fotoCopy          = [];

        if (isset($sessionFotoCopy) && is_array($sessionFotoCopy) && count($sessionFotoCopy) > 0) {
            $fotoCopy = $sessionFotoCopy;
            if (isset($sessionFotoProduk) && is_array($sessionFotoProduk) && count($sessionFotoProduk) > 0) {
                $fotoCopy = array_merge($fotoCopy, $sessionFotoProduk);
            }

            $foto = $this->func->getFotoUpload(null, $fotoCopy);
        } else {
            $foto = $this->func->getFotoUpload($idpro);
        }


        // Render View ke dalam Variabel String
        $htmlData = view('admin/produk_upload_foto', [
            'idproduk' => (int) $idpro,
            'foto' => $foto
        ]);

        // Return Response JSON CI4
        return $this->response->setJSON([
            'success' => true,
            'data'    => $htmlData,
            'token'   => csrf_hash()
        ]);
    }
    
    public function uploadFotoProduk()
    {
        $session = session();
        $request = \Config\Services::request();

        // Cek Session Autentikasi
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        // Gunakan method $request->is('post') bawaan CI4
        if ($request->is('post')) {
            $idproduk = (int) $request->getPost('idproduk');
            $file     = $request->getFile('fotoProduk');

            // Cek jumlah foto untuk menentukan jenis (0/1)
            $count = $this->func->countData('upload', ['idproduk' => $idproduk]);
            $jenis = ($count > 0) ? 0 : 1;

            // Validasi File Upload
            if ($file && $file->isValid() && !$file->hasMoved()) {

                // 1. Ambil Ekstensi dengan Fallback Aman
                $ext = $file->getClientExtension();
                if (empty($ext)) {
                    $ext = $file->getExtension();
                }
                if (empty($ext)) {
                    $ext = $file->guessExtension();
                }
                if (empty($ext)) {
                    $ext = 'jpg'; // Fallback terakhir jika ekstensi tidak terdeteksi
                }

                // 2. Format Nama File Baru
                $adminId = $session->get('admin_id') ?? 'admin';
                $newName = $adminId . date('YmdHis') . rand(100, 999) . '.' . $ext;

                // 3. Pastikan Folder Tujuan Ada
                $uploadPath = FCPATH . 'cdn/uploads/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // 4. Pindahkan File
                if ($file->move($uploadPath, $newName)) {

                    // Insert Database
                    $data = [
                        'idproduk' => $idproduk,
                        'jenis'    => $jenis,
                        'nama'     => $newName,
                        'tgl'      => date('Y-m-d H:i:s')
                    ];

                    $insertID = $this->func->insertData('upload', $data);

                    // Simpan Session Foto
                    $fotoProduk   = $session->get('fotoProduk') ?? [];
                    $fotoProduk[] = $insertID;
                    $session->set('fotoProduk', $fotoProduk);

                    if ($idproduk === 0) {
                        $upl = $session->get('uploadedPhotos') ?? 0;
                        $session->set('uploadedPhotos', $upl + 1);
                    }

                    return $this->response->setJSON([
                        'success' => true,
                        'token'   => csrf_hash()
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'msg'     => 'Gagal memindahkan file ke folder cdn/uploads/',
                        'token'   => csrf_hash()
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'msg'     => 'Error: ' . ($file ? $file->getErrorString() : 'File tidak valid atau tidak diunggah'),
                    'token'   => csrf_hash()
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'Invalid Request Method',
            'token'   => csrf_hash()
        ]);
    }

    public function hapusFotoProduk($id)
    {
        $session = session();
        $request = \Config\Services::request();
        $db      = \Config\Database::connect();

        // Cek Session Autentikasi
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('admin/login'));
        }

        $sessionFotoCopy   = $session->get('fotoCopy') ?? [];
        $sessionFotoProduk = $session->get('fotoProduk') ?? [];

        if ($id === "all") {
            $idproduk = (int) $request->getPost('idproduk');

            $fotoUpload = $this->func->getFotoUploadExceptCopy($idproduk, $sessionFotoCopy);

            foreach ($fotoUpload as $foto) {
                
                $countDuplicate = $this->func->countData('upload', ['nama'=>$foto->nama, 'id !=' => $foto->id]);
                
                if ($countDuplicate === 0) {
                    $filePath = FCPATH . 'cdn/uploads/' . $foto->nama;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Reset Session
            $session->set('uploadedPhotos', 0);
            $session->remove('fotoProduk');

            // Hapus Records Database
            if (!empty($sessionFotoCopy)) {
                $this->func->deleteData('upload', $idproduk, 'idproduk', ['id'=>$sessionFotoCopy]);
                $session->remove('fotoCopy');
            } else {
                $this->func->deleteData('upload', $idproduk, 'idproduk');
            }

        } else {
            $id = (int) $id;

            if (!in_array($id, $sessionFotoCopy)) {
                // Ambil Nama File dari GlobalData / Function Helper
                $nama = $this->func->getUpload($id, 'nama');

                $countDuplicate = $this->func->countData('upload', ['nama'=>$nama, 'id !='=>$id]);

                if ($countDuplicate === 0) {
                    $filePath = FCPATH . 'cdn/uploads/' . $nama;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                // Hapus dari Database
                $this->func->deleteData('upload', $id, 'id');

                // Unset dari Array Session fotoProduk
                if (($key = array_search($id, $sessionFotoProduk)) !== false) {
                    unset($sessionFotoProduk[$key]);
                    $session->set('fotoProduk', array_values($sessionFotoProduk));
                }
            } else {
                // Unset dari Array Session fotoCopy
                if (($key = array_search($id, $sessionFotoCopy)) !== false) {
                    unset($sessionFotoCopy[$key]);
                    $session->set('fotoCopy', array_values($sessionFotoCopy));
                }
            }

            // Decrement Session uploadedPhotos
            $uploadedPhotos = $session->get('uploadedPhotos') ?? 0;
            $session->set('uploadedPhotos', max(0, $uploadedPhotos - 1));
        }

        return $this->response->setJSON([
            'success' => true,
            'token'   => csrf_hash()
        ]);
    }

    public function jadikanFotoUtama($id)
    {
        $session = session();
        $request = \Config\Services::request();

        // Cek Session Autentikasi
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        $idproduk = (int) $request->getPost('idproduk');
        $id = (int) $id;

        $this->func->updateData('upload', ['jenis'=>0], ['idproduk'=>$idproduk, 'jenis'=>1]);

        $this->func->updateData('upload', ['jenis'=>1], ['id'=>$id]);

        return $this->response->setJSON([
            'success' => true,
            'token'   => csrf_hash()
        ]);
    }

    public function tambahProduk()
    {
        $session = session();
        $request = \Config\Services::request();

        // Cek Session Autentikasi
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        if ($request->is('post')) {
            $uploadedPhotos = $session->get('uploadedPhotos') ?? 0;

            if ($uploadedPhotos != 0) {
                $tgl = date('Y-m-d H:i:s');
                
                // 1. Ambil semua data POST
                $postData = $this->request->getPost();
                unset($postData[csrf_token()]);

                // === PEMBERSIHAN KHUSUS (Mencegah Error 'undefined') ===
                $cleanData = [];
                foreach ($postData as $key => $value) {
                    // Abaikan jika key adalah string 'undefined' atau berupa angka indeks kosong
                    if ($key !== 'undefined' && $key !== '' && !is_numeric($key)) {
                        // Jika nilai bertipe string 'undefined', netralkan jadi string kosong
                        $cleanData[$key] = ($value === 'undefined') ? '' : $value;
                    }
                }
                $postData = $cleanData;
                // ====================================================

                // 2. Olah Data Spesifikasi menjadi String (Key: Value|Key: Value)
                $spekItems = [];
                if (!empty($postData['spek_key']) && is_array($postData['spek_key'])) {
                    foreach ($postData['spek_key'] as $index => $keyName) {
                        $keyName = trim($keyName);
                        $valName = trim($postData['spek_val'][$index] ?? '');
                        
                        if ($keyName !== '' && $valName !== '') {
                            $spekItems[] = $keyName . ': ' . $valName;
                        }
                    }
                }
                $postData['spesifikasi'] = implode('|', $spekItems);
                unset($postData['spek_key'], $postData['spek_val']);

                // 3. Olah Data Detail Ukuran menjadi String (Key: Value|Key: Value)
                $ukuranItems = [];
                if (!empty($postData['ukuran_key']) && is_array($postData['ukuran_key'])) {
                    foreach ($postData['ukuran_key'] as $index => $keyName) {
                        $keyName = trim($keyName);
                        $valName = trim($postData['ukuran_val'][$index] ?? '');
                        
                        if ($keyName !== '' && $valName !== '') {
                            $ukuranItems[] = $keyName . ': ' . $valName;
                        }
                    }
                }
                $postData['ukuran'] = implode('|', $ukuranItems);
                unset($postData['ukuran_key'], $postData['ukuran_val']);

                // 4. Pastikan kolom variasi & subvariasi aman jika tidak ada di POST
                if (!isset($postData['variasi'])) {
                    $postData['variasi'] = '';
                }
                if (!isset($postData['subvariasi'])) {
                    $postData['subvariasi'] = '';
                }

                $deskripsi = $request->getPost('deskripsi') ?? '';
                $postData['deskripsi'] = trim(strip_tags($deskripsi));
                
                // Bersihkan nama produk (Slug URL)
                helper('text');
                $namaProduk = $request->getPost('nama') ?? '';
                $string     = url_title($namaProduk, '-', true); 

                $extraData = [
                    'tgl_buat'   => $tgl,
                    'tgl_update' => $tgl,
                    'url'        => $string . '-' . date('His')
                ];

                $data = array_merge($postData, $extraData);

                // Insert ke tabel produk & ambil insert ID
                $insertid = $this->func->insertData('produk', $data);

                // Update foto yang baru diunggah
                $fotoProduk = $session->get('fotoProduk') ?? [];
                if (!empty($fotoProduk)) {
                    $this->func->updateData('upload', ['idproduk' => $insertid], ['id' => $fotoProduk]);
                    $session->remove('fotoProduk');
                }

                // Duplikasi foto dari fotoCopy session
                $fotoCopy = $session->get('fotoCopy') ?? [];
                if (!empty($fotoCopy)) {
                    foreach ($fotoCopy as $val) {
                        $up = $this->func->getUpload($val, 'semua');
                        if ($up) {
                            $this->func->insertData('upload', [
                                'idproduk' => $insertid,
                                'jenis'    => $up->jenis,
                                'nama'     => $up->nama,
                                'tgl'      => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                    $session->remove('fotoCopy');
                }

                // Reset counter foto terunggah
                $session->remove('uploadedPhotos');

                return $this->response->setJSON([
                    'success' => true,
                    'msg'     => 'berhasil',
                    'id'      => $insertid,
                    'token'   => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'foto wajib di isi: ' . $uploadedPhotos,
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'form not submitted!',
            'token'   => csrf_hash()
        ]);
    }

    public function hapusProduk()
    {
        $session = session();
        $request = \Config\Services::request();

        // 1. Cek Autentikasi Session CI4
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        // 2. Ambil ID dari Request POST
        $id = $request->getPost('id');

        if ($id) {
            // Pastikan ID berupa integer / aman dari manipulasi
            $id = (int)$id;

            // 3. AMBIL DATA FOTO SEBELUM DIHAPUS DARI DATABASE
            // Menyesuaikan pemanggilan helper/model $this->func kamu
            $uploads = $this->func->getUpload($id, 'semua', 'idproduk', true); 

            if (!empty($uploads)) {
                // Jika return berupa array dari banyak objek foto
                if (is_array($uploads)) {
                    foreach ($uploads as $up) {
                        $filePath = FCPATH . 'cdn/uploads/' . $up->nama;
                        if (!empty($up->nama) && file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                } 
                // Jika return hanya single object
                else if (is_object($uploads)) {
                    $filePath = FCPATH . 'uploads/' . $uploads->nama;
                    if (!empty($uploads->nama) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // 4. Hapus data dari database
            $this->func->deleteData('produk', ['id' => $id]);
            $this->func->deleteData('produk_variasi', ['idproduk' => $id]);
            $this->func->deleteData('upload', ['idproduk' => $id]);

            return $this->response->setJSON([
                'success' => true,
                'msg'     => 'berhasil menghapus',
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'form not submitted!',
            'token'   => csrf_hash()
        ]);
    }

    public function variasiform($idpro = 0)
    {
        $session = session();

        // 1. Cek Autentikasi Session CI4
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        $vars = $this->func->getProdukVariasi($idpro);
        $variasi = [];

        foreach ($vars as $res) {
            $variasi[] = $res->idwarna;
        }

        $variasi = array_values(array_unique($variasi));

        // 3. Render view dengan membawa data yang telah disiapkan
        $data = view('admin/produk_form_variasi', [
            'id'          => $idpro,
            'variasi'     => $variasi,
            'produkvariasi' => $vars
        ]);

        // 4. Return response JSON
        return $this->response->setJSON([
            'success' => true,
            'data'    => $data,
            'token'   => csrf_hash()
        ]);
    }

    public function variansave($id = 0)
    {
        $session = session();
        $request = \Config\Services::request();

        // 1. Cek Autentikasi Session CI4
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        $hargaPost = $request->getPost('harga');

        if ($hargaPost && is_array($hargaPost)) {
            $stokTotal = 0;
            $db        = \Config\Database::connect();
            
            $stokPost         = $request->getPost('stok') ?? [];

            foreach ($hargaPost as $ids => $harga) {
                $stokVal    = (int)($stokPost[$ids] ?? 0);
                $stokTotal += $stokVal;

                $data = [
                    'stok'          => $stokVal,
                    'harga'         => $harga,
                    'tgl'           => date('Y-m-d H:i:s')
                ];

                // Update ke tabel produkvariasi
                $this->func->updateData('produk_variasi', $data, ['id'=>$ids]);
            }

            // Update akumulasi total stok ke tabel produk utama
            $this->func->updateData('produk', ['stok' => $stokTotal], ['id'=>$id]);

            return $this->response->setJSON([
                'success' => true,
                'msg'     => 'berhasil',
                'stok'    => $stokTotal,
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'form not submitted!',
            'token'   => csrf_hash()
        ]);
    }

    public function varianadd()
    {
        $session = session();
        $request = \Config\Services::request();

        // 1. Cek Autentikasi Session CI4
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'Unauthenticated',
                'token'   => csrf_hash()
            ]);
        }

        $nama   = $request->getPost('nama');
        $produk = $request->getPost('produk');

        if ($nama && $produk) {
            $tglNow = date('Y-m-d H:i:s');

            // Insert menggunakan helper/model func kamu sesuai kode yang kamu sediakan
            $insertid = $this->func->insertData('variasi_warna', [
                'nama' => $nama,
                'tgl'  => $tglNow
            ]);

            // 3. Ambil data produk utama
            $dataProduk  = $this->func->getProdukById($produk, 'semua');
            $hargaProduk = $dataProduk->harga ?? 0;

            $this->func->insertData('produk_variasi', [
                'idproduk' => $produk,
                'idwarna'    => $insertid,
                'harga'    => $hargaProduk,
                'tgl'      => $tglNow
            ]);

            return $this->response->setJSON([
                'success' => true,
                'msg'     => 'berhasil',
                'id'      => $insertid,
                'token'   => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'form not submitted!',
            'token'   => csrf_hash()
        ]);
    }

    public function varianhapus()
    {
        // 1. Cek Sesi Login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('admin/login');
        }

        // 2. Ambil data POST menggunakan HTTP Request Service CI4
        $idproduk = $this->request->getPost('produk');
        $idwarna  = $this->request->getPost('id');

        if (!empty($idproduk) && !empty($idwarna)) {
            $this->func->deleteData('produk_variasi', ['idproduk'=>$idproduk, 'idwarna'=>$idwarna]);

            $response = [
                'success' => true,
                'msg'     => 'berhasil',
                'token'   => csrf_hash() // Service helper CSRF bawaan CI4
            ];
        } else {
            $response = [
                'success' => false,
                'msg'     => 'Data produk atau variasi tidak valid!',
                'token'   => csrf_hash()
            ];
        }

        // 4. Return response JSON menggunakan fitur bawaan Controller CI4
        return $this->response->setJSON($response);
    }

    public function updateproduk()
{
    $session = session();
    $request = \Config\Services::request();

    // 1. Cek Session Autentikasi
    if (!$session->get('isLoggedIn')) {
        return $this->response->setJSON([
            'success' => false,
            'msg'     => 'Unauthenticated',
            'token'   => csrf_hash()
        ]);
    }

    if ($request->is('post')) {
        $id = $request->getPost('id');

        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'msg'     => 'ID produk tidak ditemukan!',
                'token'   => csrf_hash()
            ]);
        }

        $tgl = date('Y-m-d H:i:s');

        // 2. Ambil semua data POST
        $postData = $this->request->getPost();
        
        // Hapus token CSRF & ID agar tidak merusak query update
        unset($postData[csrf_token()], $postData['id']);

        // === PEMBERSIHAN KHUSUS (Mencegah Error 'undefined') ===
        $cleanData = [];
        foreach ($postData as $key => $value) {
            // Abaikan jika key adalah string 'undefined' atau berupa angka indeks kosong
            if ($key !== 'undefined' && $key !== '' && !is_numeric($key)) {
                // Jika nilai bertipe string 'undefined', netralkan jadi string kosong
                $cleanData[$key] = ($value === 'undefined') ? '' : $value;
            }
        }
        $postData = $cleanData;
        // ====================================================

        // 3. Olah Data Spesifikasi menjadi String (Key: Value|Key: Value)
        $spekItems = [];
        if (!empty($postData['spek_key']) && is_array($postData['spek_key'])) {
            foreach ($postData['spek_key'] as $index => $keyName) {
                $keyName = trim($keyName);
                $valName = trim($postData['spek_val'][$index] ?? '');

                if ($keyName !== '' && $valName !== '') {
                    $spekItems[] = $keyName . ': ' . $valName;
                }
            }
        }
        $postData['spesifikasi'] = implode('|', $spekItems);
        unset($postData['spek_key'], $postData['spek_val']);

        // 4. Olah Data Detail Ukuran menjadi String (Key: Value|Key: Value)
        $ukuranItems = [];
        if (!empty($postData['ukuran_key']) && is_array($postData['ukuran_key'])) {
            foreach ($postData['ukuran_key'] as $index => $keyName) {
                $keyName = trim($keyName);
                $valName = trim($postData['ukuran_val'][$index] ?? '');

                if ($keyName !== '' && $valName !== '') {
                    $ukuranItems[] = $keyName . ': ' . $valName;
                }
            }
        }
        $postData['ukuran'] = implode('|', $ukuranItems);
        unset($postData['ukuran_key'], $postData['ukuran_val']);

        // 5. Pastikan kolom variasi & subvariasi aman jika tidak ada di POST
        if (!isset($postData['variasi'])) {
            $postData['variasi'] = '';
        }
        if (!isset($postData['subvariasi'])) {
            $postData['subvariasi'] = '';
        }

        // 6. Sanitasi Deskripsi
        $deskripsi = $request->getPost('deskripsi') ?? '';
        $postData['deskripsi'] = trim(strip_tags($deskripsi));

        // 7. Genereate Slug URL Baru jika Nama Produk Diubah
        helper('text');
        $namaProduk = $request->getPost('nama') ?? '';
        $string     = url_title($namaProduk, '-', true);

        $extraData = [
            'tgl_update' => $tgl,
            'url'        => $string . '-' . date('His')
        ];

        $data = array_merge($postData, $extraData);

        // 8. Update Data Utama Produk
        $this->func->updateData('produk', $data, ['id' => $id]);

        // 9. Update Foto yang Baru Diunggah saat Edit (Jika ada)
        $fotoProduk = $session->get('fotoProduk') ?? [];
        if (!empty($fotoProduk)) {
            $this->func->updateData('upload', ['idproduk' => $id], ['id' => $fotoProduk]);
            $session->remove('fotoProduk');
        }

        // 10. Duplikasi foto dari fotoCopy session (Jika ada)
        $fotoCopy = $session->get('fotoCopy') ?? [];
        if (!empty($fotoCopy)) {
            foreach ($fotoCopy as $val) {
                $up = $this->func->getUpload($val, 'semua');
                if ($up) {
                    $this->func->insertData('upload', [
                        'idproduk' => $id,
                        'jenis'    => $up->jenis,
                        'nama'     => $up->nama,
                        'tgl'      => date('Y-m-d H:i:s')
                    ]);
                }
            }
            $session->remove('fotoCopy');
        }

        // Reset Counter Foto Terunggah
        $session->remove('uploadedPhotos');

        return $this->response->setJSON([
            'success' => true,
            'msg'     => 'berhasil',
            'id'      => $id,
            'token'   => csrf_hash()
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'msg'     => 'form not submitted!',
        'token'   => csrf_hash()
    ]);
}
}