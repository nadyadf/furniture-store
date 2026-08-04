<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<h4 class="page-title mb-4">Pesanan</h4>

<div class="mb-5">
  <div class="card shadow-sm">
    <div class="card-header row align-items-center g-3">
      <!-- Tabs Pesanan -->
      <div class="col-md-8">
        <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="pesananTab">
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link active bayar" data-item="bayar">
              Belum Dibayar
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link dikemas" data-item="dikemas">
              Perlu Dikirim
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link dikirim" data-item="dikirim">
              Dikirim
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link selesai" data-item="selesai">
              Selesai
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link batal" data-item="batal">
              Dibatalkan
            </a>
          </li>
        </ul>
      </div>

      <!-- Input Cari -->
      <div class="col-md-4">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Cari pesanan..." id="cari" />
          <button class="btn btn-info text-white" type="button" id="btn-cari">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Container Content -->
    <div class="card-body" id="load">
      <div class="text-center py-4 text-muted">
        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
        <p class="mb-0">Loading data...</p>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  // 1. Function Utama Load Data Pesanan Admin via AJAX
  function loadPesanan(loadStatus = 'bayar', page = 1) {
    // Tampilkan Loading Indicator
    $("#load").html(`
      <div class="text-center py-4 text-muted">
        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
        <p class="mb-0">Loading data...</p>
      </div>
    `);

    // Payload data POST (termasuk filter pencarian & CSRF CI4)
    var postData = {
      "cari": $("#cari").val(),
      "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
    };

    $.ajax({
      url: "<?= site_url('admin/api/pesanan') ?>?load=" + loadStatus + "&page=" + page,
      type: 'POST',
      data: postData,
      dataType: 'json',
      success: function(data) {
        // Update CSRF token jika dikirim kembali dari server
        if (typeof updateToken === 'function' && data.token) {
          updateToken(data.token);
        }
        
        // Tampilkan HTML view hasil render dari controller
        $("#load").html(data.result);
      },
      error: function(xhr, status, error) {
        $("#load").html(`
          <div class="text-center py-4 text-danger">
            <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
            <p class="mb-0">Gagal memuat data. Silakan coba lagi.</p>
          </div>
        `);
      }
    });
  }

  $(document).ready(function(){
    // 2. Load Pertama Kali saat halaman dibuka (Default: tab 'bayar', page 1)
    loadPesanan('bayar', 1);

    // 3. Event Handling Klik Tab Status Pesanan
    $(".nav-link").on('click', function(e){
      e.preventDefault();
      
      // Ubah tampilan Active Tab
      $(".nav-link").removeClass("active");
      $(this).addClass("active");

      // Ambil nilai status dari attribute data-item
      var loadStatus = $(this).data("item") || "bayar";

      // Reset pencarian atau tetap pertahankan lalu load data page 1
      loadPesanan(loadStatus, 1);
    });

    // 4. Event Handling Pencarian (Klik Tombol atau Tekan Enter)
    $("#btn-cari").on("click", function(){
      var activeStatus = $(".nav-link.active").data("item") || "bayar";
      loadPesanan(activeStatus, 1);
    });

    $("#cari").on("keyup", function(e){
      if (e.keyCode === 13) { // KeyCode 13 = Enter
        var activeStatus = $(".nav-link.active").data("item") || "bayar";
        loadPesanan(activeStatus, 1);
      }
    });

    // 5. Event Handling Click Pagination AJAX
    $(document).on('click', '.pagination-ajax a, .pagination a', function(e) {
      e.preventDefault();
      
      var urlHref = $(this).attr('href');
      if (!urlHref || urlHref === '#' || urlHref === 'javascript:void(0)') {
        return;
      }

      // Ambil parameter query ?page=X dan ?load=Y dari URL link pagination CI4
      var urlObj = new URL(urlHref, window.location.origin);
      var page = urlObj.searchParams.get('page') || 1;
      var activeStatus = $(".nav-link.active").data("item") || "bayar";

      loadPesanan(activeStatus, page);
    });

    // Submit Cetak Invoice
    $("#cetakInvoice").on("submit", function(e){
      e.preventDefault();
      var invId = $("#inv").val();
      window.open("<?= site_url('api/cetakInvoice') ?>?id=" + invId, "_blank");
    });
  });
</script>

<?= $this->endSection() ?>