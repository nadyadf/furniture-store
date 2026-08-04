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
                // Status 1: Perlu Dikirim / Dikemas
                $listPesanan = $this->func->getAdminTransactionsByStatus(1, $page, $cari);
                $data = [
                    'packedOrders' => $listPesanan,
                    'pager'        => $this->func->pager,
                    'page'         => $page,
                    'cari'         => $cari
                ];
                $res = view('admin/pesanandikemas', $data);
                break;

            case 'dikirim':
                // Status 2: Dikirim
                $listPesanan = $this->func->getAdminShippedTransactions($page, $cari);
                $data = [
                    'shippedOrders' => $listPesanan,
                    'pager'         => $this->func->pager,
                    'page'          => $page,
                    'cari'          => $cari
                ];
                $res = view('admin/pesanandikirim', $data);
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
}