<?php

namespace App\Models;

use CodeIgniter\Model;

class GlobalData extends Model
{
    public function demo()
    {
        return false;
    }

    public function globalset($data)
    {
        $db      = db_connect();
        $builder = $db->table('setting');

        if ($data !== 'semua') {
            $builder->where('field', $data);
        }

        $res = $builder->get();

        if ($data === 'semua') {
            $result = [];
            foreach ($res->getResult() as $re) {
                $result[$re->field] = $re->value;
            }
            return (object) $result;
        } else {
            $result = '';
            foreach ($res->getResult() as $re) {
                $result = $re->value;
            }
            return $result;
        }
    }

    public function getKategori()
    {
        return $this->db->table('kategori')
                        ->get()
                        ->getResult();
    }

    public function getJumlahProdukPerKategori()
{
    return $this->db->table('kategori')
        ->select('
            kategori.id,
            kategori.nama,
            COUNT(produk.id) AS jumlah_produk
        ')
        ->join(
            'produk',
            'produk.idcat = kategori.id',
            'left'
        )
        ->groupBy('kategori.id')
        ->get()
        ->getResult();
}


    public function getKeranjang()
    {
        $db      = db_connect();
        $builder = $db->table('transaksi_produk');

        if (session()->has('usrid')) {
            $builder->where('usrid', session('usrid'));
        } else {
            $builder->where('usrid', 'xzact');
        }

        $builder->where('idtransaksi', 0);

        return $builder->countAllResults();
    }

    public function getWishlistCount()
    {
        $db      = db_connect();
        $builder = $db->table('wishlist');

        $usrid = session()->has('usrid') ? session('usrid') : 0;

        $builder->where('usrid', $usrid);

        return $builder->countAllResults();
    }

    public function cekLogin()
    {
        $session = session();

        if ($session->has('usrid')) {
            $user = $this->db->table('user_data')
                            ->where('id', $session->get('usrid'))
                            ->get()
                            ->getRow(); // bisa getRowArray() kalau mau array

            if ($user && $user->id > 0) {
                // update last login
                $this->db->table('user_data')
                        ->where('id', $session->get('usrid'))
                        ->update(['tgl' => date('Y-m-d H:i:s')]);

                // sync level session
                // if ($session->get('lvl') != $user->level) {
                //     $session->set('lvl', $user->level);
                // }

                return $session->get('usrid');
            } else {
                $session->destroy();
                return redirect()->to('/home/signin')->send();
                exit;
            }
        } else {
            return 0;
        }
    }

    public function getPromoAktif()
    {
        return $this->db->table('promo')
            ->where('tgl <=', date('Y-m-d H:i:s'))
            ->where('tgl_selesai >=', date('Y-m-d H:i:s'))
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();
    }

    public function getProdukUnggulan($limit = 4)
    {
        // ===============================
        // 1. PRODUK TERLARIS
        // ===============================
        $terlaris = $this->builder('transaksi_produk tp')
        ->select('
            p.id,
            p.nama,
            p.harga,
            p.harga_coret,
            u.nama as nama_gambar,
            SUM(tp.jumlah) as total_terjual
        ')
        ->join('produk p', 'p.id = tp.idproduk')
        ->join('upload u', 'u.idproduk = p.id', 'left')
        ->groupBy([
            'p.id',
            'p.nama',
            'p.harga',
            'u.nama'
        ])
        ->having('total_terjual >', 5)
        ->orderBy('total_terjual', 'DESC')
        ->limit($limit)
        ->get()
        ->getResult();

        // ambil ID yg sudah kepakai
        $usedIds = array_column($terlaris, 'id');

        // ===============================
        // 2. JIKA KURANG DARI LIMIT
        // ===============================
        if (count($terlaris) < $limit) {

            $sisa = $limit - count($terlaris);

            $builder = $this->builder('produk p');

            $builder->select('
                    p.id,
                    p.nama,
                    p.harga,
                    p.harga_coret,
                    u.nama as nama_gambar
                ')
                ->join('upload u', 'u.idproduk = p.id', 'left')
                ->where('p.is_unggulan', 1);

            // hindari produk yg sudah diambil
            if (!empty($usedIds)) {
                $builder->whereNotIn('p.id', $usedIds);
            }

            $unggulanTambahan = $builder
                ->limit($sisa)
                ->get()
                ->getResult();

            // gabungkan
            $terlaris = array_merge($terlaris, $unggulanTambahan);
        }

        return $terlaris;
    }

    public function getProdukTerbaru($limit = 8)
    {
        return $this->db->table('produk p')
            ->select("
                p.id,
                p.nama,
                p.harga,
                p.tgl_buat,
                p.harga_coret,
                (
                    SELECT u.nama
                    FROM upload u
                    WHERE u.idproduk = p.id
                    LIMIT 1
                ) as gambar
            ")
            ->orderBy('p.tgl_buat', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

}

