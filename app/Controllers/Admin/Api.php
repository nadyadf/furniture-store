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
              $listPesanan = $this->func->getPendingShipmentOrders($page, $cari);
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

    
}