<?php 

namespace App\Controllers\Client;

use CodeIgniter\Model;
use Config\Database;
use App\Controllers\BaseController;

class Checkout extends BaseController {

    public function index() {

        $idProduk = $this->request->getPost('idproduk');

        if (empty($idProduk) || !is_array($idProduk)) {
            return redirect()
                ->to('/keranjang')
                ->with('swal_error', 'Pilih produk terlebih dahulu.');
        }

        $result = $this->func->processCheckout($idProduk);

        if (!$result['success']) {
            return redirect()->back()->with('swal_error', $result['message']);
        }

        $data = $this->data;
        $data['checkout'] = $result['data'];
      

        if ($data['isLogin'] || session()->has('usrid_temp')) {
            return view("checkout/main", $data);
        }

        return redirect()->to("signin");
    }

    public function alamat()
    {
	    if ($this->func->cekLogin() == true || session()->get('usrid_temp')) {
            $data = $this->data;
            $data['alamat'] = $this->func->getAlamat();
            $data['provinsi'] = $this->func->getAllProvinsi();
            return view("checkout/alamat", $data);
		} else {
            redirect("signin");
        }
    }

    public function simpanAlamat()
    {
        $session = session();

        if (!$this->func->cekLogin() && !$session->has('usrid_temp')) {
            redirect("signin");
        }

        if (!$session->has('prebayar')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data checkout tidak ditemukan.'
            ]);
        }

        $tipeCheckout = $session->has('usrid') ? 0 : 1;

        $alamatId = 0;
        $tujuanId = 0;

        // alamat baru
        if ($this->request->getPost('alamat') == '0') {

            if ($tipeCheckout == 1) {

                $userTemp = $this->func->getUserTemp($session->get('usrid_temp'));

                if ($userTemp && empty($userTemp->nama)) {

                    $this->func->updateUserTemp(
                        $session->get('usrid_temp'),
                        [
                            'nama' => $this->request->getPost('nama'),
                            'no_hp' => $this->request->getPost('nohp'),
                        ]
                    );

                }
            }

            $dataAlamat = [
                'idkec'    => $this->request->getPost('idkec'),
                'judul'    => $this->request->getPost('judul'),
                'alamat'   => $this->request->getPost('alamatbaru'),
                'kodepos'  => $this->request->getPost('kodepos'),
                'nama'     => $this->request->getPost('nama'),
                'no_hp'     => $this->request->getPost('nohp'),
            ];

            $alamatId = $this->func->addNewAddress($tipeCheckout, $dataAlamat);

            if ($alamatId === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kode pos tidak sesuai dengan kecamatan yang dipilih.',
                    'token'   => csrf_hash()
                ]);
            }

            $tujuanId = $this->request->getPost('idkec');

        } else {

            $alamatId = $this->request->getPost('alamat');

            $alamat = $this->func->getAlamatById($alamatId);

            if ($alamat) {
                $alamatId = $alamat->id;
                $tujuanId = $alamat->idkec;
            }
        
        }

        if ($alamatId <= 0 || $tujuanId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'token'   => csrf_hash()
            ]);
        }

        $this->func->updatePreBayar(session()->get('prebayar'), [
            'alamat' => $alamatId,
            'tujuan' => $tujuanId
        ]);

        return $this->response->setJSON([
            'success' => true,
            'token'   => csrf_hash()
        ]);
    }

    public function kurir()
    {
        if($this->func->cekLogin() == true || session()->get('usrid_temp')){
            $pre = $this->func->getPembayaranPre(session()->get('prebayar'), "semua");
        
            $berat = $pre->berat;
            $alamat = $this->func->getAlamatById($pre->alamat);
            // $gudang = $this->func->getGudang($pre->gudang);
            $kurirSetting = explode("|", $this->func->globalSet('kurir'));
            $kurirList = $this->func->getKurirByIds($kurirSetting);
            $hasilOngkir = [];

            $hasil = array();
            $paketkurir = array();
            // dd($pre->dari, $alamat->idkec);
            foreach ($kurirList as $kurir) {

                $paketList = $this->func->getPaketByIdKurir($kurir->id);

                foreach ($paketList as $paket) {

                    $ongkir = $this->func->cekOngkir(
                        $pre->dari,
                        $berat,
                        $alamat->idkec,
                        $kurir->id,
                        $paket->id,
                        $pre->alamat,
                        $pre->gudang
                    );

                    if (!empty($ongkir['success'])) {
                        $hasilOngkir[] = $ongkir;
                    }
                }
            }

            $kurir = [];
            $paket = [];

            foreach ($hasilOngkir as $ongkir) {
                $kurir[$ongkir['kuririd']] = $ongkir['kurir'];

                $paket[$ongkir['kuririd']][$ongkir['serviceid']] = [
                    'nama' => $ongkir['service'],
                    'harga' => $ongkir['harga'],
                    'cod'   => $ongkir['cod'],
                    'etd'   => $ongkir['etd'],
                ];
            }
            

            $data['kurir'] = $kurir;
            $data['paket'] = $paket;
            // dd($data['paket']);
            return view("checkout/kurir", $data);
        } else {
            redirect("signin");
        }
    }

    public function simpanKurir()
    {
        $session = session();

        if (
            !($this->func->cekLogin() || $session->has('usrid_temp'))
            || !$session->has('prebayar')
        ) {
            return redirect()->to(site_url('signin'));
        }

        $prebayar = $this->func->getPembayaranPre(
            $session->get('prebayar'),
            'semua'
        );

        if (!$prebayar || $prebayar->id <= 0) {
            return $this->response->setJSON([
                'success' => false
            ]);
        }

        $kurir = $this->request->getPost('kurir');
        $paket = $this->request->getPost('paket');

        $result = $this->func->cekOngkir(
            $prebayar->dari,
            $prebayar->berat,
            $prebayar->tujuan,
            $kurir,
            $paket,
            $prebayar->alamat,
            $prebayar->gudang
        );

        if (empty($result['success'])) {
            return $this->response->setJSON([
                'success' => false
            ]);
        }

        $this->func->updatePreBayar(
            $session->get('prebayar'),
            [
                'kurir'  => $kurir,
                'paket'  => $paket,
                'ongkir' => $result['harga'],
                'cod'    => $result['cod'],
            ]
        );

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function bayar()
    {
        if($this->func->cekLogin() == true || session()->get('usrid_temp')){
            $data = $this->data;

            $pre = $this->func->getPembayaranPre(session()->get('prebayar'), "semua");
            $data['alamat'] = $this->func->getAlamatById($pre->alamat);

            $gudang = $this->func->getGudang($pre->gudang, 'idkab');
            $kotaGudang = ($pre->gudang > 0) ? $this->func->getKabupaten($gudang) : $this->func->getKabupaten($data['set']->kota);
            $data['kota_gudang'] = $kotaGudang->tipe." ".$kotaGudang->nama;

            $data['biaya_cod'] = ($data['set']->biaya_cod > 100)
                ? $data['set']->biaya_cod
                : $pre->total * ((float) $data['set']->biaya_cod / 100);

            $data['kurir'] = $this->func->getKurir($pre->kurir, 'nama');
            $data['paket'] = $this->func->getPaket($pre->paket, 'nama');
            $data['total'] = $pre->total;
            $data['ongkir'] = $pre->ongkir;
            $data['payment_cod'] = $data['set']->payment_cod;
            $data['cod'] = $pre->cod;
            $data['payment_transfer'] = $data['set']->payment_transfer;
            
            return view("checkout/bayar", $data);
        } else {
            redirect("signin");
        }
    }

    public function simpanBayar()
    {
        $session = session();

        if (
            !($this->func->cekLogin() || $session->has('usrid_temp'))
            || !$session->has('prebayar')
        ) {
            return redirect()->to(site_url('signin'));
        }

        $pre = $this->func->getPembayaranPre(
            $session->get('prebayar'),
            'semua'
        );
        $set = $this->data['set'];

        $user = $session->has('usrid')
        ? $this->func->getUser($session->get('usrid'))
        : $this->func->getUserTemp($session->get('usrid_temp'));

        $paymentMethod = (int) $this->request->getPost('metode_bayar');

        if ($pre->id > 0 && $this->request->getPost('metode') && $this->request->getPost('metode_bayar')) {
            $text = "";
            $produkwa = "";
            $hrgwatotal = 0;
            $wa = ($session->has('type') && $session->get('type') == "whatsapp") ? "whatsapp" : null;
            $idbayar = 0;
            $kodebayaran = random_int(100, 999);
            $kodebayar = $kodebayaran;

            $total = $pre->ongkir + $pre->total;
            $transfer = $total;

            if ($paymentMethod == 2){  //transfer
                $total = $kodebayaran + $total;
            }else{
                $kodebayar = 0;
            }

            $codFee = (float) $set->biaya_cod;
            $biayaCod = $codFee > 100
            ? $codFee
            : ($codFee > 0 ? ($codFee / 100) * ($total - $pre->ongkir) : 0);
            $total = ($paymentMethod == 1) ? $total + $biayaCod : $total;
            $bcod = ($paymentMethod == 1) ? $biayaCod : 0;
            $status = $paymentMethod === 1 ? 1 : 0;
            $cod = ($paymentMethod === 1) ? 1 : 0;

            $bayar = [
                "tgl"	=> date("Y-m-d H:i:s"),
                "total"	=> $total,
                "kode_bayar"	=> $kodebayar,
                "transfer"	=> $transfer,
                "metode"	=> $this->request->getPost('metode'),
                "metode_bayar"	=> $this->request->getPost('metode_bayar'),
                "biaya_cod"	=> $bcod,
                "status"	=> $status,
                "kadaluarsa"=> date('Y-m-d H:i:s', strtotime("+2 days"))
            ];

            if($session->has('usrid')){
                $bayar["usrid"] = $session->get('usrid');
            }else{
                $bayar["usrid_temp"] = $session->get('usrid_temp');
            }

            $idbayar = $this->func->createPembayaran($bayar);

            $invoice = date("Ymd").$idbayar.$kodebayaran;
            $this->func->updatePembayaran($idbayar, ['invoice' => $invoice]);
            $invoice = "#".$invoice;

            $orderid = "TRX".date("YmdHis");
            $transaksi = [
                "orderid"	=> $orderid,
                "tgl"		=> date("Y-m-d H:i:s"),
                "tgl_update"	=> date("Y-m-d H:i:s"),
                "kadaluarsa"=> date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 2 days')),
                "alamat"=> $pre->alamat,
                "berat"	=> $pre->berat,
                "ongkir"=> $pre->ongkir,
                "kurir"	=> $pre->kurir,
                "paket"	=> $pre->paket,
                "dari"	=> $pre->dari,
                "gudang"=> $pre->gudang,
                "tujuan"=> $pre->tujuan,
                "cod"	=> $cod,
                "biaya_cod"	=> $bcod,
                "status"	=> $status,
                "idbayar"	=> $idbayar
            ];

            if($session->has('usrid')){
                $transaksi["usrid"] = $session->get('usrid');
            }else{
                $transaksi["usrid_temp"] = $session->get('usrid_temp');
            }

            $idtransaksi = $this->func->createTransaksi($transaksi);

            $produk = explode("|",$pre->produk);
            for($i=0; $i<count($produk); $i++){
                $this->func->updateTransaksiProduk($produk[$i], ["idtransaksi" => $idtransaksi]);
            }

            $tps = $this->func->getTransaksiProdukByIdTransaksi($idtransaksi);
            $nos = 1;
			// $po = 0;

            foreach($tps as $tp){
                $pro = $this->func->getProdukById($tp->idproduk);

                if($tp->variasi != 0){
                    $var = $this->func->getVariasi($tp->variasi);
                    if($tp->jumlah > $var->stok){
                        for($i=0; $i<count($produk); $i++){
                            $this->func->updateTransaksiProduk($produk[$i], ["idtransaksi" => 0]);
                        }

                        $this->func->deleteData('transaksi', $idtransaksi, 'id');
                        $this->func->deleteData('pembayaran', $idbayar, 'id');


                        echo json_encode(["success"=>false,"message"=>"stok produk tidak mencukupi"]);
                        $stok = 0;
                        exit;
                    }
                    
                    $stok = $var->stok - $tp->jumlah;
                    $prostok = $pro->stok - $tp->jumlah;

                    $this->func->updateData('produk', ["stok"=>$prostok,"tgl_update"=>date("Y-m-d H:i:s")], ["id"=>$tp->idproduk]);

                    $this->func->updateData('produk_variasi', ["stok"=>$stok,"tgl"=>date("Y-m-d H:i:s")], ["id"=>$tp->variasi]);
                    
                    $data = [
                        "stok_awal" => $var->stok,
                        "stok_akhir" => $stok,
                        "variasi" => $tp->variasi,
                        "jumlah" => $tp->jumlah,
                        "tgl"	=> date("Y-m-d H:i:s"),
                        "idtransaksi" => $idtransaksi
                    ];

                    if($session->has('usrid')){
                        $data["usrid"] = $session->get('usrid');
                    }else{
                        $data["usrid_temp"] = $session->get('usrid_temp');
                    }

                    $this->func->insertData('histori_stok', $data);
                }else{
                    if($tp->jumlah > $pro->stok){
                        // $produk = explode("|",$pre->produk);
                        for($i=0; $i<count($produk); $i++){
                            $this->func->updateTransaksiProduk($produk[$i], ["idtransaksi" => 0]);
                        }

                        $this->func->deleteData('transaksi', $idtransaksi, 'id');
                        $this->func->deleteData('pembayaran', $idbayar, 'id');
                        
                        echo json_encode(["success"=>false,"message"=>"stok produk tidak mencukupi"]);
                        $stok = 0;
                        exit;
                    }
                    $stok = $pro->stok - $tp->jumlah;

                    $this->func->updateData('produk', ["stok"=>$stok,"tglupdate"=>date("Y-m-d H:i:s")], ["id"=>$tp->idproduk]);

                    $data = [
                        "stok_awal" => $pro->stok,
                        "stok_akhir" => $stok,
                        "variasi" => 0,
                        "jumlah" => $tp->jumlah,
                        "tgl"	=> date("Y-m-d H:i:s"),
                        "idtransaksi" => $idtransaksi
                    ];

                    if($session->has('usrid')){
                        $data["usrid"] = $session->get('usrid');
                    }else{
                        $data["usrid_temp"] = $session->get('usrid_temp');
                    }

                    $this->func->insertData('histori_stok', $data);
                }

                if($wa != null){
                    $var = $this->func->getVariasi($tp->variasi);
                    $hargawa = $pro->harga;
                    if($var->id > 0){
                        $hargawa = $var->harga;
                    }
                    
                    $hargawatotal = $hargawa*$tp->jumlah;
                    $hrgwatotal += $hargawatotal;
                    $variasi = ($tp->variasi != 0 AND $var != null) ? $this->func->getWarna($var->warna,"nama") : "";
                    $produkwa .= "*".$nos.". ".$pro->nama."*\n";
                    $produkwa .= ($tp->variasi != 0 AND $var != null) ? "    Varian : ".$variasi."\n" : "";
                    $produkwa .= "    Jumlah : {$tp->jumlah}\n";
                    $produkwa .= "    Harga (@) : Rp " . number_format($hargawa, 0, ',', '.') . "\n";
                    $produkwa .= "    Harga Total : Rp " . number_format($hargawatotal, 0, ',', '.') . "\n\n";
                    $nos++;
                }
            }

            $alamat = $this->func->getAlamatById($pre->alamat);
            $kec = $this->func->getKecamatan($alamat->idkec);
            $kab = $this->func->getKabupaten($kec->idkab)->nama;
            $alamatz = "{$alamat->alamat}, {$kec->nama}, {$kab} - {$alamat->kodepos}";
            $kurir = $this->func->getKurir($pre->kurir,"nama")." ".$this->func->getPaket($pre->paket,"nama");

            if($wa != null){
                $text = "Halo kak admin ".$set->nama.", saya mau order produk berikut dong!\n\n";
                $text .= $produkwa;
                $text .= "Subtotal : *Rp ". number_format($hrgwatotal, 0, ',', '.')."*\n";
                $text .=  "Ongkos Kirim : *Rp ".number_format($pre->ongkir, 0, ',', '.')."*\n";
                $text .= ($kodebayar > 0) ? "Kode Bayar : *Rp ".number_format($kodebayar, 0, ',', '.')."*\n" : "";
                $text .= "Total : *Rp ".number_format($total, 0, ',', '.')."*\n";
                $text .= "------------------------------\n\n";
                $text .= "Nomor Invoice:\n";
                $text .= $invoice."\n";
                $text .= "------------------------------\n\n";
                $text .= "*Nama Penerima*\n";
                $text .= $alamat->nama." (".$alamat->no_hp.")\n\n";
                $text .= "*Alamat Pengiriman*\n";
                $text .= $alamatz."\n\n";
                $text .= "*Jasa Kurir*\n";
                $text .= strtoupper($kurir);
            }else{
                $text = "";
            }

            $this->func->updateData('pembayaran_pre', ["status"=>1,"idbayar"=>$idbayar,"transfer"=>$transfer,"kode_bayar"=>$kodebayar,"metode"=> $session->get('metode'),"metode_bayar"=>$_POST["metode_bayar"]], ["id"=>$pre->id]);

            $url = ($status == 0) ? site_url("home/invoice")."?inv=".$idbayar : site_url("manage/pesanan?tab=dikemas");
            $url = ($status > 0 AND $pre->usrid == 0) ? site_url("manage/detailpesanan")."?orderid=".$orderid : $url;
            echo json_encode(["success"=>true,"url"=>$url,"text"=>urlencode($text)]);
        } else {
            echo json_encode(["success"=>false]);
        }
    }
}
