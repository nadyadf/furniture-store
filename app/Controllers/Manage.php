<?php 

namespace App\Controllers;

use CodeIgniter\Model;
use Config\Database;

class Manage extends BaseController {

    public function index() 
    {
        if (!$this->func->cekLogin()) {
            return redirect()->to(site_url('signin'));
        }

        $data = $this->data;
        // Mengambil usrid dari session
        $usrid = session('usrid') ?? 0; // atau $_SESSION['usrid'] ?? 0

        $data['usrid']        = $usrid;
        $data['usr']          = $this->func->getUser($usrid, "semua");
        $data['pro']          = $this->func->getProfil($usrid, "semua", "usrid");
        $data['count_bayar']  = $this->func->getPesananCount($usrid, 'bayar');
        $data['count_proses'] = $this->func->getPesananCount($usrid, 'proses');
        $data['count_kirim']  = $this->func->getPesananCount($usrid, 'kirim');
        $data['alamat']       = $this->func->getData('alamat', $usrid, 'usrid', false);
        $data['provinsi']     = $this->func->getAllProvinsi();

        return view('client/pengaturan', $data);
    }

    public function pesanan()
    {
        if (!$this->func->cekLogin()) {
            return redirect()->to(site_url('signin'));
        }

        $session = session();
        $data = $this->data;

        // 1. Ambil status dari GET, jika kosong/tidak ada set default 'belumbayar'
        $status = $this->request->getGet('status') ?? 'belumbayar';

        // 2. Masukkan status_aktif ke array $data
        $data['status_aktif'] = $status;

        // 3. Set judul halaman
        $data['nama'] = $data['set']->nama . " &#8211; Status Pesanan";

        return view('pesanan/main', $data);
    }

    public function konfirmasi()
    {
        if ($this->func->cekLogin() == true) {
            
            if ($this->request->getPost('idbayar')) {
                
                $fileBukti = $this->request->getFile('bukti');

                if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
                    
                    $userId = session()->get('usrid');
                    $idBayar = $this->request->getPost('idbayar');
                    $newName = $userId . $idBayar . date("YmdHis") . '.' . $fileBukti->getExtension();

                    $fileBukti->move(ROOTPATH . 'public/cdn/konfirmasi/', $newName);

                    $db = \Config\Database::connect();
                    $data = [
                        "tgl"     => date("Y-m-d H:i:s"),
                        "idbayar" => $idBayar,
                        "bukti"   => $newName
                    ];
                    
                    $this->func->insertData('konfirmasi', $data);

                    return redirect()->to('manage/pesanan');

                } else {
                    $error = $fileBukti->getErrorString();
                    print_r($error);
                }

            } else {
                $push["idbayar"] = $this->request->getGet('sess') ?? 0;

                echo view("headv2", ["titel" => "Konfirmasi Pembayaran"]);
                echo view("admin/konfirmasi", $push);
                return view("foot");
                    
            }
        } else {
            
            return redirect()->to('signin');
        }
    }

    public function detailpesanan()
    {
        if ($this->func->cekLogin() == true || session()->has('usrid_temp')) {
            
            if ($this->request->getGet('orderid')) {
                $orderId = $this->request->getGet('orderid');
                $data = $this->data;
                $data['nama'] =   $data['set']->nama . ' – ' . 'Rincian Pesanan';
                $data['transaksi'] = $this->func->getTransaksiByOrderId($orderId);

                return view("client/detail_pesanan", $data);
                
            } else {
                echo "
                <script>
                    history.back();
                </script>
                ";
                exit;
            }
        } else {
            return redirect()->to('signin');
        }
    }

    public function cetakInvoice(){
        $trxid = (int) $this->request->getGet('id');
        $data = $this->data;
        $data['transaksi'] = $this->func->getTransaksiById($trxid);
		return view("print/invoice", $data);
	}

    public function lacakpaket($orderid = null)
    {
        // Jika orderid kosong, langsung kembalikan 404
        if (empty($orderid)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            // Atau jika ingin redirect ke route tertentu:
            // return redirect()->to(site_url('404'));
        }

        // Ambil data transaksi
        $trx = $this->func->getTransaksiByOrderId($orderid)[0];

        if (!$trx) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $session = session();

        $isLoggedIn = $this->func->cekLogin() == true;
        $isMember   = $isLoggedIn && isset($trx->usrid) && $trx->usrid == $session->get('usrid');
        $isNonMember = $session->has('usrid_temp') && isset($trx->usrid_temp) && $trx->usrid_temp == $session->get('usrid_temp');
        $data = $this->data;

        if ($isMember || $isNonMember) {
            $data['nama'] =   $data['set']->nama . ' – ' . 'Lacak Paket';
            $data['orderid'] = $orderid;
            $data['transaksi'] = $trx;
            
            // Memanggil view di CI4
            return view('pesanan/tracking', $data);
        }

        // Jika bukan milik user yang login/temp user, tampilkan 404
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
 }

?>