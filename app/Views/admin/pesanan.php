<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<h4 class="page-title mb-4">Pesanan</h4>

<div class="mb-5">
  <div class="card shadow-sm">
    <div class="card-header row align-items-center g-3">
      <!-- Tabs Pesanan -->
      <div class="col-md-8">
        <ul class="nav nav-tabs border-0" id="pesananTab">
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
    $("#pesananTab .nav-link").on('click', function(e){
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
      window.open("<?= site_url('admin/api/cetakInvoice') ?>?id=" + invId, "_blank");
    });
  });

  function loadingDulu() {
    $("#load").html(`
      <div class="d-flex align-items-center justify-content-center gap-2 py-3">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <span class="text-secondary fw-semibold">Memproses data...</span>
      </div>
    `);
  }

  function detail(id) {
    // 1. Inisialisasi dan Tampilkan Modal Bootstrap 5
    var modalEl = document.getElementById('modaldetail');
    var modalObj = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalObj.show();

    // 2. Reset tampilan loader
    $("#detailoader").show();
    $("#detaiload").hide();

    // 3. Load Konten HTML via AJAX
    var urlDetail = "<?= site_url('admin/api/detailpesanan') ?>?theid=" + encodeURIComponent(id);

    $("#modaldetail .modal-body").load(urlDetail, function(response, status, xhr) {
      if (status === "error") {
        $("#modaldetail .modal-body").html(
          '<div class="alert alert-danger m-3">Gagal memuat detail pesanan: ' + xhr.statusText + '</div>'
        );
      } else {
        $("#inv").val(id);
      }
      
      // Sembunyikan loader dan tampilkan konten
      $("#detailoader").hide();
      $("#detaiload").show();
    });
  }

  function cetak(inv) {
    if (!inv) return;
    
    // Menggunakan encodeURIComponent untuk keamanan karakter khusus pada parameter URL
    const url = "<?= site_url('admin/api/cetakInvoice') ?>?id=" + encodeURIComponent(inv);
    
    // Membuka di tab baru secara eksplisit ('_blank')
    window.open(url, '_blank');
  }

  function selesai(id) {
    if (!id) return;

    Swal.fire({
      title: "Yakin pesanan sudah selesai?",
      text: "Status pesanan akan diupdate ke selesai",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#198754",
      cancelButtonColor: "#ff646d",
      confirmButtonText: "Ya, Selesai!",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        // Payload data POST + CSRF CI4
        var postData = { 
          "id": id,
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
        };

        $.post("<?= site_url('admin/api/terimapesanan'); ?>", postData, function(data) {
          if (typeof updateToken === 'function' && data.token) {
            updateToken(data.token);
          }

          if (data.success === true) {
            Swal.fire({
              title: "Berhasil!",
              text: "Data pesanan telah diperbarui",
              icon: "success"
            }).then(() => {
              // Ambil tab aktif saat ini lalu reload datanya
              var activeStatus = $("#pesananTab .nav-link.active").data("item") || "dikirim";
              loadPesanan(activeStatus, 1);
            });
          } else {
            Swal.fire({
              title: "Gagal!",
              text: data.msg || "Gagal mengupdate data, coba ulangi beberapa saat lagi",
              icon: "error"
            });
          }
        }, "json").fail(function() {
          Swal.fire({
            title: "Error!",
            text: "Gagal terhubung ke server. Silakan coba lagi.",
            icon: "error"
          });
        });
      }
    });
  }

  function batalkan(id) {
    if (!id) return;

    Swal.fire({
      title: "Yakin membatalkan pesanan ini?",
      text: "Pesanan yang sudah dibatalkan tidak dapat dikembalikan lagi",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, Batalkan!",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        // Susun payload POST + Token CSRF CI4
        var postData = {
          "id": id,
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
        };

        $.post("<?= site_url('admin/api/batalkanpesanan'); ?>", postData, function(data) {
          // Refresh token CSRF di frontend
          if (typeof updateToken === 'function' && data.token) {
            updateToken(data.token);
          }

          if (data.success === true) {
            Swal.fire({
              title: "Berhasil!",
              text: "Pesanan telah berhasil dibatalkan",
              icon: "success"
            }).then(() => {
              // Ambil tab status yang sedang aktif saat ini lalu reload datanya
              var activeStatus = $("#pesananTab .nav-link.active").data("item") || "batal";
              loadPesanan(activeStatus, 1);
            });
          } else {
            Swal.fire({
              title: "Gagal!",
              text: data.msg || data.message || "Gagal membatalkan pesanan, coba ulangi beberapa saat lagi",
              icon: "error"
            });
          }
        }, "json").fail(function() {
          Swal.fire({
            title: "Error!",
            text: "Gagal terhubung ke server. Silakan coba lagi.",
            icon: "error"
          });
        });
      }
    });
  }
</script>

<div class="modal fade" id="modaldetail" tabindex="-1" aria-labelledby="modaldetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title id="modaldetailLabel">
          <i class="fas fa-boxes me-2"></i>Detail Pesanan
        </h6>
        <!-- Tombol Close Bootstrap 5 -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="detaiload"></div>
        
        <!-- Indikator Loader (Pilihan Spinner BS5 atau FontAwesome) -->
        <div id="detailoader" class="py-3 text-center text-muted">
          <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <span>Memuat, tunggu sebentar...</span>
        </div>
      </div>

      <div class="modal-footer">
        <form id="cetakInvoice">
          <input type="hidden" id="inv" name="inv" />
          <button class="btn btn-sm btn-secondary" type="submit">
            <i class="fas fa-print me-1"></i> Cetak Invoice
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>