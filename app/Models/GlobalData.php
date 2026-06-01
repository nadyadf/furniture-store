<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;
use Config\Database;

class GlobalData extends Model
{
    protected $encryption;
    protected $DBGroup       = 'default';
    protected $table         = 'produk';   // wajib ada
    protected $primaryKey    = 'id';

    protected $returnType    = 'object';   // WAJIB supaya paginate tidak error
    protected $useSoftDeletes = false;

    protected $allowedFields = []; // boleh kosong kalau hanya read

    // protected $pager;

    public function __construct()
    {
        parent::__construct(); // WAJIB
         $this->encryption = Services::encrypter();
         $this->db = Database::connect();
    }

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

    function tema($pilih = null)
    {
        $warna = [

            // 🔥 Tema utama (gradient modern + furniture feel)
            1 => [

                // ✅ 0. Natural Wood (WAJIB - sesuai request kamu)
                [
                    "light" => "linear-gradient(135deg, #8b7355, #a68a64)",
                    "hover" => "linear-gradient(135deg, #7B5A3C, #8b7355)",
                    "testimoni" => "radial-gradient(circle, rgba(234,219,200,0.5), rgba(255,255,255,0.8))",
                    "foot" => "#EADBC8",
                    "foot_gradient" => "linear-gradient(0deg, #EADBC8, #f5f5f5)"
                ],

                // ✅ 1. Dark Elegant (furniture premium)
                [
                    "light" => "linear-gradient(135deg, #2c2c2c, #4a4a4a)",
                    "hover" => "linear-gradient(135deg, #1a1a1a, #2c2c2c)",
                    "testimoni" => "radial-gradient(circle, rgba(200,200,200,0.2), rgba(255,255,255,0.6))",
                    "foot" => "#dcdcdc",
                    "foot_gradient" => "linear-gradient(0deg, #dcdcdc, #f5f5f5)"
                ],

                // ✅ 2. Soft Beige Minimalist
                [
                    "light" => "linear-gradient(135deg, #d6c3a3, #f3e9dc)",
                    "hover" => "linear-gradient(135deg, #c2a785, #e6d5c3)",
                    "testimoni" => "radial-gradient(circle, rgba(240,230,210,0.6), rgba(255,255,255,0.9))",
                    "foot" => "#f3e9dc",
                    "foot_gradient" => "linear-gradient(0deg, #f3e9dc, #ffffff)"
                ],

                // ✅ 3. Olive Natural (eco furniture vibe)
                [
                    "light" => "linear-gradient(135deg, #6b705c, #a5a58d)",
                    "hover" => "linear-gradient(135deg, #4f553d, #6b705c)",
                    "testimoni" => "radial-gradient(circle, rgba(200,210,180,0.4), rgba(255,255,255,0.8))",
                    "foot" => "#e9edc9",
                    "foot_gradient" => "linear-gradient(0deg, #e9edc9, #ffffff)"
                ],

                // ✅ 4. Warm Terracotta
                [
                    "light" => "linear-gradient(135deg, #c97b63, #e6a57e)",
                    "hover" => "linear-gradient(135deg, #a65a45, #c97b63)",
                    "testimoni" => "radial-gradient(circle, rgba(255,210,190,0.5), rgba(255,255,255,0.9))",
                    "foot" => "#ffe5d9",
                    "foot_gradient" => "linear-gradient(0deg, #ffe5d9, #ffffff)"
                ],

                // ✅ 5. Modern Gray Industrial
                [
                    "light" => "linear-gradient(135deg, #6d6875, #b5838d)",
                    "hover" => "linear-gradient(135deg, #4a4a52, #6d6875)",
                    "testimoni" => "radial-gradient(circle, rgba(220,220,230,0.4), rgba(255,255,255,0.8))",
                    "foot" => "#f0efeb",
                    "foot_gradient" => "linear-gradient(0deg, #f0efeb, #ffffff)"
                ],
            ],

            // 🎯 Tema solid (tanpa gradient berat, lebih clean UI)
            2 => [

                // ✅ 0. Classic Wood
                [
                    "light" => "#8b7355",
                    "hover" => "#7B5A3C",
                    "testimoni" => "#f5e9dc",
                    "foot" => "#EADBC8",
                    "foot_gradient" => "linear-gradient(0deg, #EADBC8, #ffffff)"
                ],

                // ✅ 1. Clean White Minimal
                [
                    "light" => "#ffffff",
                    "hover" => "#f1f1f1",
                    "testimoni" => "#fafafa",
                    "foot" => "#f5f5f5",
                    "foot_gradient" => "linear-gradient(0deg, #f5f5f5, #ffffff)"
                ],

                // ✅ 2. Dark Mode Elegant
                [
                    "light" => "#2c2c2c",
                    "hover" => "#1a1a1a",
                    "testimoni" => "#3a3a3a",
                    "foot" => "#e0e0e0",
                    "foot_gradient" => "linear-gradient(0deg, #e0e0e0, #ffffff)"
                ],

                // ✅ 3. Soft Green Natural
                [
                    "light" => "#a5a58d",
                    "hover" => "#6b705c",
                    "testimoni" => "#e9edc9",
                    "foot" => "#f0f4ec",
                    "foot_gradient" => "linear-gradient(0deg, #f0f4ec, #ffffff)"
                ],

                // ✅ 4. Warm Sand
                [
                    "light" => "#d4a373",
                    "hover" => "#b08968",
                    "testimoni" => "#faedcd",
                    "foot" => "#fefae0",
                    "foot_gradient" => "linear-gradient(0deg, #fefae0, #ffffff)"
                ],
            ]
        ];

        $temawarna = $this->globalset("tema_default") ?? 1;

        if ($pilih !== null) {
            return (object) $warna[$temawarna][$pilih];
        }

        return $warna[$temawarna];
    }

