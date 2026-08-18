<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<h4 class="fw-bold mb-4">Riwayat Transaksi Penjualan</h4>

<div class="mb-5">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom">
      <div class="row g-3 align-items-center">
        <!-- Title & Filter Icon -->
        <div class="col-md-6 d-flex align-items-center gap-2 fs-5 fw-semibold text-secondary">
          <i class="fas fa-filter text-primary"></i> Periode Laporan
        </div>
        
        <!-- Action Buttons -->
        <div class="col-md-6 text-md-end">
          <button onclick="printDiv('load','Laporan Penjualan')" class="btn btn-primary">
            <i class="fas fa-print me-1"></i> Cetak
          </button>
        </div>

        <div class="col-md-2">
          <label for="tglmulai" class="form-label small fw-medium mb-1">Tanggal Mulai</label>
          <input type="text" id="tglmulai" name="tglmulai" class="form-control datepicker" value="<?= date("Y-m-d", strtotime("-30 day")) ?>" />
        </div>

        <div class="col-md-2">
          <label for="tglselesai" class="form-label small fw-medium mb-1">Tanggal Selesai</label>
          <input type="text" id="tglselesai" name="tglselesai" class="form-control datepicker" value="<?= date("Y-m-d") ?>" />
        </div>

        <div class="col-md-4">
          <label for="gudang" class="form-label small fw-medium mb-1">Gudang</label>
          <select id="gudang" onChange="loadRiwayat()" class="form-select">
            <option value="semua">Semua</option>
            <option value="0">PUSAT</option>
            <?php
              foreach ($gudang_list as $g) {
                echo '<option value="' . esc($g->id) . '">' . esc($g->nama) . ' - ' . esc($g->namakota) . '</option>';
              }
            ?>
          </select>
        </div>

        <div class="col-md-4">
          <label for="status" class="form-label small fw-medium mb-1">Status Transaksi</label>
          <select id="status" onChange="loadRiwayat()" class="form-select">
            <option value="0">Semua Transaksi</option>
            <option value="1">Semua Transaksi Yg Sudah Bayar</option>
            <option value="2">Belum Dibayar</option>
            <option value="3">Perlu Dikirim</option>
            <option value="4">Sedang Dikirim</option>
            <option value="5">Selesai</option>
            <option value="6">Dibatalkan</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Container Load Data -->
    <div class="card-body p-4" id="load">
      <div class="text-center py-4 text-muted">
        <i class="fas fa-spin fa-spinner fa-2x mb-2"></i>
        <p class="mb-0">Loading data...</p>
      </div>
    </div>
  </div>
</div>

<div id="editor"></div>

<!-- JS PDF CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- CDN Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script type="text/javascript">
  var csrfName = '<?= csrf_token() ?>';
  var csrfHash = '<?= csrf_hash() ?>';

  function updateToken(newToken) {
    if (newToken) {
      csrfHash = newToken;
    }
  }

  $(function() {
    loadRiwayat();

    // Inisialisasi Flatpickr
    flatpickr(".datepicker", {
      dateFormat: "Y-m-d",    // Nilai tersembunyi yang dikirim ke database (YYYY-MM-DD)
      altInput: true,         // Membuat input tampilan visual terpisah
      altFormat: "d/m/Y",     // Format yang terlihat di layar (DD/MM/YYYY)
      allowInput: true,
      onChange: function(selectedDates, dateStr, instance) {
        loadRiwayat();        // Otomatis reload data saat tanggal diubah
      }
    });

    $(".tabs-item").on('click', function() {
      $(".tabs-item.active").removeClass("active");
      $(this).addClass("active");
    });

    $("#rekeningform").on("submit", function(e) {
      e.preventDefault();
      Swal.fire({
        text: "Pastikan lagi data yang Anda masukkan sudah sesuai",
        title: "Validasi data",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Simpan",
        cancelButtonText: "Cek Lagi"
      }).then((vals) => {
        if (vals.isConfirmed) {
          var datar = $(this).serializeArray();
          datar.push({ name: csrfName, value: csrfHash });

          $.post("<?= site_url("api/update") ?>", datar, function(data) {
            updateToken(data.token);
            if (data.success === true) {
              loadHalaman(1);
              $("#modal").modal("hide");
              Swal.fire("Berhasil", "Data halaman sudah disimpan", "success");
            } else {
              Swal.fire("Gagal!", "Gagal menyimpan data, coba ulangi beberapa saat lagi", "error");
            }
          }, "json");
        }
      });
    });
  });

  function loadRiwayat() {
    $("#load").html('<div class="text-center py-4 text-muted"><i class="fas fa-spin fa-spinner fa-2x mb-2"></i><p class="mb-0">Loading data...</p></div>');

    var postData = {
      "gudang": $("#gudang").val(),
      "status": $("#status").val(),
      "tglmulai": $("#tglmulai").val(),     // Akan mengambil nilai YYYY-MM-DD
      "tglselesai": $("#tglselesai").val()  // Akan mengambil nilai YYYY-MM-DD
    };
    postData[csrfName] = csrfHash;

    $.post("<?= site_url("admin/laporantransaksi?load=hal") ?>", postData, function(data) {
      updateToken(data.token);
      $("#load").html(data.result);
    }, "json");
  }

  function printDiv(divId, title) {
    let mywindow = window.open('', 'PRINT', 'height=650,width=900,top=100,left=150');

    mywindow.document.write(`<html><head><title>${title}</title>`);
    mywindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">');
    mywindow.document.write('<link rel="stylesheet" href="<?= base_url() ?>/assets/css/util.css">');
    mywindow.document.write('<link rel="stylesheet" href="<?= base_url() ?>/assets/css/minmin.css?v=<?= time() ?>">');

    // CSS Tambahan untuk Perbaikan Tampilan Cetak/PDF
    mywindow.document.write(`
        <style>
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .table {
                width: 100% !important;
                font-size: 10px !important;
                table-layout: auto !important;
            }
            .table th, .table td {
                padding: 4px 5px !important;
                word-break: break-word !important;
                white-space: normal !important;
            }
        </style>
    `);

    mywindow.document.write('</head><body>');
    mywindow.document.write($("#" + divId).html());
    mywindow.document.write('</body></html>');

    mywindow.document.close();
    mywindow.focus();

    setTimeout(function() {
      mywindow.print();
      setTimeout(function() {
        mywindow.close();
      }, 1000);
    }, 1000);

    return true;
}
</script>


<?= $this->endSection() ?>