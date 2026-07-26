<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb Bootstrap 5 -->
<div class="container first-section">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb ps-3 pe-2 pt-4 px-lg-0">
      <li class="breadcrumb-item">
        <a href="<?php echo site_url(); ?>" class="text-decoration-none">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="<?php echo site_url("manage/pesanan"); ?>" class="text-decoration-none">Pesananku</a>
      </li>
      <li class="breadcrumb-item active text-dark" aria-current="page">
        Lacak Paket Pengiriman
      </li>
    </ol>
  </nav>
</div>

<!-- Main Content -->
<div class="container py-5">
  <div class="row">
    
    <!-- Title Section -->
    <div class="col-12 text-center" style="margin-bottom: 30px;">
      <h3 class="fw-bold text-primary mb-0">
        Order ID <span class="text-success">#<?= $orderid; ?></span>
      </h3>
    </div>

    <!-- Informasi Paket -->
    <div class="col-md-5 mb-4">
      <h4 class="fw-bold text-primary mb-3">
        Informasi Paket
      </h4>
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          
          <div class="mb-3">
            <label class="text-muted d-block mb-1">Kurir Pengiriman:</label>
            <span class="badge bg-warning text-dark fs-6 fw-bold">
              <?= strtoupper(strtolower($transaksi->nama_kurir." - ".$transaksi->nama_paket)); ?>
            </span>
          </div>

          <div class="mb-3">
            <label class="text-muted d-block mb-1">No Resi Pengiriman:</label>
            <span class="fs-5 text-success fw-semibold">
              <?= strtoupper(strtolower($transaksi->resi)); ?>
            </span>
          </div>

          <hr class="text-muted opacity-25">

          <div>
            <label class="text-muted d-block mb-1">Waktu Pengiriman:</label>
            <span class="text-success fw-medium">
              <i class="fa fa-clock me-1"></i>
              <?php
              $timestamp = strtotime($transaksi->kirim);
              $formatter = new IntlDateFormatter(
                  'id_ID', 
                  IntlDateFormatter::LONG, 
                  IntlDateFormatter::SHORT, 
                  'Asia/Jakarta', 
                  IntlDateFormatter::GREGORIAN, 
                  "dd MMM yyyy HH:mm" 
              );
              $tanggalIndo = $formatter->format($timestamp);
              echo $tanggalIndo; ?> WIB
            </span>
          </div>

        </div>
      </div>
    </div>

    <!-- Status Pengiriman -->
    <div class="col-md-7 mb-4">
      <h4 class="fw-bold text-primary mb-3">
        Status Pengiriman
      </h4>
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="overflow-hidden" id="load">
            <div class="d-flex align-items-center text-muted">
              <i class="fa fa-spinner fa-spin fa-2x me-3 text-primary"></i>
              <h5 class="mb-0 fs-6">Menghubungi ekspedisi, mohon tunggu sebentar...</h5>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
  $(function(){
    $("#load").load("<?= site_url("assync/lacakiriman?orderid=".$orderid); ?>");
  });
</script>

<?= $this->endSection() ?>