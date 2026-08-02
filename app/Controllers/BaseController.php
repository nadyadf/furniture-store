<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\GlobalData;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['form', 'url', 'custom'];
    protected $func;
    protected $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->func = model(GlobalData::class);

        // Hanya untuk Client/Toko Utama
        $usrid = $this->func->cekLogin('user');
        $set   = $this->func->globalset('semua');

        $this->data['set']       = $set;
        $this->data['kategori']  = $this->func->getKategori();
        $this->data['keranjang'] = $this->func->getKeranjang();
        $this->data['isLogin']   = ($usrid > 0);
        $this->data['nama']      = $set->nama . ' – ' . $set->slogan;
        $this->data['desc']      = 'Web toko furnitur ' . $set->nama;
        $this->data['img']       = base_url('cdn/assets/img/' . $set->favicon);
        $this->data['url']       = base_url();
        $this->data['cari']      = esc($this->request->getGet('cari') ?? '', 'url');
    }
}

?>