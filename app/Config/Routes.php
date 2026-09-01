<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Route Group khusus Client (Tanpa prefix URL)
$routes->group('', ['namespace' => 'App\Controllers\Client'], static function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('signin', 'Home::signin');
    $routes->post('signin', 'Home::signin');

    $routes->get('signup', 'Home::signup');
    $routes->post('signup', 'Home::signup');
    $routes->post('signup/(:segment)', 'Home::signup/$1');

    $routes->post('signout', 'Home::signout');

    $routes->set404Override('Home::_404');

    $routes->get('cek-pesanan', 'TrackPesanan::index');
    $routes->post('cek-pesanan/cek', 'TrackPesanan::cek');

    $routes->get('konfirmasi', 'Konfirmasi::index');
    $routes->post('konfirmasi/kirim', 'Konfirmasi::kirim');

    $routes->get('kategori/(:segment)', 'Kategori::index/$1');
    $routes->get('produk/(:segment)', 'Produk::index/$1');
    $routes->get('page/(:segment)', 'Page::index/$1');
    $routes->get('blog/(:segment)', 'Blog::single/$1');

    $routes->get('katalog', 'Shop::index');
    $routes->get('katalog/(:segment)', 'Shop::index/$1');

    $routes->get('keranjang', 'Home::keranjang');

    $routes->get('home/formatc/(:segment)', 'Home::formatc/$1');
    $routes->get('home/invoice', 'Home::invoice');

    $routes->get('assync/pesanan', 'Assync::pesanan');
    $routes->get('assync/lacakiriman', 'Assync::lacakiriman');
    $routes->get('assync/pesananterakhir', 'Assync::pesananterakhir');
    $routes->get('assync/loadalamat', 'Assync::loadalamat');
    $routes->post('assync/prosesbeli', 'Assync::prosesbeli');
    $routes->post('assync/updatekeranjang', 'Assync::updateKeranjang');
    $routes->post('assync/hapuskeranjang', 'Assync::hapusKeranjang');
    $routes->post('assync/getkab', 'Assync::getKab');
    $routes->post('assync/getkec', 'Assync::getKec');
    $routes->post('assync/updatepesanan', 'Assync::updatePesanan');
    $routes->post('assync/batalkanPesanan', 'Assync::batalkanPesanan');
    $routes->post('assync/tambahalamat', 'Assync::tambahalamat');
    $routes->post('assync/getAlamat', 'Assync::getAlamat');
    $routes->post('assync/hapusAlamat', 'Assync::hapusAlamat');
    $routes->post('assync/updateprofil', 'Assync::updateProfil');
    $routes->post('assync/updatepass', 'Assync::updatePass');

    $routes->get('checkout', 'Checkout::index');
    $routes->post('checkout', 'Checkout::index');
    $routes->get('checkout/alamat', 'Checkout::alamat');
    $routes->post('checkout/simpanalamat', 'Checkout::simpanAlamat');
    $routes->get('checkout/kurir', 'Checkout::kurir');
    $routes->post('checkout/simpankurir', 'Checkout::simpanKurir');
    $routes->get('checkout/bayar', 'Checkout::bayar');
    $routes->post('checkout/simpanbayar', 'Checkout::simpanBayar');


    $routes->get('akun', 'Manage::index');
    $routes->get('manage/pesanan', 'Manage::pesanan');
    $routes->get('manage/detailpesanan', 'Manage::detailpesanan');
    $routes->get('manage/cetakinvoice', 'Manage::cetakInvoice');
    $routes->get('manage/lacakpaket/(:segment)', 'Manage::lacakpaket/$1');
    $routes->post('manage/konfirmasi', 'Manage::konfirmasi');

});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // Auth & Dashboard
    $routes->get('/', 'Admin::index');
    $routes->get('login', 'Admin::login');
    $routes->get('logout', 'Admin::logout');
    $routes->post('auth', 'Admin::auth');

    // Pesanan & Laporan
    $routes->get('pesanan', 'Admin::pesanan');
    $routes->get('laporantransaksi', 'Admin::laporantransaksi');
    $routes->post('laporantransaksi', 'Admin::laporantransaksi');

    // Slider
    $routes->get('slider', 'Admin::slider');
    $routes->get('sliderform', 'Admin::sliderform');
    $routes->get('sliderform/(:segment)', 'Admin::sliderform/$1');
    $routes->post('sliderform', 'Admin::sliderform');
    $routes->post('sliderform/(:segment)', 'Admin::sliderform/$1');
    $routes->post('hapus_slider', 'Admin::hapusslider');

    // Produk
    $routes->get('produk', 'Admin::produk');
    $routes->post('produk', 'Admin::produk');
    $routes->get('produkform', 'Admin::produkform');
    $routes->get('produkform/(:segment)', 'Admin::produkform/$1');
    $routes->post('produkform/(:segment)', 'Admin::produkform/$1');

    // API - Pesanan & Cetak
    $routes->post('api/pesanan', 'Api::pesanan');
    $routes->get('api/detailpesanan', 'Api::detailpesanan');
    $routes->get('api/cetakInvoice', 'Api::cetakInvoice');
    $routes->get('api/cetakLabel', 'Api::cetakLabel');
    $routes->get('api/lacakiriman', 'Api::lacakiriman');
    $routes->post('api/updatepesanan', 'Api::updatepesanan');
    $routes->post('api/batalkanpesanan', 'Api::batalkanpesanan');
    $routes->post('api/inputresi', 'Api::inputresi');
    $routes->post('api/terimapesanan', 'Api::terimapesanan');

    // API - Foto Produk
    $routes->post('api/uploadFotoProduk', 'Api::uploadFotoProduk');
    $routes->post('api/uploadFotoResult/(:segment)', 'Api::uploadFotoResult/$1');
    $routes->post('api/hapusFotoProduk/(:segment)', 'Api::hapusFotoProduk/$1');
    $routes->post('api/jadikanFotoUtama/(:segment)', 'Api::jadikanFotoUtama/$1');

    // API - Produk & Varian (PERBAIKAN DILAKUKAN DI SINI)
    $routes->post('api/tambahproduk', 'Api::tambahproduk');
    $routes->post('api/hapusproduk', 'Api::hapusproduk');
    $routes->post('api/updateproduk', 'Api::updateproduk');
    $routes->post('api/variasiform/(:segment)', 'Api::variasiform/$1'); // Fixed: (:segment)
    $routes->post('api/variansave/(:segment)', 'Api::variansave/$1');   // Fixed: (:segment)
    $routes->post('api/varianadd', 'Api::varianadd');
    $routes->post('api/varianhapus', 'Api::varianhapus');
});



