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
              $listPesanan = $this->func->getAdminTransactionsByStatus(3, $page, $cari);
              $data = [
                  'completedOrders' => $listPesanan,
                  'pager'           => $this->func->pager,
                  'page'            => $page,
                  'cari'            => $cari
              ];
              $res = view('admin/pesananselesai', $data);
              break;

          case 'batal':
          case 'dibatalkan':
              // Status 4: Dibatalkan
              $listPesanan = $this->func->getAdminTransactionsByStatus(4, $page, $cari);
              $data = [
                  'cancelledOrders' => $listPesanan,
                  'pager'           => $this->func->pager,
                  'page'            => $page,
                  'cari'            => $cari
              ];
              $res = view('admin/pesananbatal', $data);
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
    
}