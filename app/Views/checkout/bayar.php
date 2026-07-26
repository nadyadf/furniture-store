<div class="row">
    <div class="col-md-8 mb-4">
        <div class="section p-4 mb-4">
            <div class="fs-5 fw-bold mb-4">
                Informasi Pengiriman
            </div>

            <div class="px-3 py-2 bg-soft rounded mb-4">
                <div class="fw-semibold">
                    <?=$alamat->nama . " (" . $alamat->no_hp . ")"?>
                </div>
                <i><?=$alamat->alamat . " - " . $alamat->kodepos?></i>
            </div>

            <div class="mb-2 fw-semibold">
                Pesanan dikirim dari
            </div>

            <div class="px-3 py-2 bg-soft rounded d-inline-block mb-4">
                <div class="fw-semibold">
                    <i class="fas fa-map-marker-alt"></i>
                    <?=$kota_gudang?>
                </div>
            </div>

            <div class="mb-2 fw-semibold">
                Kurir Pengiriman
            </div>

            <div class="px-3 py-2 bg-soft rounded d-inline-block">
                <div class="fw-semibold">
                    <?=$kurir . " - " . $paket?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
      <div class="section p-4 mb-4">
          <form id="cekout">
              <input type="hidden" id="subtotal" value="<?=$total;?>">
              <input type="hidden" id="ongkir" value="<?=$ongkir;?>">
              <input type="hidden" name="metode" id="metode" value="1">
              <input type="hidden" name="total" id="total" value="<?=$total + $ongkir;?>">
              <input type="hidden" name="biaya_cod" id="biayacod" value="<?=$biaya_cod;?>">
              <input type="hidden" name="metode_bayar" id="metode_bayar" value="0">
          </form>

          <div class="fs-5 fw-bold mb-4">
              Informasi Pembayaran
          </div>

          <!-- Subtotal -->
          <div class="row align-items-center mb-2">
              <div class="col-6">
                  <p class="mb-0">Subtotal</p>
              </div>
              <div class="col-6 text-end">
                  <p class="mb-0">
                      Rp <span id="subtotalbayar"><?=number_format($total, 0, ',', '.')?></span>
                  </p>
              </div>
          </div>

          <div class="row align-items-center mb-2">
              <div class="col-6">
                  <p class="mb-0">Ongkos Kirim</p>
              </div>
              <div class="col-6 text-end">
                  <p class="mb-0">
                      Rp <?=number_format($ongkir, 0, ',', '.')?>
                  </p>
              </div>
          </div>

          <div class="row align-items-center mb-2 codon" style="display:none;">
              <div class="col-6">
                  <p class="mb-0">Biaya COD</p>
              </div>
              <div class="col-6 text-end">
                  <p class="mb-0">
                      Rp <span id="byacod"><?=number_format($biaya_cod, 0, ',', '.')?></span>
                  </p>
              </div>
          </div>

          <hr>

          <div class="row align-items-center">
              <div class="col-4">
                  <h5 class="mb-0">Total</h5>
              </div>
              <div class="col-8 text-end">
                  <h5 class="mb-0 fw-bold">
                      Rp <span id="totalbayar"><?=number_format($total + $ongkir, 0, ',', '.')?></span>
                  </h5>
              </div>
          </div>
      </div>

      <div class="section p-4">
        <div class="fs-5 fw-semibold mb-4">
            Pilih Metode Pembayaran
        </div>

        <div class="metode mb-4 metodelainnya">
            <?php
                if ($payment_cod == 1 && $cod == 1) {
            ?>
                <div class="row mx-0 mb-3 metodebayar methods align-items-center"
                    id="bayarcod"
                    data-metode="1"
                    data-bayar="cod">

                    <i class="fas fa-check-circle"></i>

                    <div class="col-md-6 col-4 px-0">
                        <div class="bg-logo p-1">
                            <img src="<?=base_url('assets/img/cod.png')?>" class="w-100" alt="COD">
                        </div>
                    </div>

                    <div class="col-md-6 col-8">
                        COD
                    </div>
                </div>
            <?php
                }

                if ($payment_transfer == 1) {
            ?>
                <div class="row mx-0 mb-3 metodebayar methods align-items-center"
                    id="bayartransfer"
                    data-metode="2"
                    data-bayar="transfer">

                    <i class="fas fa-check-circle"></i>

                    <div class="col-md-6 col-4 px-0">
                        <div class="bg-logo p-1">
                            <img src="<?=base_url('assets/img/transfer.png')?>" class="w-100" alt="Transfer">
                        </div>
                    </div>

                    <div class="col-md-6 col-8">
                        Transfer Manual
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="text-danger" id="error-bayar">
            <i>Belum dapat menyelesaikan pesanan, silakan lengkapi alamat dan total beserta ongkos kirim terlebih dahulu.</i>
        </div>

        <div class="text-warning" id="proses" style="display:none;">
            <h5 class="mb-0">
                <i class="fas fa-compact-disk fa-spin"></i>
                <i>Memproses pesanan, mohon tunggu sebentar</i>
            </h5>
        </div>

        <div class="pembayaran" style="display:none;">
            <a href="javascript:void(0);"
              onclick="checkoutWA();"
              class="btn btn-success btn-lg w-100 mb-3">
                <i class="fab fa-whatsapp"></i>
                Checkout Whatsapp
            </a>

            <a href="javascript:void(0);"
              onclick="checkoutNow();"
              class="btn btn-primary btn-lg w-100">
                <i class="fas fa-chevron-circle-right"></i>
                Lanjut Pembayaran
            </a>
        </div>
      </div>
  </div>
  
</div>

<script>
  $(function() {

    $(".methods").on("click",function(){
        var valmet = $("#metode_bayar").val();
        $("#metode_bayar").val($(this).data("metode"));
        $(".methods").removeClass("active");
        $("#bayar"+$(this).data("bayar")).addClass("active");
        showCheckout();
    });

  });

  function showCheckout(){
      $(".pembayaran").show();
      $("#error-bayar").hide();
      $("#proses").hide();
  }

  function checkoutNow() {
      $(".pembayaran").hide();
      $("#proses").show();

      $.ajax({
          url: "<?=site_url('checkout/simpanbayar')?>",
          type: "POST",
          data: $("#cekout").serialize(),
          dataType: "json",

          success: function (data) {
              if (data.success) {
                  window.location.href = data.url;
              } else {
                  Swal.fire(
                      "Gagal checkout",
                      "Terjadi kesalahan saat melakukan checkout. Periksa kembali metode pembayaran yang Anda pilih.",
                      "error"
                  );

                  $(".pembayaran").show();
                  $("#proses").hide();
              }
          },

          error: function (xhr, status, error) {

            Swal.fire(
                "Gagal checkout",
                xhr.responseText,
                "error"
            );


            $(".pembayaran").show();
            $("#proses").hide();
          }
      });
  }

  

</script>