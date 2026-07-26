<?=  $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-breadcrumb first-section">
  <nav aria-label="breadcrumb first-section">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= site_url(); ?>">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Invoice</li>
      </ol>
  </nav>
</div>

<form class="pt-0 pb-5">
    <div class="container py-4">
        <div class="mb-4">
            <div class="col-lg-8 mx-auto">
                <div class="py-4 px-3">
                    <div class="row align-items-center g-3">
                        <div class="col-md-2 d-none d-md-block text-center">
                            <i class="fas fa-check-circle text-success fs-1"></i>
                        </div>

                        <div class="col-md-10">
                            <div class="fs-6 text-muted">
                                Order ID #<?= esc($data->invoice) ?>
                            </div>

                            <h4 class="title-theme fw-semibold mb-0">
                                Terima Kasih <?= esc($namaUser) ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(!session()->has('usrid')): ?> 
          
            <div class="alert alert-warning text-center mb-4"> 
                
                <div class="fs-4 fw-medium text-danger mb-2">
                <i class="fas fa-exclamation-circle"></i> PERHATIAN
                </div>
                
                <p class="mb-3">
                Karena Anda tidak terdaftar sebagai member <b><?= $set->nama ?></b>, mohon dengan sangat untuk menyimpan Nomor Invoice di bawah ini baik-baik agar dapat melakukan tracking atau cek status pesanan Anda kedepannya.
                </p>
                
                <div class="fs-5 fw-bold text-primary pt-2"> <!-- pt-2 menggantikan p-t-12 -->
                <i class="fas fa-copy clip cursor-pointer" data-clipboard-text="<?= $data->invoice; ?>"></i> &nbsp;
                <?= $data->invoice; ?>
                </div>
                
            </div>

            <?php endif; ?>
        </div>

        <div class="row">
          <div class="col-md-6 mb-4">
              <h4 class="title-theme fw-bold mb-4">
                  Pembayaran
              </h4>

              <div class="section p-4">

              <?php
							  if($data->transfer > 0){
              ?>
                      <div class="pb-4">

                          <?php if ($ubahMetode) : ?>

                              <div class="row mb-4">

                                  <div class="col-12 mb-3">
                                      <h5 class="text-dark">Mohon lakukan pembayaran sejumlah</h5>
                                      <span class="fs-2 text-danger fw-bold">
                                          Rp <?= number_format($bayarTotal, 0, ',', '.') ?>
                                      </span>
                                  </div>

                                  <div class="col-12 mb-3">
                                      <h5 class="text-dark">Pilih Metode Pembayaran :</h5>
                                  </div>

                                  <div class="col-12">
                                      <div class="row metode-bayar g-3">

                                          <?php if ($set->payment_cod == 1) : ?>
                                              <div class="col-md-6">
                                                  <div class="metode-item cod" onclick="bayarCOD()">
                                                      <i class="cek fas fa-check-circle fs-4"></i>
                                                      <img class="icon" src="<?= base_url('assets/img/cod.png') ?>" alt="COD">
                                                      <br>
                                                      Bayar Ditempat
                                                      <br>&nbsp;
                                                  </div>
                                              </div>
                                          <?php endif; ?>

                                          <?php if ($set->payment_transfer == 1) : ?>
                                              <div class="col-md-6">
                                                  <div class="metode-item manual" onclick="bayarManual()">
                                                      <i class="cek fas fa-check-circle fs-4"></i>
                                                      <img class="icon" src="<?= base_url('assets/img/transfer.png') ?>" alt="Transfer">
                                                      <br>
                                                      Transfer Manual
                                                      <br>&nbsp;
                                                  </div>
                                              </div>
                                          <?php endif; ?>

                                      </div>
                                  </div>

                              </div>

                          <?php endif; ?>

                          <!-- Transfer Manual -->
                          <div class="row pt-2 bayarmanual" style="display:none;">
                              <div class="col-12 mb-3">
                                  <h5 class="text-dark">
                                      Silakan transfer pembayaran ke rekening berikut:
                                  </h5>
                              </div>

                              <div class="col-12">

                                  <?php foreach ($bank as $rekening) : ?>

                                      <div class="border-start border-4 ps-3 py-2 mb-3 border-warning">
                                          <h5 class="mb-1">
                                              <span class="text-danger fw-bold">
                                                  Bank <?= esc($rekening->nama) ?> :
                                              </span>

                                              <span class="text-success fw-bold">
                                                  <?= esc($rekening->norek) ?>
                                              </span>
                                          </h5>

                                          <small class="text-muted">
                                              a/n <?= esc($rekening->atas_nama) ?><br>
                                              KCP <?= esc($rekening->kcp) ?>
                                          </small>
                                      </div>

                                  <?php endforeach; ?>

                                  <p class="mt-4 mb-2 fw-bold">
                                      PENTING:
                                  </p>

                                  <ul class="ps-3">
                                      <li>Mohon lakukan pembayaran dalam <strong>1 × 24 jam</strong>.</li>
                                      <li>Sistem akan otomatis mendeteksi apabila pembayaran sudah masuk.</li>
                                      <li>Jika status pembayaran belum berubah setelah transfer, silakan lakukan konfirmasi pembayaran.</li>
                                      <li>Pesanan akan dibatalkan otomatis apabila pembayaran tidak dilakukan.</li>
                                  </ul>

                              </div>
                          </div>

                          <!-- COD -->
                          <div class="row pt-2 bayarcod" style="display:none;">

                              <div class="col-12 mb-3">
                                  <h5 class="text-dark">
                                      Pembayaran COD akan dikenakan biaya tambahan sebesar
                                      <span class="text-success fw-bold">
                                          Rp <?= number_format($biayaCod, 0, ',', '.') ?>
                                      </span>
                                  </h5>
                              </div>

                              <div class="col-12">

                                  <p class="fw-bold mb-2">
                                      PENTING:
                                  </p>

                                  <ul class="ps-3">
                                      <li>Mohon lakukan pembayaran kepada kurir saat barang diterima.</li>
                                      <li>Apabila tidak melakukan pembayaran saat kurir datang, pesanan akan dibatalkan dan akun akan ditinjau kembali apabila terdeteksi melakukan fake order.</li>
                                  </ul>

                              </div>

                          </div>

                      </div>

                      <a href="<?= site_url('manage/pesanan') ?>"
                        class="btn btn-success btn-lg w-100 text-center bayarcod"
                        style="display:none;">
                          <i class="fa fa-chevron-right"></i>
                          <strong>LANJUT KE PESANAN</strong>
                      </a>

                      <a href="<?= session()->has('usrid')
                          ? site_url('manage/pesanan?konfirmasi=' . $data->id)
                          : site_url('konfirmasi') ?>"
                        class="btn btn-warning btn-lg w-100 text-center bayarmanual"
                        style="display:none;">
                          <strong>KONFIRMASI PEMBAYARAN</strong>
                          <i class="fa fa-chevron-circle-right"></i>
                      </a>

                  <?php } ?>

              </div>
          </div>

          <div class="col-md-6 mb-4">

              <h4 class="title-theme fw-bold mb-4">
                  Informasi Pengiriman
              </h4>

              <div class="section p-4 mb-4">

                  <div class="row gy-4">

                      <div class="col-md-6">
                          <h5 class="text-dark mb-2">Nama Penerima</h5>
                          <p class="color1 mb-0">
                              <?= strtoupper(strtolower($alamat->nama)); ?>
                          </p>
                      </div>

                      <div class="col-md-6">
                          <h5 class="text-dark mb-2">No. Telepon</h5>
                          <p class="color1 mb-0">
                              <?= $alamat->no_hp; ?>
                          </p>
                      </div>

                      <div class="col-12">
                          <h5 class="text-dark mb-2">Alamat Pengiriman</h5>

                          <p class="color1 mb-0">
                              <?php
                                  echo strtoupper(strtolower(
                                      "{$alamat->alamat}<br>{$alamat->nama_kecamatan}, {$alamat->nama_kabupaten}<br>Kodepos {$alamat->kodepos}"
                                  ));
                              ?>
                          </p>
                      </div>

                  </div>

              </div>


          <h4 class="title-theme fw-bold mb-4">
              Produk Pesanan
          </h4>

          <div class="produk">

              <?php
              foreach ($transaksi as $trx) {

                  $ongkir = isset($ongkir)
                      ? $ongkir + $trx->ongkir
                      : $trx->ongkir;

                  foreach ($trx->produk as $item) {

                      $variasi = "";

                      $variasi =
                          "<br><small class='text-danger'>Variasi: {$item->nama_warna}</small>";
                  
              ?>

                  <div class="produk-item row align-items-center g-3 mb-4">

                      <div class="col-3 px-0 my-0">
                          <div
                              class="img rounded"
                              style="background-image:url('<?= base_url('cdn/uploads/'. $item->gambar) ?>')">
                          </div>
                      </div>

                      <div class="col-9 my-0">
                          <div class="ps-2 fw-semibold">
                              <?= $item->nama . $variasi; ?>

                              <p class="text-info mb-0">
                                  <?= $item->jumlah; ?> ×
                                  Rp <?= number_format($item->harga, 0, ',', '.'); ?>
                              </p>
                          </div>
                      </div>

                  </div>

              <?php
                  }
              }
              ?>

          </div>

      </div>
      </div>
    </div>