    public function getKategori($id = null, $field = "semua", $column = "id")
    {
        $builder = $this->db->table('kategori');

        // kalau id kosong → ambil semua
        if ($id === null) {
            return $builder->get()->getResult();
        }

        // kalau ada id → ambil satu data
        $builder->where($column, $id);
        $row = $builder->get()->getRow();

        if ($field === "semua") {

            if ($row) {
                return $row;
            }

            $fields = $this->db->getFieldData('kategori');
            $empty = new \stdClass();

            foreach ($fields as $f) {
                $empty->{$f->name} = $this->kosongan($f->type);
            }

            return $empty;
        }

        return $row->$field ?? "";
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
        $session = session();
        $builder = $this->db->table('transaksi_produk');

        $usrid      = $session->get('usrid');
        $usrid_temp = $session->get('usrid_temp');

        if (!empty($usrid) && $usrid != 0) {
            $builder->where('usrid', $usrid);

        } elseif (!empty($usrid_temp)) {
            $builder->where('usrid_temp', $usrid_temp);

        } else {
            return 0;
        }

        return $builder->where('idtransaksi', 0)
                    ->countAllResults();
    }

    public function getKeranjangFull($filter = [])
    {
        $session = session();

        $builder = $this->db->table('transaksi_produk tp');

        $builder->select('
            tp.*, 
            p.nama, 
            p.url, 
            p.subvariasi, 
            p.min_order,
            pv.idwarna,
            vw.nama as nama_warna
        ');

        $builder->join('produk p', 'p.id = tp.idproduk', 'left');
        $builder->join('produk_variasi pv', 'pv.id = tp.variasi', 'left');
        $builder->join('variasi_warna vw', 'vw.id = pv.idwarna', 'left');

        // default cart
        $builder->where('tp.idtransaksi', 0);

        // filter user/session
        if (empty($filter['skip_user'])) {

            $usrid      = $session->get('usrid');
            $usrid_temp = $session->get('usrid_temp');

            if (!empty($usrid) && $usrid != 0) {

                $builder->where('tp.usrid', $usrid);

            } elseif (!empty($usrid_temp)) {

                $builder->where('tp.usrid_temp', $usrid_temp);

            } else {

                return [];
            }
        }

        // filter tambahan fleksibel
        if (!empty($filter['id'])) {
            $builder->where('tp.id', $filter['id']);
        }

        if (!empty($filter['idtransaksi'])) {
            $builder->where('tp.idtransaksi', $filter['idtransaksi']);
        }

        if (!empty($filter['idproduk'])) {
            $builder->where('tp.idproduk', $filter['idproduk']);
        }

        $builder->orderBy('tp.gudang', 'ASC');

        $result = $builder->get()->getResult();

        // ambil gambar
        foreach ($result as $k) {

            $k->gambar = $this->db->table('upload')
                ->where('idproduk', $k->idproduk)
                ->get()
                ->getResult();
        }

        return $result;
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

    public function encode($string)
    {
        return $this->encryption->encrypt($string);
    }

    public function decode($string)
    {
        return $this->encryption->decrypt($string);
    }

    // ✅ Ambil semua produk
    // public function getSemuaProduk()
    // {
    //     return $this->db->table('produk')
    //                     ->select('produk.*, kategori.nama as nama_kategori')
    //                     ->join('kategori', 'kategori.id = produk.idcat', 'left')
    //                     ->orderBy('produk.id', 'DESC')
    //                     ->get()
    //                     ->getResult();
    // }

    // // ✅ Ambil produk berdasarkan slug kategori
    // public function getProdukByKategori($slug)
    // {
    //     return $this->db->table('produk')
    //                     ->select('produk.*, kategori.nama as nama_kategori')
    //                     ->join('kategori', 'kategori.id = produk.idcat', 'left')
    //                     ->where('kategori.url', $slug)
    //                     ->orderBy('produk.id', 'DESC')
    //                     ->get()
    //                     ->getResult();
    // }

    public function getProduk($perPage = 12, $cari = null, $slug = null)
    {
        $builder = $this->builder(); // pakai builder model

        $builder->select("
            produk.*,
            (
                SELECT u.nama
                FROM upload u
                WHERE u.idproduk = produk.id
                LIMIT 1
            ) as gambar
        ");

        if ($cari) {
            $builder->groupStart()
                    ->like('produk.nama', $cari)
                    ->orLike('produk.deskripsi', $cari)
                    ->groupEnd();
        }

        if ($slug) {
            $builder->join('kategori','kategori.id = produk.idcat');
            $builder->where('kategori.url', $slug);
        }


        $builder->orderBy('produk.id','DESC');

        $this->builder = $builder;


        return $this->paginate($perPage,'produk'); // ✅ dari Model
    }

    public function getProdukById($id, $field = "semua")
    {
        $builder = $this->db->table('produk');
        $builder->where('id', $id);

        $row = $builder->get()->getRow();

        // jika minta semua field
        if ($field === "semua") {

            if ($row) {

                // ambil semua upload berdasarkan id produk
                $row->gambar = $this->db->table('upload')
                    ->where('idproduk', $id)
                    ->get()
                    ->getResult();

                return $row;
            }

            // jika data tidak ada → buat object kosong
            $fields = $this->db->getFieldData('produk');

            $empty = new \stdClass();

            foreach ($fields as $f) {
                $empty->{$f->name} = $this->kosongan($f->type);
            }

            // tambahkan array gambar kosong
            $empty->gambar = [];

            return $empty;
        }
    }

    public function getProdukVariasi($idproduk)
    {
        return $this->db->table('produk_variasi')
            ->where('idproduk', $idproduk)
            ->get()
            ->getResult();
    }

    public function getWarnaVariasi($idproduk)
    {
        return $this->db->table('produk_variasi pv')
            ->select('
                pv.idwarna,
                vw.nama as warna,
                SUM(pv.stok) as stok,
                MIN(pv.id) as id,
                MIN(pv.harga) as harga
            ')
            ->join('variasi_warna vw','vw.id = pv.idwarna','left')
            ->where('pv.idproduk', $idproduk)
            ->groupBy('pv.idwarna')
            ->get()
            ->getResult();
    }

    public function getWarna($id, $field = 'nama', $by = 'id')
    {
        $builder = $this->db->table('variasi_warna');
        $builder->where($by, $id);

        $row = $builder->get()->getRow();

        // jika minta semua field
        if ($field === 'semua') {

            if ($row) {
                return $row;
            }

            // jika tidak ada data
            $fields = $this->db->getFieldData('variasi_warna');

            $empty = new \stdClass();
            foreach ($fields as $f) {
                $empty->{$f->name} = $this->kosongan($f->type);
            }

            return $empty;
        }

        // jika hanya minta field tertentu
        return $row->$field ?? "";
    }

    public function getVariasi($id, $field = "semua", $column = "id")
    {
        $builder = $this->db->table('produk_variasi');

        $builder->where($column, $id);
        $builder->limit(1);

        $query = $builder->get();

        if ($field === "semua") {

            $row = $query->getRow();

            if ($row) {
                return $row;
            }

            // jika tidak ada data → buat object kosong sesuai struktur tabel
            $fields = $this->db->getFieldData('produk_variasi');

            $emptyObject = new \stdClass();

            foreach ($fields as $fieldInfo) {
                $columnName = $fieldInfo->name;
                $emptyObject->$columnName = $this->kosongan($fieldInfo->type);
            }

            return $emptyObject;

        } else {

            $row = $query->getRow();

            return $row->$field ?? "";
        }
    }
}

