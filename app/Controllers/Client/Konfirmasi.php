<?php

namespace App\Controllers\Client;
use App\Controllers\BaseController;

class Konfirmasi extends BaseController
{
    public function index(): string
    {
        $data = $this->data;
        $data['nama'] = $data['set']->nama . ' – ' . 'Konfirmasi Pembayaran Pesanan';

        return view('client/konfirmasi', $data);
    }

    public function kirim()
    {
        $invoice = $this->request->getPost('invoice');

        if (!empty($invoice)) {
            // Clean string invoice & cari data pembayaran
            $cleanInvoice = $this->func->clean_string($invoice);
            $bayar        = $this->func->getPembayaran($cleanInvoice, 'semua', 'invoice');

            // Validasi data transaksi non-member
            if ($bayar && (int)($bayar->id ?? 0) > 0 && (int)($bayar->usrid_temp ?? 0) > 0 && (int)($bayar->usrid ?? 0) === 0) {
                
                $file = $this->request->getFile('bukti');

                // Cek apakah ada file yang diunggah dan valid
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $allowedExtensions = ['gif', 'jpg', 'jpeg', 'png', 'pdf'];
                    $extension         = strtolower($file->getClientExtension());

                    if (in_array($extension, $allowedExtensions)) {
                        // Format nama file: usrid_tempidbayarYYYYMMDDHHIISS.ext
                        $fileName = $bayar->usrid_temp . $bayar->id . date('YmdHis') . '.' . $extension;

                        // Pindahkan file ke direktori target (public/cdn/konfirmasi)
                        $file->move(FCPATH . 'cdn/konfirmasi/', $fileName);

                        // Simpan data ke tabel konfirmasi
                        $db = \Config\Database::connect();
                        $db->table('konfirmasi')->insert([
                            'tgl'     => date('Y-m-d H:i:s'),
                            'idbayar' => $bayar->id,
                            'bukti'   => $fileName
                        ]);

                        return redirect()->to('konfirmasi?result=sukses');
                    }

                    return redirect()->to('konfirmasi?result=gagal&msg=' . urlencode('Format file tidak diizinkan.'));
                }

                return redirect()->to('konfirmasi?result=gagal&msg=' . urlencode('Upload file gagal atau file rusak.'));
            }
        }

        return redirect()->to('konfirmasi?result=gagal');
    }
}