</form>

<script>

  $(function(){
	<?php
		if(!$ubahMetode){
			if($data->metode_bayar == 1){
				echo "bayarCOD();";
			}elseif($data->metode_bayar == 2){
				echo "bayarManual();";
			}
		}
	?>
  });

  function bayarCOD() {
    $(".bayarmanual").hide();

    const requestData = {
        id: "<?= $data->id ?>",
        biaya: "<?= $biayaCod ?>",
        metode: 1,
        [$("#names").val()]: $("#tokens").val()
    };

    $.ajax({
        url: "<?= site_url('assync/updatepesanan') ?>",
        type: "POST",
        data: requestData,
        dataType: "json",

        success: function (response) {
            updateToken(response.token);

            if (response.success) {
                $(".metode-item").removeClass("active");
                $(".metode-item.cod").addClass("active");
                $(".bayarcod").show();
            } else {
                Swal.fire(
                    "Gagal request COD",
                    "Pembayaran melalui COD sedang terkendala. Silakan hubungi admin toko.",
                    "error"
                );
            }
        },

        error: function (xhr, status, error) {
            console.error("Update pesanan gagal:", {
                status,
                error,
                response: xhr.responseText
            });

            Swal.fire(
                "Terjadi Kesalahan",
                "Tidak dapat terhubung ke server. Silakan coba beberapa saat lagi.",
                "error"
            );
        }
    });
  }

  function bayarManual() {
      $(".bayarcod").hide();

      const requestData = {
          id: "<?= $data->id ?>",
          metode: 2,
          [$("#names").val()]: $("#tokens").val()
      };

      $.ajax({
          url: "<?= site_url('assync/updatepesanan') ?>",
          type: "POST",
          data: requestData,
          dataType: "json",

          success: function (response) {
              updateToken(response.token);

              if (response.success) {
                  $(".metode-item").removeClass("active");
                  $(".metode-item.manual").addClass("active");
                  $(".bayarmanual").show();
              } else {
                  Swal.fire(
                      "Gagal request Transfer",
                      "Pembayaran melalui transfer sedang terkendala. Silakan hubungi admin toko.",
                      "error"
                  );
              }
          },

          error: function (xhr, status, error) {
              console.error("Update pesanan gagal:", {
                  status,
                  error,
                  response: xhr.responseText
              });

              Swal.fire(
                  "Terjadi Kesalahan",
                  "Tidak dapat terhubung ke server. Silakan coba beberapa saat lagi.",
                  "error"
              );
          }
      });
  }
</script>

<?= $this->endSection() ?>