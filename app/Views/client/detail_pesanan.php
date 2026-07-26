<?=  $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb Bootstrap 5 -->
<div class="container first-section">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb ps-3 pe-2 pt-4 px-lg-0">
      
      <li class="breadcrumb-item">
        <a href="<?= site_url(); ?>" class=" text-decoration-none">Home</a>
      </li>
      
      <li class="breadcrumb-item">
        <a href="<?= site_url('manage/pesanan'); ?>" class=" text-decoration-none">Pesananku</a>
      </li>
      
      <li class="breadcrumb-item active text-dark" aria-current="page">
        Detail Pesanan
      </li>
      
    </ol>
  </nav>
</div>

<form class="pb-5" style="padding-bottom: 5.3rem;"> <!-- Menggantikan p-b-85 -->
  <div class="container py-5"> <!-- py-5 otomatis memberikan padding top & bottom setara 3rem (~48px) -->
    <?php if (!empty($transaksi)) :
      foreach ($transaksi as $trx) : ?>
        <?php if(!session()->has('usrid')): ?> <!-- Menyesuaikan session CI4 -->
          
          <div class="alert alert-warning text-center mb-4"> <!-- mb-4 menggantikan m-b-32 -->
            
            <div class="fs-4 fw-medium text-danger mb-2">
              <i class="fas fa-exclamation-circle"></i> PERHATIAN
            </div>
            
            <p class="mb-3">
              Karena Anda tidak terdaftar sebagai member <b><?= $set->nama ?></b>, mohon dengan sangat untuk menyimpan Nomor Invoice di bawah ini baik-baik agar dapat melakukan tracking atau cek status pesanan Anda kedepannya.
            </p>
            
            <div class="fs-5 fw-bold text-primary pt-2"> <!-- pt-2 menggantikan p-t-12 -->
              <i class="fas fa-copy clip cursor-pointer" data-clipboard-text="<?= $trx->pembayaran->invoice; ?>"></i> &nbsp;
              <?= $trx->pembayaran->invoice; ?>
            </div>
            
          </div>

        <?php endif; ?>
        
        <div class="row g-4"> <!-- g-0 menggantikan m-lr-0 untuk menghilangkan gutter margin kiri-kanan -->
          <div class="col-md-7 mb-4"> <!-- mb-4 menggantikan m-b-30 -->
            <h4 class="title-theme fw-bold pb-3"> <!-- fw-bold menggantikan font-bold, pb-3 menggantikan p-b-20 -->
              <?php if(session()->has('usrid')){ ?> <!-- Menyesuaikan session CI4 -->
                Order ID <span class="text-success">#<?=$trx->orderid;?></span>
              <?php }else{ ?>
                Invoice: <span class="text-success"><?=$trx->pembayaran->invoice?></span>
              <?php } ?>
            </h4>
            
            <!-- Menggunakan utility border & shadow-sm bawaan BS5 sebagai pengganti box .section -->
            <div class="card p-4 px-md-4 px-3 border-0 shadow-sm rounded"> <!-- Mengharmoniskan p-lr-24, p-tb-20, m-lr-0-xl, p-lr-15-sm -->
              <div class="row mb-2"> <!-- mb-2 menggantikan m-b-12 -->
                <div class="col-md-6 py-2"> <!-- py-2 menggantikan p-b-10 p-t-10 -->
                  <p class="mb-2"> <!-- mb-2 menggantikan m-b-10 -->
                    Waktu Pemesanan :<br/>
                    <?php
                    $timestampOrder = strtotime($trx->tgl);
                    $formatter = new IntlDateFormatter(
                        'id_ID', 
                        IntlDateFormatter::LONG, 
                        IntlDateFormatter::SHORT, 
                        'Asia/Jakarta', 
                        IntlDateFormatter::GREGORIAN, 
                        "dd MMM yyyy HH:mm" 
                    );
                    $tanggalOrder = $formatter->format($timestampOrder);
                    ?>
                    <i class="fw-medium"><?= $tanggalOrder; ?> WIB</i> <!-- fw-medium menggantikan font-medium -->
                  </p>
                  <p class="mb-0">
                    Waktu Pembayaran :<br/>
                    <?php
                    $timestampPay = strtotime($trx->tgl);
                    $tanggalBayar = $formatter->format($timestampPay);
                    ?>
                    <i class="fw-medium"><?php echo $trx->pembayaran->tgl; ?> WIB</i>
                  </p>
                </div>
                
                <div class="col-md-6">
                  <!-- Di Bootstrap 5, teks pada badge / status box sebaiknya menggunakan text-dark jika background-nya warning (kuning) -->
                  <?php if($trx->status == 0){ ?>
                    <!-- Belum Dibayar -->
                    <p class="bg-warning text-dark mb-2 p-2 rounded status-pesanan fw-medium text-center">Belum Dibayar</p>
                    <p class="mb-1">Segera lakukan pembayaran maks. 1x24jam untuk menghindari pembatalan otomatis.</p>
                  <?php }elseif($trx->status == 2 && $trx->resi != ""){ ?>
                    <!-- Dalam Pengiriman -->
                    <p class="bg-primary text-white mb-2 p-2 rounded status-pesanan fw-medium text-center">Sedang Dikirim</p>
                    <p class="mb-1">Pesanan Anda sudah dalam perjalanan, untuk melihat proses pengiriman silahkan cek info dibawah.</p>
                  <?php }elseif($trx->status == 1){ ?>
                    <!-- Sedang Dikemas -->
                    <p class="bg-primary text-white mb-2 p-2 rounded status-pesanan fw-medium text-center">Sedang Dikemas</p>
                    <p class="mb-1">Pesanan sedang dikemas oleh admin dan akan segera dikirim.</p>
                  <?php }elseif($trx->status == 3){ ?>
                    <!-- Selesai -->
                    <p class="bg-success text-white mb-2 p-2 rounded status-pesanan fw-medium text-center">Telah Diterima</p>
                    <p class="mb-1">Pesanan telah diterima oleh pembeli.</p>
                  <?php }elseif($trx->status == 4){ ?>
                    <!-- Dibatalkan -->
                    <p class="bg-danger text-white mb-2 p-2 rounded status-pesanan fw-medium text-center">Pesanan Dibatalkan</p>
                    <p class="mb-1">Pesanan dibatalkan karena <?php echo $trx->keterangan; ?></p>
                  <?php } ?>
                </div>
              </div>
              <a href="<?= site_url("manage/cetakinvoice?id=" . $trx->id) ?>" class="btn w-100 btn-primary mb-2" target="_blank">
                <i class="fas fa-print"></i> &nbsp;CETAK INVOICE
              </a>

              <?php if($trx->status == 0){ ?>
                <a href="<?= site_url("home/invoice?inv=" . $trx->idbayar) ?>" class="btn w-100 btn-success mt-2">
                  <i class="fas fa-receipt"></i> &nbsp;BAYAR PESANAN SEKARANG
                </a>
              <?php } ?>
            </div>

            <h4 class="title-theme fw-bold pt-4 pb-3"> <!-- p-t-30 ke pt-4, p-b-20 ke pb-3 -->
              Produk Pesanan
            </h4>
            <div class="produk">
              <?php
                $total = 0;
                $totalqty = 0;
                foreach($trx->produk as $produk){
                  $total += $produk->harga * $produk->jumlah;
                  $totalqty += $produk->jumlah;
                ?>
                <div class="pb-3"> <!-- p-b-20 disesuaikan ke pb-3 -->
                  <div class="row produk-item g-0"> <!-- m-lr-0 diganti g-0, class 'produk-item' tetap dipertahankan -->
                    <div class="col-4 col-md-3">
                      <?php 
                        $gambarUrl = (!empty($produk->gambar)) 
                          ? base_url('cdn/uploads/'. $produk->gambar) 
                          : base_url('cdn/uploads/default.jpg');
                      ?>
                      <!-- Class 'img' dan style background tetap dipertahankan utuh -->
                      <div class="img" style="background-image:url('<?= $gambarUrl ?>')" alt="IMG"></div>
                    </div>
                    <div class="col-8 col-md-9">
                      <p class="fw-medium mb-1"><?= $produk->nama; ?></p> <!-- font-medium ke fw-medium -->
                      
                      <small class="text-primary d-block mb-1">Warna: <?= $produk->nama_warna; ?></small>
                      
                      <p class="mb-0">
                        Rp <?= number_format($produk->harga, 0, ',', '.') ?> 
                        <span class="fs-14">x <?= $produk->jumlah; ?></span> <!-- Class kustom 'fs-14' dipertahankan -->
                      </p>
                    </div>
                  </div>
                </div>
              <?php
                }
                $beratkg = $trx->berat/1000;
                $beratkg = round($beratkg,2,PHP_ROUND_HALF_UP);
              ?>
            </div>
          </div>

          <div class="col-md-5 mb-4"> <!-- m-b-30 ke mb-4 -->
            <h4 class="title-theme fw-bold pb-3"> <!-- font-bold ke fw-bold, p-b-20 ke pb-3 -->
              Rincian Pembayaran
            </h4>
            
            <div class="section p-4 px-md-4 px-3 mb-4"> <!-- Harmonisasi p-lr-24 p-tb-30 m-lr-0-xl p-lr-15-sm m-b-30 -->
              <div class="row py-2"> <!-- p-tb-8 ke py-2 -->
                <div class="col-6">TOTAL HARGA<br/>(<?=$totalqty?> BARANG)</div>
                <div class="col-6 fw-medium text-end">Rp <?= number_format($total, 0, ',', '.') ?></div> <!-- font-medium ke fw-medium, text-right ke text-end -->
              </div>
              <hr/>
              <div class="row py-2">
                <div class="col-6">Total Ongkir (<?=$beratkg?>kg)</div>
                <div class="col-6 fw-medium text-end">Rp <?= number_format($trx->ongkir, 0, ',', '.') ?></div>
              </div>
              <?php if($trx->biaya_cod > 0){ ?>
              <hr/>
              <div class="row py-2">
                <div class="col-6">Biaya COD</div>
                <div class="col-6 fw-medium text-end">Rp <?= number_format($trx->biaya_cod, 0, ',', '.') ?></div>
              </div>
              <?php } ?>
              <hr/>
              <div class="row py-2">
                <div class="col-6 fw-medium text-primary">Grand Total</div>
                <div class="col-6 fw-bold text-end text-primary">Rp <?= number_format($total+$trx->biaya_cod+$trx->ongkir, 0, ',', '.') ?> </div>
              </div>
            </div>

            <h4 class="title-theme fw-bold pb-3">
              Informasi Pengiriman
            </h4>
            
            <div class="section p-4 px-md-4 px-3"> <!-- Harmonisasi padding responsif -->
              <div class="pb-2"> <!-- p-b-14 ke pb-2 -->
                <div class="pb-3"> <!-- p-b-20 ke pb-3 -->
                  <h5 class="text-black pb-2">PENGIRIMAN DARI</h5> <!-- p-b-8 ke pb-2 -->
                  <div class="bg-soft fw-medium py-2 px-3"><i class="fas fa-map-marker-alt"></i> <?= $trx->kota_asal; ?></div> <!-- p-tb-8 p-lr-12 ke py-2 px-3, class kustom dipertahankan -->
                </div>
                <div class="pb-3">
                  <h5 class="pb-2">KURIR & PAKET</h5>
                  <div class='bg-soft fw-medium py-2 px-3'>
                    <?= strtoupper($trx->nama_kurir)." ".strtoupper($trx->nama_paket);?> <!-- Ditambahkan echo/shorttag agar teks ter-render -->
                  </div>
                </div>
                <div class="pb-2"> <!-- p-b-10 ke pb-2 -->
                  <h5 class="text-black pb-2">RESI PENGIRIMAN</h5> <!-- p-b-10 ke pb-2 -->
                  <p class="text-success fw-medium mb-0"><?php echo $trx->resi; ?></p>
                </div>
                <hr/>
                <div class="row pt-2"> <!-- p-t-20 ke pt-2 -->
                  <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="text-black pb-2">Nama Penerima</h5>
                    <p class="mb-0"><?php echo strtoupper(strtolower($trx->alamat->nama)); ?></p>
                  </div>
                  <div class="col-md-6">
                    <h5 class="text-black pb-2">No Telepon</h5>
                    <p class="mb-0"><?php echo $trx->alamat->no_hp; ?></p>
                  </div>
                </div>
                <div class="row pt-3"> <!-- p-t-20 ke pt-3 -->
                  <div class="col-md-12">
                    <h5 class="text-black pb-2">Alamat Pengiriman</h5>
                    <p class="mb-0">
                      <?= strtoupper(strtolower($trx->alamat->alamat)); ?><br>
                      <?= $trx->alamat->nama_kecamatan.", ".$trx->alamat->tipe_kabupaten." ".$trx->alamat->nama_kabupaten; ?><br>
                      Kodepos <?= $trx->alamat->kodepos; ?>
                    </p>
                  </div>
                </div>
              </div>
              
              <?php if($trx->resi != "" && $trx->kurir != "cod" && $trx->kurir != "toko"){ ?>
                <div class="mt-4"></div> <!-- m-t-30 ke mt-4 -->
                <!-- btn-block diganti w-100 agar tombol melebar penuh sesuai standar BS5 -->
                <a href="<?= site_url("manage/lacakpaket/".$trx->orderid); ?>" class="btn btn-warning btn-lg w-100">
                  <i class="fas fa-shipping-fast"></i> &nbsp;<b>CEK STATUS PENGIRIMAN</b>
                </a>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="mb-4 mx-auto">
        <div class="card p-4 py-4">
          <div class="text-danger fs-5 text-center">
            Pesanan yang Anda cari tidak ditemukan.
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</form>

<?= $this->endSection() ?>