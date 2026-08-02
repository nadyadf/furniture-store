<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\GlobalData;

abstract class AdminBaseController extends Controller
{
    protected $request;
    protected $helpers = ['form', 'url', 'custom'];
    protected $func;
    protected $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->func = model(GlobalData::class);

        // Khusus Admin (Bisa ambil settingan dasar saja tanpa keranjang/kategori)
        $adminId = $this->func->cekLogin('admin');
        $set     = $this->func->globalset('semua');
        

        $this->data['set']       = $set;
        $this->data['isAdmin']   = ($adminId > 0);
        $this->data['adminId']   = $adminId;

        $this->data['nama_app']   = 'Admin - ' . $set->nama;
        $this->data['jmlPesanan'] = $this->func->getJmlPesanan(); // Hanya contoh data yg relevan untuk Admin
    }
}

?>