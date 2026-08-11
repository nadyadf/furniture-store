<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>


<div class="container py-4 first-section">
  <!-- Title Section -->
  <div class="text-center mb-4">
    <h2 class="fw-bold text-primary">Konfirmasi Pembayaran Pesanan</h2>
  </div>

  <!-- Form Card Section -->
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">

        <!-- Form Konfirmasi -->
        <form method="POST" enctype="multipart/form-data" action="<?= site_url("konfirmasi/kirim") ?>">
          <!-- CI4 CSRF Protection Field -->
          <?= csrf_field() ?>

          <!-- Input Nomor Invoice -->
          <div class="mb-3">
            <label for="invoice" class="form-label fw-semibold text-secondary">
              Nomor Invoice / ID Pesanan
            </label>
            <input 
              type="text" 
              class="form-control form-control-lg fw-bold" 
              id="invoice" 
              name="invoice"
              required 
              autocomplete="off"
            />
          </div>

          <!-- Input Bukti Transfer -->
          <div class="mb-4">
            <label for="bukti" class="form-label fw-semibold text-secondary">
              Bukti Transfer
            </label>
            <input 
              type="file" 
              class="form-control" 
              id="bukti" 
              name="bukti" 
              accept="image/*,application/pdf" 
              required 
            />
            <div class="form-text">Format yang diizinkan: JPG, JPEG, PNG, GIF, PDF</div>
          </div>

          <!-- Submit Button -->
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-lg">
              <i class="fas fa-check-circle me-1"></i> Kirim Bukti Pembayaran
            </button>
          </div>
        </form>

        <!-- Alert Notification Handling -->
        <?php 
          $request = \Config\Services::request();
          $result  = $request->getGet('result');
          $msg     = $request->getGet('msg');
        ?>

        <?php if ($result === 'sukses'): ?>
          <div class="alert alert-success text-center mt-4 mb-0 rounded-3 shadow-sm">
            <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
            <strong>Terima Kasih!</strong> Data konfirmasi pembayaran sudah dikirim ke Admin. Mohon menunggu proses persetujuan (maksimal 1x24 jam). Apabila ada kendala, silakan konfirmasi kepada Admin melalui tombol bantuan.
          </div>
        <?php elseif ($result === 'gagal'): ?>
          <div class="alert alert-danger text-center mt-4 mb-0 rounded-3 shadow-sm">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Mohon Maaf!</strong> Konfirmasi pembayaran gagal dikirim. Silakan periksa kembali data yang Anda masukkan.
            
            <?php if (!empty($msg)): ?>
              <div class="mt-2 pt-2 border-top border-danger-subtle text-start small">
                <strong>Pesan Error:</strong><br/>
                <?= esc($msg) ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>