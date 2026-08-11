<?php

namespace App\Controllers\Client;
use App\Controllers\BaseController;

class TrackPesanan extends BaseController
{
    public function index()
    {
        $data = $this->data;
        $data['nama'] = $data['set']->nama . ' – ' . 'Cek Status Pesanan';

        return view('client/tracking_order', $data);
    }

    public function cek()
    {
        // Ambil data POST
        $orderid = $this->request->getPost('orderid');

        if (!empty($orderid)) {
            // Bersihkan input dan ambil idbayar
            $cleanOrderId = $this->func->clean_string($orderid);
            $idbayar = (int) $this->func->getPembayaran($cleanOrderId, 'id', 'invoice');

            // 2. Pastikan idbayar ditemukan (> 0) sebelum mengambil data transaksi
            if ($idbayar > 0) {
                $trxList = $this->func->getTransaksiByPaymentId($idbayar);
                
                // Ambil transaksi pertama jika ada
                $trx = $trxList[0] ?? null;

                if ($trx && (int)($trx->id ?? 0) > 0 && (int)($trx->usrid_temp ?? 0) > 0 && (int)($trx->usrid ?? 0) === 0) {
                    session()->set('usrid_temp', $trx->usrid_temp);

                    return $this->response->setJSON([
                        'success'  => true,
                        'order_id' => $trx->orderid,
                        'token'    => csrf_hash()
                    ]);
                }
            }

            // Jika idbayar tidak ditemukan / transaksi tidak valid
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
                'token'   => csrf_hash()
            ]);
        }

        // Response jika gagal
        return $this->response->setJSON([
            'success' => false,
            'token'   => csrf_hash()
        ]);
    }
}