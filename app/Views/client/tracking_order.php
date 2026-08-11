<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container py-4 first-section">
  <!-- Title Section -->
  <div class="text-center mb-4">
    <h2 class="fw-bold text-primary">Cek Status Pesanan</h2>
    <p class="text-muted">Masukkan nomor invoice atau ID pesanan Anda untuk melacak status pesanan</p>
  </div>

  <!-- Form Card Section -->
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
        <form id="cekpesanan">
          <!-- Input Order ID -->
          <div class="mb-3 text-center">
            <label for="orderid" class="form-label fw-semibold text-secondary">
              Nomor Invoice / ID Pesanan
            </label>
            <input 
              type="text" 
              class="form-control form-control-lg text-center fw-bold" 
              id="orderid" 
              name="orderid" 
              required 
              autocomplete="off"
            />
          </div>

          <!-- Submit Button -->
          <div class="d-grid gap-2">
            <button id="ceksubmit" type="submit" class="btn btn-success btn-lg">
              <i class="fas fa-search me-1"></i> Cek Pesanan
            </button>
          </div>
        </form>

        <!-- Alert Notification -->
        <div class="alert alert-danger text-center mt-3 mb-0 rounded-3 d-none" id="alert-error">
          <i class="fas fa-exclamation-circle me-1"></i>
          Mohon maaf, nomor yang Anda masukkan tidak ditemukan atau pesanan tersebut telah dikaitkan dengan akun terdaftar.
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    $(function(){
        $("#orderid").focus(function(){
            $(".alert").hide();
        });
        
        $("#cekpesanan").on("submit", function(e){
          e.preventDefault();
          
          var hate = $("#ceksubmit").html();
          $("#ceksubmit").prop("disabled", true).html('<i class="fas fa-compact-disc fa-spin"></i> &nbsp;Tunggu sebentar...');
          
          // Ambil token CSRF CI4 (Fallback jika elemen #names/#tokens tidak ada)
          var csrfName = $("#names").length ? $("#names").val() : '<?= csrf_token() ?>';
          var csrfHash = $("#tokens").length ? $("#tokens").val() : '<?= csrf_hash() ?>';

          var postData = {
              "orderid": $("#orderid").val()
          };
          postData[csrfName] = csrfHash;

          $.post("<?=site_url("cek-pesanan/cek")?>", postData, function(data){
              // HAPUS eval() - 'data' sudah berbentuk JSON Object otomatis di CI4
              if (typeof updateToken === 'function' && data.token) {
                  updateToken(data.token);
              } else if ($("#tokens").length && data.token) {
                  $("#tokens").val(data.token);
              }

              if (data.success == true) {
                  // Sesuaikan dengan return controller kamu (order_id)
                  var trxId = data.order_id || data.trxid; 
                  window.location.href = "<?=site_url("manage/detailpesanan/")?>?orderid=" + trxId;
              } else {
                  $("#ceksubmit").prop("disabled", false).html(hate);
                  $(".alert").removeClass("d-none").show(); // Tampilkan alert error
              }
          }, "json").fail(function(){
              // Jika Server 500 / CSRF Mismatch
              $("#ceksubmit").prop("disabled", false).html(hate);
              alert("Terjadi kesalahan sistem, silakan coba lagi.");
          });
      });
    });
</script>

<?= $this->endSection() ?>