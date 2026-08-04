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

        $gambarCache = [];
        $gudangCache = [];

        foreach ($result as $item) {

            // Gambar
            if (!isset($gambarCache[$item->variasi])) {
                $gambarCache[$item->variasi] = $this->db
                    ->table('upload')
                    ->where('id_produk_variasi', $item->variasi)
                    ->get()
                    ->getRow();
            }

            $item->gambar = $gambarCache[$item->variasi];

            // Gudang
            $kota = $this->globalset("kota");
            if (!isset($gudangCache[$item->gudang])) {
                $gudangCache[$item->gudang] = $this->getGudang(
                    $item->gudang,
                    'idkab'
                );
            }

            $item->gudang = $gudangCache[$item->gudang] ? $this->getKabupaten($gudangCache[$item->gudang]) : $this->getKabupaten($kota);
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

    public function cekLogin(string $type = 'user'): int
    {
        $session = session();

        if ($session->has('usrid')) {
            $table = ($type === 'admin') ? 'admin' : 'user_data';

            $userData = $this->db->table($table)
                                ->where('id', $session->get('usrid'))
                                ->get()
                                ->getRow();

            if ($userData && $userData->id > 0) {
                // Update 'tgl' hanya jika bukan admin
                if ($type !== 'admin') {
                    $this->db->table($table)
                            ->where('id', $session->get('usrid'))
                            ->update(['tgl' => date('Y-m-d H:i:s')]);
                }

                return (int) $session->get('usrid');
            }
        }

        // Jika id tidak ada di tabel yang dicari (atau belum login), kembalikan 0
        return 0;
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
            p.url,
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
                p.url,
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

                $row->totalstok = $this->db->table('produk_variasi')
                    ->selectSum('stok')
                    ->where('idproduk', $id)
                    ->get()
                    ->getRow()
                    ->stok ?? 0;

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
                pv.id,
                pv.idwarna,
                vw.nama as warna,
                pv.stok,
                pv.harga,
                u.nama as gambar
            ')
            ->join('variasi_warna vw', 'vw.id = pv.idwarna', 'left')
            ->join('upload u', 'u.id_produk_variasi = pv.id', 'left')
            ->where('pv.idproduk', $idproduk)
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

    public function getFoto($id)
    {
        $server = base_url('cdn/uploads');

        $builder = db_connect()->table('upload');
        $builder->where('idproduk', $id);

        $foto = $builder->limit(1)->get()->getRow();

        if ($foto) {
            return $server . '/' . $foto->nama;
        }

        return base_url('cdn/uploads/no-image.png');
    }

    public function getProdukTerkait($idProduk, $idKategori, $limit = 4)
    {
        return $this->db->table('produk p')
            ->select('
                p.*,
                MIN(u.nama) as gambar
            ')
            ->join('upload u', 'u.idproduk = p.id', 'left')
            ->where('p.idcat', $idKategori)
            ->where('p.id !=', $idProduk)
            ->groupBy('p.id')
            ->orderBy('RAND()')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getKecamatan($value, $field = 'semua', $column = 'id')
    {
        $row = $this->db->table('kec')
            ->where($column, $value)
            ->limit(1)
            ->get()
            ->getRow();

        // Data ditemukan
        if ($row) {

            if ($field === 'semua') {
                return $row;
            }

            return $row->{$field} ?? '';
        }

        // Data tidak ditemukan
        if ($field === 'semua') {

            $empty = new \stdClass();

            $empty->id = 0;
            $empty->rajaongkir = 0;
            $empty->idkab = 0;
            $empty->nama = 'city temp';

            return $empty;
        }

        return '';
    }
    public function getAlamat()
    {
        $builder = $this->db->table('alamat a');

        if (session()->has('usrid')) {
            $builder->where('a.usrid', session('usrid'));
        } else {
            $builder->where('a.usrid_temp', session('usrid_temp'));
        }

        return $builder
            ->select('
                a.*,
                kec.nama as kecamatan,
                kab.nama as kabupaten
            ')
            ->join('kec', 'kec.id = a.idkec', 'left')
            ->join('kab', 'kab.id = kec.idkab', 'left')
            ->orderBy('a.status', 'DESC')
            ->get()
            ->getResult();
    }

    public function getProvinsi(
        $value = null,
        string $field = 'id'
    )
    {
        $builder = $this->db->table('prov');

        if ($value !== null) {
            return $builder
                ->where($field, $value)
                ->get()
                ->getRow();
        }

        return $builder
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResult();
    }

    public function getKabupaten(
        $value = null,
        string $field = 'id'
    )
    {
        $builder = $this->db->table('kab');

        if ($value !== null) {
            return $builder
                ->where($field, $value)
                ->get()
                ->getRow();
        }

        return $builder
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResult();
    }

    public function getAllProvinsi()
    {
        return $this->db
            ->table('prov')
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResult();
    }

    public function getTransaksiProduk($value, $column = "id", $selectField = "semua")
    {
        $db      = db_connect();

        $builder = $db->table('transaksi_produk');
        $builder->where($column, $value);
        $builder->limit(1);

        $query = $builder->get();
        $row   = $query->getFirstRow();

        // === Jika ingin ambil semua data ===
        if ($selectField === "semua") {

            if ($row) {
                return $row;
            }

            // fallback jika data tidak ditemukan
            $fields = $db->getFieldData('transaksi_produk');

            $emptyObject = new \stdClass();
            foreach ($fields as $field) {
                $fieldName = $field->name;
                $emptyObject->$fieldName = $this->kosongan($field->type);
            }

            return $emptyObject;
        }

        // === Jika hanya ambil 1 field tertentu ===
        if ($row) {
            return $row->$selectField ?? "";
        }

        return "";
    }

    public function getTransaksiProdukByIdTransaksi($value): array
    {
        return $this->db->table('transaksi_produk')
            ->where('idtransaksi', $value)
            ->get()
            ->getResult();
    }

    public function processCheckout(array $idProduk): array
    {
        $produkValid = [];
        $berat = 0;
        $total = 0;
        $gudang = null;

        foreach ($idProduk as $id) {

            $transaksi = $this->getTransaksiProduk($id);

            if (!$transaksi || $transaksi->idtransaksi != 0) {
                continue;
            }

            $produk = $this->getProdukById($transaksi->idproduk);

            if (!$produk || $produk->id <= 0) {
                continue;
            }

            if ($gudang === null) {
                $gudang = $produk->gudang;
            }

            if ($gudang != $produk->gudang) {
                continue;
            }

            $produkValid[] = $id;

            $berat += $produk->berat * $transaksi->jumlah;
            $total += $transaksi->harga * $transaksi->jumlah;
        }

        if (empty($produkValid)) {
            return [
                'success' => false,
                'message' => 'Produk tidak valid'
            ];
        }

        $prebayarId = $this->createPrePayment($produkValid, $gudang, $berat, $total);

        return [
            'success' => true,
            'data' => [
                'prebayarId' => $prebayarId,
                'berat' => $berat,
                'total' => $total
            ]
        ];
    }

    private function createPrePayment(array $produk, int $gudang, float $berat, float $total)
    {
        $session = session();
        $tipeco = $session->has('usrid') ? 0 : 1;

        // close old prepayment
        if ($session->has('prebayar')) {
            $builder = $this->db->table('pembayaran_pre');

            $builder->where('tipe_co', $tipeco)
                ->where('status', 0);

            if ($tipeco == 0) {
                $builder->where('usrid', $session->get('usrid'));
            } else {
                $builder->where('usrid_temp', $session->get('usrid_temp'));
            }

            $builder->update(['status' => 2]);

            $session->remove('prebayar');
        }

        $set = $this->globalset('semua');

        $dari = $gudang == 0
            ? $set->kecamatan
            : $this->getGudang($gudang, 'idkec');

        $data = [
            'tipe_co' => $tipeco,
            'tgl' => date('Y-m-d H:i:s'),
            'dari' => $dari,
            'gudang' => $gudang,
            'total' => $total,
            'berat' => $berat,
            'produk' => implode('|', $produk)
        ];

        if ($tipeco == 0) {
            $data['usrid'] = $session->get('usrid');
        } else {
            $data['usrid_temp'] = $session->get('usrid_temp');
        }

        $this->db->table('pembayaran_pre')->insert($data);

        $id = $this->db->insertID();

        $session->set('prebayar', $id);

        return $id;
    }

    public function getGudang(
        $value,
        string $field = 'semua',
        string $key = 'id'
    )
    {
        $builder = $this->db->table('gudang'); 
        $result = $builder 
            ->where($key, $value) 
            ->get() 
            ->getRow(); // Ambil seluruh data 
        
        if ($field === 'semua') { 
            if ($result) { return $result; } 
            $emptyObject = new \stdClass(); 
            foreach ($this->db->getFieldData('gudang') as $column) { 
                $emptyObject->{$column->name} = $this->kosongan($column->type); 
            }
            return $emptyObject; 
        } // Ambil field tertentu 
        
        return $result->{$field} ?? null;
    }

    public function getUser($userId, $field = 'semua', $searchBy = 'id')
    {
        $user = $this->db->table('user_data')
            ->where($searchBy, $userId)
            ->get(1)
            ->getRow();

        // Mengembalikan satu field saja
        if ($field !== 'semua') {
            return $user->{$field} ?? '';
        }

        // Mengembalikan seluruh data user
        if ($user) {
            return $user;
        }

        // Jika user tidak ditemukan, buat object kosong sesuai struktur tabel
        $emptyUser = new \stdClass();

        foreach ($this->db->getFieldData('user_data') as $column) {
            $emptyUser->{$column->name} = null;
        }

        return $emptyUser;
    }

    public function getUserTemp(
        $value,
        string $searchField = 'id'
    )
    {
        return $this->db
            ->table('user_temp')
            ->where($searchField, $value)
            ->get()
            ->getRow();
    }

    public function updateUserTemp(
        int $id,
        array $data
    ): bool
    {
        return $this->db
            ->table('user_temp')
            ->where('id', $id)
            ->update($data);
    }

    public function addNewAddress(
        bool $tipeCheckout,
        array $dataAlamat
    )
    {
        // Validasi kode pos
        $destinationId = $this->validateKel(
            $dataAlamat['idkec'],
            $dataAlamat['kodepos']
        );

        if ($destinationId === null) {
            return false;
        }

        $session = session();

        $alamatBuilder = $this->db->table('alamat');

        if ($tipeCheckout == 0) {
            $alamatBuilder->where('usrid', $session->get('usrid'));
        } else {
            $alamatBuilder->where('usrid_temp', $session->get('usrid_temp'));
        }

        $dataAlamat['status'] = $alamatBuilder->countAllResults() > 0 ? 0 : 1;

        if ($tipeCheckout == 0) {
            $dataAlamat['usrid'] = $session->get('usrid');
        } else {
            $dataAlamat['usrid_temp'] = $session->get('usrid_temp');
        }

        $this->db->table('alamat')->insert($dataAlamat);

        return $this->db->insertID();
    }

    public function getAlamatById(int $id): ?object
    {
        // Cukup query berdasarkan ID alamat karena ID sudah Primary Key (Unik)
        return $this->db->table('alamat a')
            ->select("
                a.*,
                kec.nama AS nama_kecamatan,
                CONCAT(kab.tipe, ' ', kab.nama) AS nama_kabupaten
            ")
            ->join('kec', 'kec.id = a.idkec', 'left')
            ->join('kab', 'kab.id = kec.idkab', 'left')
            ->where('a.id', $id)
            ->get()
            ->getRow();
    }

    public function getPembayaranPre($nilai, $kolom, $kolomPencarian = 'id')
    {
        $query = $this->db
            ->table('pembayaran_pre')
            ->where($kolomPencarian, $nilai)
            ->get(1);

        if ($kolom === 'semua') {

            $data = $query->getRow();

            if ($data) {
                return $data;
            }

            $fields = $this->db->getFieldData('pembayaran_pre');

            $dataKosong = new \stdClass();

            foreach ($fields as $field) {
                $namaField = $field->name;
                $dataKosong->$namaField = $this->kosongan($field->type);
            }

            return $dataKosong;
        }

        $data = $query->getRow();

        return $data ? $data->$kolom : '';
    }

    public function getKurirByIds(array $idKurir)
    {
        if (empty($idKurir)) {
            return [];
        }

        return $this->db
            ->table('kurir')
            ->whereIn('id', $idKurir)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    public function updatePreBayar(int $idPreBayar, array $data): bool
    {
        return $this->db
            ->table('pembayaran_pre')
            ->where('id', $idPreBayar)
            ->update($data);
    }

    public function getKurir($id, $field = 'semua', $column = 'id')
    {
        $row = $this->db
            ->table('kurir')
            ->where($column, $id)
            ->get()
            ->getRow();

        if ($field === 'semua') {
            return $row;
        }

        return $row ? ($row->$field ?? null) : null;
    }

    public function getPaket($id, $field = 'semua', $column = 'id')
    {
        $row = $this->db
            ->table('paket')
            ->where($column, $id)
            ->get()
            ->getRow();

        if ($field === 'semua') {
            return $row;
        }

        return $row ? ($row->$field ?? null) : null;
    }

    public function getPaketByIdKurir(int $idKurir): array
    {
        return $this->db->table('paket')
            ->select('id, idkurir, rajaongkir, nama')
            ->where('idkurir', $idKurir)
            ->orderBy('rajaongkir', 'ASC')
            ->get()
            ->getResult();
    }

    public function hitungBeratOngkir(int $beratGram, string $kurir): int
    {
        if ($beratGram <= 0) {
            return 1;
        }

        $toleransi = match (strtolower($kurir)) {
            'jne'  => 300,
            'pos'  => 200,
            'tiki' => 299,
            default => 0,
        };

        if ($beratGram <= (1000 + $toleransi)) {
            return 1;
        }

        return (int) ceil(($beratGram - $toleransi) / 1000);
    }
    public function getKurirCustom(int $kurirId, int $paketId)
    {
        return $this->db
            ->table('kurir_custom')
            ->where('kurir', $kurirId)
            ->where('paket', $paketId)
            ->get()
            ->getResult();
    }

    public function getKel(int $placeId, string $placeType): ?string
    {
        // Ambil data gudang/alamat berdasarkan ID
        $place = $this->db
            ->table($placeType)
            ->where('id', $placeId)
            ->get()
            ->getRow();

        if (!$place) {
            log_message('error', "getKel: Tidak ditemukan data {$placeType} dengan id = {$placeId}");
            return null;
        }

        $idKec  = $place->idkec;
        $zipCode = trim($place->kodepos);

        // Ambil data kecamatan
        $kec = $this->db
            ->table('kec')
            ->where('id', $idKec)
            ->get()
            ->getRow();

        if (!$kec) {
            log_message('error', "getKel: Tidak ditemukan kecamatan dengan id = {$idKec}");
            return null;
        }

        $kecName = $kec->nama;

        // Cari di database lokal
        $kel = $this->db
            ->table('kel')
            ->where('idkec', $idKec)
            ->where('kodepos', $zipCode)
            ->get()
            ->getRow();

        if ($kel) {
            return $kel->rajaongkir;
        }

        // Request ke RajaOngkir
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . urlencode($kecName),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'key: ' . $this->globalset('rajaongkir')
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            log_message('error', "getKel: CURL error - {$error}");
            return null;
        }

        $responseData = json_decode($response, true);

        if (!isset($responseData['data']) || empty($responseData['data'])) {
            log_message('error', "getKel: Tidak ada data API untuk '{$kecName}'");
            return null;
        }

        // Filter berdasarkan kodepos
        $filtered = array_values(
            array_filter(
                $responseData['data'],
                static fn($item) => ($item['zip_code'] ?? '') == $zipCode
            )
        );

        if (empty($filtered)) {
            log_message('error', "getKel: Tidak ada data API yang cocok dengan kodepos {$zipCode}");
            return null;
        }

        $destinationId = $filtered[0]['id'];
        $subdistrict   = ucwords(strtolower($filtered[0]['subdistrict_name']));

        // Cek apakah sudah ada
        $exists = $this->db
            ->table('kel')
            ->where('nama', $subdistrict)
            ->where('kodepos', $zipCode)
            ->countAllResults();

        if ($exists === 0) {
            $this->db
                ->table('kel')
                ->insert([
                    'idkec'      => $idKec,
                    'nama'       => $subdistrict,
                    'kodepos'    => $zipCode,
                    'rajaongkir' => $destinationId,
                ]);
        }

        return $destinationId;
    }

    public function validateKel(int $idKec, string $kodePos): ?string
    {
        // Normalisasi kode pos
        $kodePos = trim($kodePos);

        // Ambil data kecamatan
        $kec = $this->db
            ->table('kec')
            ->where('id', $idKec)
            ->get()
            ->getRow();

        $kecName = $kec->nama;

        // 1. Cek database lokal
        $kel = $this->db
            ->table('kel')
            ->where('idkec', $idKec)
            ->where('kodepos', $kodePos)
            ->get()
            ->getRow();

        if ($kel) {
            return $kel->rajaongkir;
        }

        // 2. Cek ke API RajaOngkir
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . urlencode($kodePos),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'key: ' . $this->globalset('rajaongkir')
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            log_message('error', "validateKel: CURL Error - {$error}");
            return null;
        }

        $responseData = json_decode($response, true);

        if (empty($responseData['data'])) {
            log_message('error', "validateKel: Kode pos {$kodePos} tidak ditemukan.");
            return null;
        }
        

        $filtered = array_values(array_filter(
            $responseData['data'],
            static function ($item) use ($kecName) {
                return strcasecmp(
                    trim($item['district_name'] ?? ''),
                    trim($kecName)
                ) === 0;
            }
        ));

        if (empty($filtered)) {
            log_message('error', "validateKel: Kode pos {$kodePos} tidak termasuk kecamatan {$kecName}");
            return null;
        }

        $item = $filtered[0];

        // Simpan ke database jika belum ada
        $subdistrict = ucwords(strtolower($item['subdistrict_name']));

        $existing = $this->db
            ->table('kel')
            ->where('idkec', $idKec)
            ->where('nama', $subdistrict)
            ->get()
            ->getRow();

        if ($existing) {
            $this->db
                ->table('kel')
                ->where('id', $existing->id)
                ->update([
                    'kodepos'    => $kodePos,
                    'rajaongkir' => $item['id'],
                ]);
        } else {
            $this->db
                ->table('kel')
                ->insert([
                    'idkec'      => $idKec,
                    'nama'       => $subdistrict,
                    'kodepos'    => $kodePos,
                    'rajaongkir' => $item['id'],
                ]);
        }

        return $item['id'];
    }

    public function getHistoryOngkir(
        int $dari,
        int $tujuan,
        string $kurir,
        ?string $service = null
    ): array {
        $builder = $this->db
            ->table('histori_ongkir')
            ->where('dari', $dari)
            ->where('tujuan', $tujuan)
            ->where('kurir', strtolower($kurir));

        if ($service !== null) {
            $builder->where('serviceid', $service);
        }

        return $builder
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();
    }

    public function cekOngkir($dari,$berat,$tujuan,$kurirId,$serviceId, $alamatId, $gudangId){
			
        $kurir = $this->getKurir($kurirId,"semua");
        $service = $this->getPaket($serviceId,"semua");
        $beratkg = $this->hitungBeratOngkir($berat, $kurir->rajaongkir);
        
        // CUSTOM KURIR
        if ($kurir->jenis == 2) {
            $idKabTujuan = $this->getKecamatan($tujuan,"idkab");
            // $berat = $this->hitungBeratOngkir($berat);
            $kurirCustom = $this->getKurirCustom($kurir->id, $service->id);

            if (!empty($kurirCustom)) {
                $hasils = array();
                foreach($kurirCustom as $kc){
                    $biaya = ($kc->jenis == 1) ? $kc->harga : $kc->harga * $beratkg;
                    $hasils[$kc->idkab] = array( // hasils, s = sementara
                        "success"	=> true,
                        "dari"		=> $dari,
                        "tujuan"	=> $tujuan,
                        "kurir"		=> $kurir->nama, // jne, jnt, dsb
                        "service"	=> $service->nama, // REG, CTC, dsb
                        "kuririd"	=> $kurir->id,
                        "serviceid"	=> $service->id,
                        "cod"		=> $service->cod, // boolean
                        "etd"		=> $kc->estimasi, // estimasi hari
                        "harga"		=> $biaya,
                        "update"	=> date("Y-m-d H:i:s"),
                        "hargaperkg"=> $kc->harga,
                        "token"		=> csrf_hash()
                    );
                }

                if (isset($hasils[$idKabTujuan])) {
                    $hasil = $hasils[$idKabTujuan];
                } else { // Tidak ada kurir custom yg cocok dengan id kab tujuan
                    $hasil = array(
                        "success"	=> false,
                        "dari"		=> $dari,
                        "tujuan"	=> $tujuan,
                        "kurir"		=> $kurir->nama,
                        "service"	=> $service->nama,
                        "kuririd"	=> $kurir->id,
                        "serviceid"	=> $service->id,
                        "cod"		=> $service->cod,
                        "etd"		=> 1,
                        "harga"		=> 0,
                        "update"	=> date("Y-m-d H:i:s"),
                        "hargaperkg"=> 0,
                        "keterangan"=> "ongkir tidak ditemukan",
                        "token"		=> csrf_hash()
                    );
                }
            } else { // Tidak ada kurir custom yg sesuai dengan kurir dan service yg diminta
                $hasil = array(
                    "success"	=> false,
                    "dari"		=> $dari,
                    "tujuan"	=> $tujuan,
                    "kurir"		=> $kurir->nama,
                    "service"	=> $service->nama,
                    "kuririd"	=> $kurir->id,
                    "serviceid"	=> $service->id,
                    "cod"		=> $service->cod,
                    "etd"		=> 1,
                    "harga"		=> 0,
                    "update"	=> date("Y-m-d H:i:s"),
                    "hargaperkg"=> 0,
                    "keterangan"=> "ongkir tidak ditemukan",
                    "token"		=> csrf_hash()
                );
            }
        } else { // Inisialisasi variable untuk kurir Jenis 1 
            // $kuririd = $kurir->id;
            $kurir = $kurir->rajaongkir;
            // $serviceid = $service->id;
            $serviceCod = $service->cod; 
            $service = $service->rajaongkir; // cod, OKE, REG, dll
        }
        

        if (isset($hasil)) {
            return $hasil; // jika kurir custom, return langsung hasilnya
        } else { // Untuk yg bukan Kurir Custom

            // Dapatkan subdistrict id dari rajaongkir
            $dari = $this->getKel($gudangId, "gudang"); // id kelurahan asal pada db rajaongkir
            $datakec = $this->getKecamatan($tujuan,"semua"); // data kecamatan tujuan
            $tujuan = $this->getKel($alamatId, "alamat"); // id kelurahan tujuan pada db rajaongkir

            if ($datakec->idkab == $dari AND $kurir == "jne") { // kemungkinan code ini tidak tereksekusi karena $dari adalah id kelurahan pada rajaongkir bukan id kabupaten pada db lokal
                if($_GET["service"] == "REG"){ $service = "CTC"; }
                elseif($_GET["service"] == "YES"){ $service = "CTCYES"; }
            }

            $historyOngkir = $this->getHistoryOngkir($dari, $tujuan, $kurir);
            if (!empty($historyOngkir)) {
                foreach($historyOngkir as $ho) {
                    if (strcasecmp($service,$ho->service) == 0) {  // histori ongkir yg serupa tersedia
                        if ($ho->harga <= 0) { // jika harga 0, maka request ke rajaongkir
                            return $this->reqOngkir($dari, $beratkg, $tujuan, $kurir, $service, $kurirId, $serviceId);
                            exit;
                        }
                        
                        $harga = $ho->harga * $beratkg;
                        $etd = $ho->etd != "" OR $ho->etd != "-" ? $ho->etd : "0";
                        $array = array(
                            "success"	=> true,
                            "dari"		=> $ho->dari,
                            "tujuan"	=> $ho->tujuan,
                            "kurir"		=> $ho->kurir,
                            "service"	=> $ho->service,
                            "kuririd"	=> $kurirId,
                            "serviceid"	=> $serviceId,
                            "cod"		=> $serviceCod,
                            "etd"		=> $etd,
                            "harga"		=> $harga,
                            "update"	=> $ho->update
                        );
                        return $array;
                    }
                }
             } else { // Jika tidak ada history ongkir yg serupa, request ke raja ongkir
                return $this->reqOngkir($dari, $berat, $tujuan, $kurir, $service, $kurirId, $serviceId);
            }
        }
	}

    private function reqOngkir($dari, $berat, $tujuan, $kurir, $services, $kuririd, $serviceid){
		$usrid = session('usrid') ?? 0;
		//$kur = $this->getKurir($kuririd,"semua");
		$ser = $this->getPaket($serviceid,"semua");


		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => http_build_query([
					"origin" => $dari,
					"destination" => $tujuan,
					"weight" => $berat,
					"courier" => $kurir
			]),
			// "origin=".$dari."&originType=city&destination=".$tujuan."&destinationType=subdistrict&weight=".$berat."&courier=".$kurir,
			CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"content-type: application/x-www-form-urlencoded",
			"Key: ".$this->globalset("rajaongkir")
			),
		));

		$response = curl_exec($curl);
		// echo "<pre>";
        //         print_r($response);
		// 						echo "</pre>";
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			$arr = json_decode($response);
			//print_r($response);
			//exit;
			//print_r($arr->rajaongkir->results[0]->costs[0]->cost[0]->value);
			$hasil = array("success"=>false,"response"=>"daerah tidak terjangkau!","harga"=>0);

			if (isset($arr->meta->code) AND $arr->meta->code == "200") {
				$hasil = array("success"=>false,"response"=>"daerah tidak terjangkau!","message"=>"service code tidak ada data","harga"=>0,"kurir"=>$kurir,"paket"=>$services,"origin"=>$dari);
                $beratkg = $this->hitungBeratOngkir($berat, $kurir);

				for($i=0; $i<count($arr->data); $i++){

					$harga = $arr->data[$i]->cost / $beratkg;
					$service = $arr->data[$i]->service;
					$etd = $arr->data[$i]->etd;
					$etd = $etd != "" ? $etd : "0";
					$array = array(
						"dari"		=> $dari,
						"tujuan"	=> $tujuan,
						"kurir"		=> $kurir,
						"service"	=> $service,
						"kuririd"	=> $kuririd,
						"serviceid"	=> $serviceid,
						"cod"		=> $ser->cod,
						"harga"		=> $harga,
						"etd"		=> $etd,
						"update"	=> date("Y-m-d H:i:s"),
						"usrid"		=> $usrid
					);

                    //simpan setiap response dengan berbagai service yg tersedia
                    $this->saveHistoryOngkir($array);
                    
					if ($services != "") {
						if (strcasecmp($service,$services) == 0) { // cek kesamaan service yg dibutuhkan
							$hasil = array(
								"success"	=> true,
								"dari"		=> $dari,
								"tujuan"	=> $tujuan,
								"kurir"		=> $kurir,
								"service"	=> $service,
								"kuririd"	=> $kuririd,
								"serviceid"	=> $serviceid,
								"cod"		=> $ser->cod,
								"harga"		=> $arr->data[$i]->cost,
								"etd"		=> $etd,
								"update"	=> date("Y-m-d H:i:s"),
								"hargaperkg"=> $harga
							);
						} else {
							if ($kurir == "jne") {
								if ($services == "REG") {
									if (strcasecmp($service,"CTC") == 0) { // REG dan CTC dianggap sama
										$hasil = array(
											"success"	=> true,
											"dari"		=> $dari,
											"tujuan"	=> $tujuan,
											"kurir"		=> $kurir,
											"service"	=> $service,
											"kuririd"	=> $kuririd,
											"serviceid"	=> $serviceid,
											"cod"		=> $ser->cod,
											"harga"		=> $arr->data[$i]->cost,
											"etd"		=> $etd,
											"update"	=> date("Y-m-d H:i:s"),
											"hargaperkg"=> $harga
										);
									}
								} elseif ($services == "YES") {
									if (strcasecmp($service,"CTCYES") == 0) { // YES dan CTCYES dianggap sama
										$hasil = array(
											"success"	=> true,
											"dari"		=> $dari,
											"tujuan"	=> $tujuan,
											"kurir"		=> $kurir,
											"service"	=> $service,
											"kuririd"	=> $kuririd,
											"serviceid"	=> $serviceid,
											"cod"		=> $ser->cod,
											"harga"		=> $arr->data[$i]->cost,
											"etd"		=> $etd,
											"update"	=> date("Y-m-d H:i:s"),
											"hargaperkg"=> $harga
										);
									}
								}
							}
						}
					} else {
						$etd = $arr->data[$i]->etd;
						$etd = $etd != "" ? $etd : "0";
						$hasil = array(
							"success"	=> true,
							"dari"		=> $dari,
							"tujuan"	=> $tujuan,
							"kurir"		=> $kurir,
							"service"	=> $arr->data[$i]->service,
							"kuririd"	=> $kuririd,
							"serviceid"	=> $serviceid,
							"cod"		=> $ser->cod,
							"harga"		=> $arr->data[$i]->cost,
							"etd"		=> $etd,
							"update"	=> date("Y-m-d H:i:s"),
							"hargaperkg"=> $harga
						);
					}
				}
			}
			//echo "dari: ".$dari.", tujuan: ".$tujuan.", berat: ".$berat.", kurir: ".$kurir."<br/>&nbsp;<br/>";
			return $hasil;
		}
	}

    public function saveHistoryOngkir(array $data): bool
    {
        $historyOngkir = $this->getHistoryOngkir(
            $data['dari'],
            $data['tujuan'],
            $data['kurir'],
            $data['service']
        );

        $builder = $this->db->table('histori_ongkir');

        if (!empty($historyOngkir)) {
            return $builder
                ->where('id', $historyOngkir['id'])
                ->update($data);
        }
            
        return $builder->insert($data);
        
    }

    public function createPembayaran(array $paymentData): int
    {
        $builder = $this->db->table('pembayaran');

        $builder->insert($paymentData);

        return $this->db->insertID();
    }

    public function updatePembayaran(int $paymentId, array $paymentData): bool
    {
        return $this->db->table('pembayaran')
            ->where('id', $paymentId)
            ->update($paymentData);
    }

    public function createTransaksi(array $transaksiData): int
    {
        $builder = $this->db->table('transaksi');

        $builder->insert($transaksiData);

        return $this->db->insertID();
    }

    public function updateTransaksiProduk(int $id, array $data): bool
    {
        return $this->db->table('transaksi_produk')
            ->where('id', $id)
            ->update($data);
    }

    public function insertData(string $table, array $data): int
    {
        $this->db->table($table)->insert($data);

        return $this->db->insertID();
    }

    public function deleteData(string $table, $value, string $field = 'id'): bool
    {
        return $this->db->table($table)
            ->where($field, $value)
            ->delete();
    }

    public function updateData(string $table, array $data, array $conditions): bool
    {
        return $this->db->table($table)
            ->where($conditions)
            ->update($data);
    }

    public function getProfil($profileId, string $field = 'semua', string $searchBy = 'id')
    {
        $profile = $this->db->table('profil')
            ->where($searchBy, $profileId)
            ->get(1)
            ->getRow();

        // Mengembalikan satu field saja
        if ($field !== 'semua') {
            return $profile->{$field} ?? '';
        }

        // Mengembalikan seluruh data
        if ($profile) {
            return $profile;
        }

        // Jika data tidak ditemukan, buat object kosong
        $emptyProfile = new \stdClass();

        foreach ($this->db->getFieldData('profil') as $column) {
            $emptyProfile->{$column->name} = null;
        }

        return $emptyProfile;
    }

    public function getData(string $table, $value, string $searchBy = 'id', bool $single = true)
    {
        $builder = $this->db->table($table)->where($searchBy, $value);

        if ($single) {
            $row = $builder->get(1)->getRow();

            if ($row) {
                return $row;
            }

            $emptyObject = new \stdClass();
            foreach ($this->db->getFieldData($table) as $column) {
                $emptyObject->{$column->name} = null;
            }

            return $emptyObject;
        }

        $result = $builder->get()->getResult();

        if (!empty($result)) {
            return $result;
        }

        $emptyObject = new \stdClass();
        foreach ($this->db->getFieldData($table) as $column) {
            $emptyObject->{$column->name} = null;
        }

        return [$emptyObject];
    }

    public function getAllData($table)
    {
        return $this->db->table($table)
                        ->get()
                        ->getResult();
    }
    
    public function getBank($bankId, string $field = 'semua', string $searchBy = 'id')
    {
        $bank = $this->db->table('rekening_bank')
            ->where($searchBy, $bankId)
            ->get(1)
            ->getRow();

        // Mengembalikan satu field saja
        if ($field !== 'semua') {
            return $bank->{$field} ?? '';
        }

        // Mengembalikan seluruh data
        if ($bank) {
            return $bank;
        }

        // Jika data tidak ditemukan, buat object kosong
        $emptyBank = new \stdClass();

        foreach ($this->db->getFieldData('rekening_bank') as $column) {
            $emptyBank->{$column->name} = null;
        }

        return $emptyBank;
    }

    public function getPembayaran($paymentId, string $field = 'semua', string $searchBy = 'id')
    {
        $payment = $this->db->table('pembayaran')
            ->where($searchBy, $paymentId)
            ->get(1)
            ->getRow();

        // Mengembalikan satu field saja
        if ($field !== 'semua') {
            return $payment->{$field} ?? '';
        }

        // Mengembalikan seluruh data
        if ($payment) {
            return $payment;
        }

        // Jika data tidak ditemukan, buat object kosong
        $emptyPayment = new \stdClass();

        foreach ($this->db->getFieldData('pembayaran') as $column) {
            $emptyPayment->{$column->name} = null;
        }

        return $emptyPayment;
    }

    public function getTransaksiById(int $id): array
    {
        $builder = $this->db->table('transaksi');

        // Filter berdasarkan user session yang sedang aktif
        if (session()->has('usrid')) {
            $builder->where('usrid', session()->get('usrid'));
        } else {
            $builder->where('usrid_temp', session()->get('usrid_temp'));
        }

        // Ambil semua transaksi yang cocok (mengembalikan array of objects)
        $transactions = $builder->where('id', $id)
                                ->get()
                                ->getResult();

        if (empty($transactions)) {
            return [];
        }

        // Lakukan perulangan untuk melengkapi data setiap transaksi
        foreach ($transactions as &$transaction) {

            // 1. Ambil Data Pembayaran
            $transaction->pembayaran = $this->db->table('pembayaran')
                ->where('id', $transaction->idbayar)
                ->get()
                ->getRow();

            // 2. Ambil Data Alamat Lengkap (+ Kec, Kab, & Prov)
            $transaction->alamat = $this->db->table('alamat a')
                ->select("a.*, kec.nama AS nama_kecamatan, k.nama AS nama_kabupaten, k.tipe AS tipe_kabupaten, p.nama AS nama_provinsi")
                ->join('kec', 'kec.id = a.idkec', 'left')
                ->join('kab k', 'k.id = kec.idkab', 'left')
                ->join('prov p', 'p.id = k.idprov', 'left')
                ->where('a.id', $transaction->alamat)
                ->get()
                ->getRow();

            // 3. Ambil Data User (Lokal/Registered vs Temp/Guest)
            if (!empty($transaction->usrid) && $transaction->usrid != 0) {
                $transaction->user = $this->db->table('user_data')
                    ->where('id', $transaction->usrid)
                    ->get()
                    ->getRow();
            } elseif (!empty($transaction->usrid_temp) && $transaction->usrid_temp != 0) {
                $transaction->user = $this->db->table('user_temp')
                    ->where('id', $transaction->usrid_temp)
                    ->get()
                    ->getRow();
            } else {
                $transaction->user = null;
            }

            // 4. Ambil Seluruh Data Gudang beserta Kota Asal
            $gudang = $this->db->table('gudang g')
                ->select("g.*, CONCAT(k.tipe, ' ', k.nama) AS kota_asal, k.nama AS nama_kabupaten, k.tipe AS tipe_kabupaten")
                ->join('kab k', 'k.id = g.idkab', 'left')
                ->where('g.id', $transaction->gudang)
                ->get()
                ->getRow();

            $transaction->gudang_detail = $gudang; 
            $transaction->kota_asal     = $gudang ? $gudang->kota_asal : '-';

            // 5. Cari Nama Kurir
            $kurir = $this->db->table('kurir')
                ->select('nama')
                ->where('id', $transaction->kurir)
                ->get()
                ->getRow();

            $transaction->nama_kurir = $kurir ? $kurir->nama : '-';

            // 6. Cari Nama Paket
            $paket = $this->db->table('paket')
                ->select('nama')
                ->where('id', $transaction->paket)
                ->get()
                ->getRow();

            $transaction->nama_paket = $paket ? $paket->nama : '-';

            // 7. Ambil Detail Produk Terkait
            $transaction->produk = $this->db->table('transaksi_produk tp')
                ->select("
                    tp.*,
                    p.*,
                    pv.harga AS harga_variasi,
                    vw.nama AS nama_warna,
                    u.nama AS gambar
                ")
                ->join('produk p', 'p.id = tp.idproduk', 'left')
                ->join('produk_variasi pv', 'pv.id = tp.variasi', 'left')
                ->join('variasi_warna vw', 'vw.id = pv.idwarna', 'left')
                ->join('upload u', "u.id_produk_variasi = pv.id", 'left')
                ->where('tp.idtransaksi', $transaction->id)
                ->get()
                ->getResult();
        }

        return $transactions;
    }

    public function getTransaksiByPaymentId(int $paymentId): array
    {
        $transactions = $this->db->table('transaksi')
            ->where('idbayar', $paymentId)
            ->get()
            ->getResult();

        foreach ($transactions as &$transaction) {
            $transaction->produk = $this->db->table('transaksi_produk tp')
                ->select("
                    tp.*,
                    p.nama,
                    p.harga,
                    pv.harga AS harga_variasi,
                    vw.nama AS nama_warna,
                    u.nama AS gambar
                ")
                ->join('produk p', 'p.id = tp.idproduk')
                ->join('produk_variasi pv', 'pv.id = tp.variasi', 'left')
                ->join('variasi_warna vw', 'vw.id = pv.idwarna', 'left')
                ->join('upload u', "u.id_produk_variasi = pv.id", 'left')
                ->where('tp.idtransaksi', $transaction->id)
                ->get()
                ->getResult();
        }

        return $transactions;
    }

    public function getRekeningAdmin(): array
    {
        return $this->db->table('rekening')
            ->select('rekening.*, rekening_bank.*, rekening_bank.id AS idBank')
            ->join('rekening_bank', 'rekening_bank.id = rekening.idbank')
            ->where('rekening.usrid', 0)
            ->get()
            ->getResult();
    }

    public function getTransaksiByOrderId(string $orderId): array
    {
        $builder = $this->db->table('transaksi');

        if (session()->has('usrid')) {
            $builder->where('usrid', session()->get('usrid'));
        } else {
            $builder->where('usrid_temp', session()->get('usrid_temp'));
        }

        $transactions = $builder->where('orderid', $orderId)
                                ->get()
                                ->getResult();

        if (empty($transactions)) {
            return [];
        }

        foreach ($transactions as &$transaction) {
            
            // 1. Ambil Data Pembayaran
            $transaction->pembayaran = $this->db->table('pembayaran')
                ->where('id', $transaction->idbayar)
                ->get()
                ->getRow();

            // 2. Ambil SEMUA Data Alamat Lengkap beserta relasi Kota/Kabupaten & Kecamatan
            // Seluruh kolom dari tabel alamat akan masuk ke properti $transaction->alamat
            $transaction->alamat = $this->db->table('alamat a')
                ->select("a.*, kec.nama AS nama_kecamatan, k.nama AS nama_kabupaten, k.tipe AS tipe_kabupaten")
                ->join('kec', 'kec.id = a.idkec')
                ->join('kab k', 'k.id = kec.idkab')
                ->where('a.id', $transaction->alamat)
                ->get()
                ->getRow();

            // 3. Cari Kota Asal (Gudang: transaksi -> gudang -> kabupaten)
            $gudangKota = $this->db->table('gudang g')
                ->select("CONCAT(k.tipe, ' ', k.nama) AS kota_asal")
                ->join('kab k', 'k.id = g.idkab')
                ->where('g.id', $transaction->gudang)
                ->get()
                ->getRow();
            
            $transaction->kota_asal = $gudangKota ? $gudangKota->kota_asal : '-';

            // 4. Cari Nama Kurir
            $kurir = $this->db->table('kurir')
                ->where('id', $transaction->kurir)
                ->get()
                ->getRow();
            
            $transaction->nama_kurir = $kurir ? $kurir->nama : '-';
            $transaction->kurir_rajaongkir = $kurir ? $kurir->rajaongkir : '-';

            // 5. Cari Nama Paket
            $paket = $this->db->table('paket')
                ->select('nama')
                ->where('id', $transaction->paket)
                ->get()
                ->getRow();
            
            $transaction->nama_paket = $paket ? $paket->nama : '-';

            // 6. Ambil Detail Produk Terkait
            $transaction->produk = $this->db->table('transaksi_produk tp')
                ->select("
                    tp.*,
                    p.nama,
                    p.harga,
                    pv.harga AS harga_variasi,
                    vw.nama AS nama_warna,
                    u.nama AS gambar
                ")
                ->join('produk p', 'p.id = tp.idproduk')
                ->join('produk_variasi pv', 'pv.id = tp.variasi', 'left')
                ->join('variasi_warna vw', 'vw.id = pv.idwarna', 'left')
                ->join('upload u', "u.id_produk_variasi = pv.id", 'left')
                ->where('tp.idtransaksi', $transaction->id)
                ->get()
                ->getResult();
        }

        return $transactions;
    }

    public function getUnpaidPayments($page = 1)
    {
        $this->setTable('pembayaran');
        
        $pembayaranList = $this->where('status', 0)
                                ->where('usrid', session()->get('usrid'))
                                ->orderBy('status', 'ASC')
                                ->orderBy('id', 'DESC')
                                ->paginate(10, 'default', $page);

        if (empty($pembayaranList)) {
            return [];
        }

        $db = db_connect();

        foreach ($pembayaranList as &$pembayaran) {

            $konfirmasi = $db->table('konfirmasi')
                            ->where('idbayar', $pembayaran->id) 
                            ->get()
                            ->getResultObject();

            // Simpan data konfirmasi ke properti Object pembayaran (akan bernilai null jika tidak ada)
            $pembayaran->konfirmasi = $konfirmasi;
            
            // Perbaikan: Akses ID menggunakan $pembayaran->id (bukan array)
            $transaksi = $db->table('transaksi')
                            ->where('idbayar', $pembayaran->id) 
                            ->get()
                            ->getRowObject(); // Menggunakan getRowObject agar senada berbentuk Object

            if ($transaksi) {
                // Ambil semua data dari 'transaksi_produk' berdasarkan id transaksi
                $transaksiProdukList = $db->table('transaksi_produk')
                                        ->where('idtransaksi', $transaksi->id) 
                                        ->get()
                                        ->getResultObject(); // Menggunakan Object

                // Lacak data produk, variasi, warna, dan gambar
                foreach ($transaksiProdukList as &$tp) {
                    
                    // Cari data ke tabel 'produk'
                    $tp->produk = $db->table('produk')
                                    ->where('id', $tp->idproduk)
                                    ->get()
                                    ->getRowObject();

                    // Cari data ke tabel 'produk_variasi'
                    $variasi = $db->table('produk_variasi')
                                ->where('id', $tp->variasi)
                                ->get()
                                ->getRowObject();

                    if ($variasi) {
                        // Cari nama warna di 'variasi_warna'
                        $variasi->warna = $db->table('variasi_warna')
                                            ->where('id', $variasi->idwarna)
                                            ->get()
                                            ->getRowObject();
                        
                        // Ambil gambar tambahan dari tabel 'upload'
                        $variasi->gambar = $db->table('upload')
                                            ->where('id_produk_variasi', $variasi->id)
                                            ->get()
                                            ->getRowObject(); 
                    }

                    $tp->variasi_detail = $variasi;
                }

                // Gabungkan list produk ke dalam struktur data transaksi
                $transaksi->produk_list = $transaksiProdukList;
            }

            // Simpan data transaksi ke dalam properti Object pembayaran
            $pembayaran->transaksi = $transaksi;
        }

        return $pembayaranList;
    }

    function arrEnc($arr, $type = "encode")
    {
        if ($type == "encode") {
            $result = base64_encode(serialize($arr));
        } else {
            $result = unserialize(base64_decode($arr));
        }

        return $result;
    }

    public function getTransactionsByStatus($status = 4, $page = 1, $orderColumn = 'id', $orderDirection = 'DESC')
    {
        $this->setTable('transaksi');
        
        $builder = $this->where('usrid', session()->get('usrid'));

        // Fleksibilitas: Jika parameter $status berupa array, gunakan whereIn. Jika bukan, gunakan where biasa.
        if (is_array($status)) {
            $builder->whereIn('status', $status);
        } else {
            $builder->where('status', $status);
        }

        $transaksiList = $builder->orderBy($orderColumn, $orderDirection)
                                ->paginate(10, 'default', $page);

        if (empty($transaksiList)) {
            return [];
        }

        $db = db_connect();

        foreach ($transaksiList as &$transaksi) {
            
            $transaksiProdukList = $db->table('transaksi_produk')
                                    ->where('idtransaksi', $transaksi->id) 
                                    ->get()
                                    ->getResultObject(); 

            foreach ($transaksiProdukList as &$tp) {
                
                $tp->produk = $db->table('produk')
                                ->where('id', $tp->idproduk)
                                ->get()
                                ->getRowObject();

                $variasi = $db->table('produk_variasi')
                            ->where('id', $tp->variasi)
                            ->get()
                            ->getRowObject();

                if ($variasi) {
                    $variasi->warna = $db->table('variasi_warna')
                                        ->where('id', $variasi->idwarna)
                                        ->get()
                                        ->getRowObject();
                    
                    $variasi->gambar = $db->table('upload')
                                        ->where('id_produk_variasi', $variasi->id)
                                        ->get()
                                        ->getRowObject(); 
                }
                $tp->variasi_detail = $variasi;
            }

            $transaksi->produk_list = $transaksiProdukList;
        }

        return $transaksiList;
    }

    public function getShippedTransactions(bool $hasResi = true, int $page = 1): array
    {
        $this->setTable('transaksi');
        
        $builder = $this->where('status', 2)
                        ->where('usrid', session()->get('usrid'));

        // Filter berdasarkan ketersediaan resi
        if ($hasResi) {
            $builder->where('resi !=', '')
                    ->where('resi IS NOT NULL');
        } else {
            $builder->groupStart()
                        ->where('resi', '')
                        ->orWhere('resi IS NULL')
                    ->groupEnd();
        }

        $transaksiList = $builder->orderBy('id', 'DESC')
                                ->paginate(10, 'default', $page);

        if (empty($transaksiList)) {
            return [];
        }

        $db = db_connect();

        foreach ($transaksiList as &$transaksi) {
            
            $transaksiProdukList = $db->table('transaksi_produk')
                                    ->where('idtransaksi', $transaksi->id) 
                                    ->get()
                                    ->getResultObject(); 

            foreach ($transaksiProdukList as &$tp) {
                
                $tp->produk = $db->table('produk')
                                ->where('id', $tp->idproduk)
                                ->get()
                                ->getRowObject();

                $variasi = $db->table('produk_variasi')
                            ->where('id', $tp->variasi)
                            ->get()
                            ->getRowObject();

                if ($variasi) {
                    $variasi->warna = $db->table('variasi_warna')
                                        ->where('id', $variasi->idwarna)
                                        ->get()
                                        ->getRowObject();
                    
                    $variasi->gambar = $db->table('upload')
                                        ->where('id_produk_variasi', $variasi->id)
                                        ->get()
                                        ->getRowObject(); 
                }
                $tp->variasi_detail = $variasi;
            }

            $transaksi->produk_list = $transaksiProdukList;
        }

        return $transaksiList;
    }

    public function getPesananCount($usrid = 0, $status = 'semua')
    {
        $db = db_connect();
        $builder = $db->table('transaksi');

        // Filter berdasarkan ID User
        $builder->where('usrid', $usrid);

        switch ($status) {
            case 'bayar':
                $builder->where('status', 0);
                break;

            case 'proses':
                $builder->where('status', 1);
                break;

            case 'kirim':
                // Dalam Pengiriman (status = 2)
                $builder->where('status', 2);
                break;

            case 'selesai':
                // Pesanan Selesai / Diterima (status = 3)
                $builder->where('status', 3);
                break;

            case 'batal':
                $builder->where('status', 4);
                break;

            default:
                $builder->where('status !=', 4);
                break;
        }

        return $builder->countAllResults();
    }

    public function getPesananTerakhir($usrid = 0, $limit = 5)
    {
        return $this->db->table('transaksi t')
                        ->select('t.*, p.total, p.status AS status_bayar')
                        ->join('pembayaran p', 'p.id = t.idbayar', 'left')
                        ->where('t.usrid', $usrid)
                        ->orderBy('t.id', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->getResult();
    }

    public function mainsite_url(string $url = ''): string
    {
        // ROOTPATH menunjuk ke root folder project (selevel app/, writable/, dll)
        $lisensiPath = ROOTPATH . 'lisensi.json';
        $domain = '';

        if (file_exists($lisensiPath)) {
            $jsonContent = file_get_contents($lisensiPath);
            $lisensi = json_decode($jsonContent, true);

            if (isset($lisensi['domain']) && ! empty($lisensi['domain'])) {
                $domain = rtrim($lisensi['domain'], '/') . '/';
            }
        }

        // Jika lisensi tidak ada atau domain kosong di JSON, gunakan base_url bawaan CI4
        if (empty($domain)) {
            $domain = rtrim(base_url(), '/') . '/';
        }

        return $domain . ltrim($url, '/');
    }

    public function getJmlPesanan(): int
    {
        return $this->db->table('transaksi')
                        ->where('status <=', 1)
                        ->countAllResults();
    }

    public function ubahTgl($format, $tanggal = "now", $bahasa = "id")
    {
        $en = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $id = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $lang = ${$bahasa} ?? $id;
        return str_replace($en, $lang, date($format, strtotime($tanggal)));
    }

    public function getDashboardData()
    {
        $data = [
            'jualtoday'   => 0, 'trxtoday'   => 0, 'omsettoday'   => 0,
            'jualkemarin' => 0, 'trxkemarin' => 0, 'omsetkemarin' => 0,
            'jualbulan'   => 0, 'trxbulan'   => 0, 'omsetbulan'   => 0,
            'juallalu'    => 0, 'trxlalu'    => 0, 'omsetlalu'    => 0,
            'pcsfix'      => [],
            'notafix'     => [],
            'graphtgl'    => [],
            'topus_total'     => [],
            'topus_usrid'     => [],
            'topus_nama'      => [],
            'topus_transaksi' => [],
            'topus_jmlpcs'    => []
        ];

        $today   = date("Y-m-d");
        $kemarin = date("Y-m-d", strtotime("-1 day"));
        $sebulan = date("Ymd", strtotime("-20 day"));
        $bulan   = date("Y-m-");
        
        // Penanganan bulan lalu agar format Y-m- presisi (termasuk ganti tahun)
        $lalu    = date("Y-m-", strtotime("-1 month"));

        // Generate 20 Hari Terakhir untuk Grafik
        $graphtgl = [];
        for ($i = 0; $i < 20; $i++) {
            $day = 20 - $i;
            $graphtgl[] = date("d-m", strtotime("-{$day} day"));
        }

        // 1. OLAH DATA TRANSAKSI & TRANSAKSI PRODUK
        $trx = $this->db->table('transaksi')
                        ->where('tgl >=', $lalu . "01")
                        ->where('status >=', 1)
                        ->get()
                        ->getResult();

        $pcs  = [];
        $nota = [];

        foreach ($trx as $r) {
            $tglYm = $this->ubahTgl("Y-m-", $r->tgl);
            $tglYmd = $this->ubahTgl("Ymd", $r->tgl);
            $tgldm = $this->ubahTgl("d-m", $r->tgl);
            $tglOnly = explode(" ", $r->tgl)[0];

            // Hitung Jumlah Transaksi
            if ($tglYm == $bulan) {
                $data['trxbulan'] += 1;
            } elseif ($tglYm == $lalu) {
                $data['trxlalu'] += 1;
            }

            if ($tglOnly == $today) {
                $data['trxtoday'] += 1;
            } elseif ($tglOnly == $kemarin) {
                $data['trxkemarin'] += 1;
            }

            // Hitung Produk Terjual per Transaksi
            $trxProduk = $this->db->table('transaksi_produk')
                                  ->where('idtransaksi', $r->id)
                                  ->get()
                                  ->getResult();

            $jml = 0;
            foreach ($trxProduk as $rs) {
                if ($tglYm == $bulan) {
                    $data['jualbulan'] += $rs->jumlah;
                } elseif ($tglYm == $lalu) {
                    $data['juallalu'] += $rs->jumlah;
                }

                if ($tglOnly == $today) {
                    $data['jualtoday'] += $rs->jumlah;
                } elseif ($tglOnly == $kemarin) {
                    $data['jualkemarin'] += $rs->jumlah;
                }

                if ($tglYmd >= $sebulan) {
                    $jml += $rs->jumlah;
                }
            }

            // Data untuk Grafik
            if ($tglYmd >= $sebulan) {
                $pcs[$tgldm]  = ($pcs[$tgldm] ?? 0) + $jml;
                $nota[$tgldm] = ($nota[$tgldm] ?? 0) + 1;
            }
        }

        // Format data Grafik agar sesuai array tgl
        foreach ($graphtgl as $gtgl) {
            $data['pcsfix'][]  = $pcs[$gtgl] ?? 0;
            $data['notafix'][] = $nota[$gtgl] ?? 0;
            $data['graphtgl'][] = $gtgl;
        }

        // 2. OLAH DATA OMSET / PEMBAYARAN
        $pembayaran = $this->db->table('pembayaran')
                               ->where('tgl >=', $lalu . "01")
                               ->where('status >=', 1)
                               ->get()
                               ->getResult();

        foreach ($pembayaran as $r) {
            $tglYm   = $this->ubahTgl("Y-m-", $r->tgl);
            $tglOnly = explode(" ", $r->tgl)[0];
            $netto   = $r->total - $r->kode_bayar;

            if ($tglYm == $bulan) {
                $data['omsetbulan'] += $netto;
            } elseif ($tglYm == $lalu) {
                $data['omsetlalu'] += $netto;
            }

            if ($tglOnly == $today) {
                $data['omsettoday'] += $netto;
            } elseif ($tglOnly == $kemarin) {
                $data['omsetkemarin'] += $netto;
            }
        }

        // 3. TOP USER
        $topUsers = $this->db->table('pembayaran')
                             ->select('SUM(total) AS total, SUM(kode_bayar) AS kode, usrid')
                             ->like('tgl', $bulan)
                             ->where('status >=', 1)
                             ->groupBy('usrid')
                             ->orderBy('total', 'DESC')
                             ->get()
                             ->getResult();

        foreach ($topUsers as $r) {
            // Hitung total transaksi per user
            $totalTrx = $this->db->table('transaksi')
                                 ->selectCount('id', 'total_data')
                                 ->where('usrid', $r->usrid)
                                 ->get()
                                 ->getRow();
            $total = $totalTrx->total_data ?? 0;

            // Hitung total pcs per user
            $totalPcs = $this->db->table('transaksi_produk')
                                 ->selectSum('jumlah', 'jml')
                                 ->where('usrid', $r->usrid)
                                 ->get()
                                 ->getRow();
            $jml = $totalPcs->jml ?? 0;

            // Profil User
            $usr = $this->db->table('profil')->where('usrid', $r->usrid)->get()->getRow();
            $usrnama = $usr->nama ?? "USER DIHAPUS";

            $data['topus_total'][]     = $r->total;
            $data['topus_usrid'][]     = $r->usrid;
            $data['topus_nama'][]      = $usrnama;
            $data['topus_transaksi'][] = $total;
            $data['topus_jmlpcs'][]    = $jml;
        }

        return $data;
    }

    public function getAdmin($adminId, $field = 'semua', $searchBy = 'id')
    {
        $admin = $this->db->table('admin')
            ->where($searchBy, $adminId)
            ->get(1)
            ->getRow();

        // Mengembalikan satu field saja
        if ($field !== 'semua') {
            return $admin->{$field} ?? '';
        }

        // Mengembalikan seluruh data admin
        if ($admin) {
            return $admin;
        }

        // Jika admin tidak ditemukan, buat object kosong sesuai struktur tabel 'admin'
        $emptyAdmin = new \stdClass();

        foreach ($this->db->getFieldData('admin') as $column) {
            $emptyAdmin->{$column->name} = null;
        }

        return $emptyAdmin;
    }

    public function getAdminUnpaidPayments(int $page = 1, string $cari = '', int $perpage = 10): array
{
    $db  = \Config\Database::connect();
    $set = $this->globalset("semua"); // Mengembalikan Object

    $builder = $db->table('pembayaran');

    // Filter Pencarian
    if (!empty($cari)) {
        $userIDs = $db->table('profil')
            ->select('usrid')
            ->like('nama', $cari)
            ->orLike('nohp', $cari)
            ->get()->getResultArray();
        $arrUsrid = array_filter(array_column($userIDs, 'usrid'));

        $alamatIDs = $db->table('alamat')
            ->select('usrid, usrid_temp')
            ->like('nama', $cari)
            ->orLike('alamat', $cari)
            ->orLike('nohp', $cari)
            ->get()->getResultArray();
        
        $arrAlamatUsrid     = array_filter(array_column($alamatIDs, 'usrid'));
        $arrAlamatUsridTemp = array_filter(array_column($alamatIDs, 'usrid_temp'));

        $mergedUsrid     = array_unique(array_merge($arrUsrid, $arrAlamatUsrid));
        $mergedUsridTemp = array_unique($arrAlamatUsridTemp);

        $builder->groupStart()
            ->like('pembayaran.invoice', $cari)
            ->orLike('pembayaran.total', $cari)
            ->orLike('pembayaran.kode_bayar', $cari);

        if (!empty($mergedUsrid)) {
            $builder->orWhereIn('pembayaran.usrid', $mergedUsrid);
        }
        if (!empty($mergedUsridTemp)) {
            $builder->orWhereIn('pembayaran.usrid_temp', $mergedUsridTemp);
        }
        $builder->groupEnd();
    }

    $builder->where('pembayaran.status', 0);

    // Hitung Total Rows
    $totalRows = $builder->countAllResults(false);

    // Ambil Data Pembayaran (Object)
    $offset   = ($page - 1) * $perpage;
    $payments = $builder->orderBy('pembayaran.id', 'DESC')
                        ->limit($perpage, $offset)
                        ->get()
                        ->getResult(); // <-- Ambil sebagai Object

    // Set Pager
    $pager = \Config\Services::pager();
    $this->pager = $pager->makeLinks($page, $perpage, $totalRows, 'bootstrap_full');

    if (empty($payments)) {
        return [];
    }

    $resultData = [];
    foreach ($payments as $r) {
        // 1. Handling Transaksi (Mengembalikan Array Result -> Ambil indeks ke-0)
        $trxList = $this->getTransaksiByPaymentId($r->id);
        $trx     = $trxList[0] ?? (object) []; // Object Transaksi Pertamaecho "<pre>";



        // 2. Konfirmasi Transfer (Object)
        $konfirmasi = $db->table('konfirmasi')
            ->where('idbayar', $r->id)
            ->get()
            ->getRow(); // <-- Ambil sebagai Object

        $bukti = $konfirmasi->bukti ?? '';
        $tglFormatted = date('d/m/Y H:i', strtotime($r->tgl));
        if ($bukti) {
            $tglFormatted .= "<br/><a href='javascript:void(0)' onclick='bukti(\"" . base_url("konfirmasi/" . $bukti) . "\")'>&raquo; Lihat Bukti Transfer</a>";
        }

        // 3. Profil & Alamat (Object)
        $isMember = ($r->usrid > 0);
        $profil   = $isMember 
            ? $this->getProfil($r->usrid, "semua", "usrid") 
            : $this->getUserTemp($r->usrid_temp);

        // 1. Ambil ID Alamat dari $trx dan pastikan berbentuk angka (int)
$idAlamat = (int) ($trx->alamat ?? 0);

// 2. Query ke fungsi getAlamatById
$alamat = ($idAlamat > 0) ? $this->getAlamatById($idAlamat) : null;

// 3. Jika $alamat masih null (misal karena alamat non-member ada di tabel usertemp / alamat_temp)
// Ambil profil pembeli
$isMember = ((int) $r->usrid > 0);
$profil   = $isMember 
    ? $this->getProfil($r->usrid, "semua", "usrid") 
    : $this->getUserTemp($r->usrid_temp);

// 4. Ambil properti dari object $alamat (gunakan nama kolom dari tabel alamat)
$namaProfil   = esc($profil->nama ?? 'Tamu');
$namaAlamat   = esc($alamat->nama ?? '-');
$nohpAlamat   = esc($alamat->nohp ?? $alamat->no_hp ?? '-');
$detailAlamat = esc($alamat->alamat ?? '-');

// HTML Tampilan Pembeli
if ($isMember) {
    $pembeliHtml = "<span class='text-primary'>[" . $namaProfil . "]</span>";
} else {
    $pembeliHtml = "<span class='text-danger'>[" . $namaProfil . "] <i class='badge badge-danger p-lr-8 p-tb-3'>non member</i></span>";
}
$pembeliHtml .= "<br/><small>" . $namaAlamat . " (" . $nohpAlamat . ")</small>";
$pembeliHtml .= "<br/><small class='m-t--4 dis-block'><i>" . $detailAlamat . "</i></small>";

        // 4. Kurir (Object)
        $namaKurir = !empty($trx->kurir) ? strtoupper($this->getKurir($trx->kurir, "nama")) : '-';
        $namaPaket = !empty($trx->paket) ? strtoupper($this->getPaket($trx->paket, "nama")) : '-';
        $kurirHtml = $namaKurir . "<br/><small class='text-primary'>" . $namaPaket . "</small>";

        // 5. Metode Bayar
        $metodeList  = [1 => "Bayar Ditempat (COD)", 2 => "Transfer"];
        $metodeLabel = $metodeList[(int)$r->metode_bayar] ?? "Lainnya";

        // 6. Gudang (Object)
        $gudangId = $trx->gudang ?? 0;
        if ($gudangId > 0) {
            $gudang     = $this->getGudang($gudangId, "semua");
            $kota       = $this->getKabupaten($gudang->idkab ?? 0);
            $namaKota   = trim(($kota->tipe ?? '') . " " . ($kota->nama ?? ''));
            $namaGudang = ($gudang->nama ?? '') . " - " . $namaKota;
        } else {
            $kota       = $this->getKabupaten($set->kota ?? 0);
            $namaKota   = trim(($kota->tipe ?? '') . " " . ($kota->nama ?? ''));
            $namaGudang = "PUSAT - " . $namaKota;
        }

        // Return Object Result
        $resultData[] = (object) [
            'id'           => $r->id,
            'invoice'      => $r->invoice,
            'total'        => $r->total,
            'kodebayar'    => $r->kode_bayar ?? 0,
            'kode_bayar'   => $r->kode_bayar ?? 0,
            'biaya_cod'    => $r->biaya_cod ?? 0,
            'metode_bayar' => $r->metode_bayar,
            'metode_nama'  => $metodeLabel,
            'tgl'          => $r->tgl,
            'tgl_format'   => $tglFormatted,
            'bukti'        => $bukti,
            'trxid'        => $trx->id ?? 0,
            'orderid'      => $trx->orderid ?? '-',
            'pembeli_html' => $pembeliHtml,
            'kurir_html'   => $kurirHtml,
            'namagudang'   => $namaGudang,

            // Raw data utuh berbentuk Object
            'raw_payment'  => $r,
            'raw_trx'      => $trx
        ];
    }

    return $resultData;
}

/**
 * Helper Private: Meratakan segala tipe data (Object, Array 2D, Array 1D, NULL) menjadi Array 1D yang aman
 */
/**
 * Helper Private: Meratakan segala tipe data (Object, Array 2D, Array 1D, NULL) menjadi Array 1D yang aman.
 * Jika masukan berupa Array 2D (seperti getTransaksiByPaymentId), otomatis mengambil baris pertama ([0]).
 */
private function toArray($data): array
{
    if (empty($data)) {
        return [];
    }

    // Jika data berupa Object, ubah ke Array
    if (is_object($data)) {
        $data = (array) $data;
    }

    // Jika data berupa Array 2D / list berindeks [0 => ...], ambil elemen pertamanya
    if (is_array($data) && isset($data[0])) {
        $first = $data[0];
        return is_object($first) ? (array) $first : (array) $first;
    }

    return is_array($data) ? $data : [];
}
}

