<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0">Daftar Produk</h4>
  <div class="d-flex gap-2">
    <a href="<?= site_url("admin/produkform") ?>" class="btn btn-success">
      <i class="fas fa-plus-circle me-1"></i> Produk Baru
    </a>
    <a href="javascript:void(0)" onclick="importProduk()" class="btn btn-primary">
      <i class="fas fa-download me-1"></i> Impor Excel
    </a>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-white py-3">
    <div class="row g-2 align-items-center">
      <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Cari produk..." id="cari" />
      </div>
      <div class="col-md-3">
        <button class="btn w-100 d-flex justify-content-between align-items-center" style="background-color: rgba(251, 172, 76, 0.25);">
          <span>Stok Habis</span>
          <span class="badge bg-danger rounded-pill"><?=$jmlProdukHabis?></span>
        </button>
      </div>
      <div class="col-md-3 col-8">
        <select id="status" class="form-select">
          <option value="0">Semua Produk</option>
          <option value="1">Stok Tersedia</option>
          <option value="3">Stok Menipis</option>
          <option value="2">Stok Habis</option>
        </select>
      </div>
      <div class="col-md-2 col-4">
        <select id="perpage" class="form-select">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="75">75</option>
          <option value="100">100</option>
        </select>
      </div>
    </div>
  </div>
  <div class="card-body p-4" id="load">
    <div class="text-center py-4 text-muted">
      <i class="fas fa-spin fa-spinner fa-2x mb-2"></i>
      <p class="mb-0">Loading data...</p>
    </div>
  </div>
</div>


<script type="text/javascript">
  // Variabel CSRF Token CI4
  var csrfName = '<?= csrf_token() ?>';
  var csrfHash = '<?= csrf_hash() ?>';

  function updateToken(newToken) {
    if (newToken) {
      csrfHash = newToken;
    }
  }

  $(function() {
    refreshTabel(1);

    // Form Import Excel
    $("#impor").on("submit", function(e) {
      e.preventDefault();

      var formData = new FormData();
      $(".progress").removeClass("d-none").show();
      $(this).hide();

      formData.append("fileupload", $("#file").get(0).files[0]);
      formData.append(csrfName, csrfHash);

      $.ajax({
        url: '<?= site_url("api/import"); ?>',
        type: 'POST',
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        data: formData,
        xhr: function() {
          var jqXHR = $.ajaxSettings.xhr();
          if (jqXHR.upload) {
            jqXHR.upload.addEventListener("progress", function(evt) {
              if (evt.lengthComputable) {
                var percentComplete = Math.round((evt.loaded * 100) / evt.total);
                $(".progress .progress-bar")
                  .css("width", percentComplete + "%")
                  .attr("aria-valuenow", percentComplete);
              }
            }, false);
          }
          return jqXHR;
        },
        success: function(res) {
          $("#impor").show();
          $(".progress").addClass("d-none").hide();
          updateToken(res.token);

          if (res.success === true) {
            var modalEl = document.getElementById('modalimpor');
            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire("Berhasil", "Data produk telah berhasil diimpor", "success").then(() => {
              refreshTabel(1);
            });
          } else {
            Swal.fire("Gagal Impor", "Terjadi kesalahan saat server memproses file:<br/><i class='text-danger'>" + res.msg + "</i>", "error");
          }
        },
        error: function() {
          $("#impor").show();
          $(".progress").addClass("d-none").hide();
          Swal.fire("Gagal!", "Terjadi kesalahan server saat unggah file.", "error");
        }
      });
    });

    // Event Filter & Pencarian
    $("#perpage, #status").on("change", function() {
      refreshTabel(1);
    });

    // Trigger pencarian saat tekan Enter atau ketik di input pencarian
    $("#cari").on("keyup change", function(e) {
      if (e.type === "keyup" && e.key !== "Enter") return;
      refreshTabel(1);
    });
  });

  function refreshTabel(page) {
    $("#load").html('<div class="text-center py-4 text-muted"><i class="fas fa-spin fa-spinner fa-2x mb-2"></i><p class="mb-0">Loading data...</p></div>');
    
    var perpage = $("#perpage").val();
    var postData = {
      "cari": $("#cari").val(),
      "status": $("#status").val()
    };
    postData[csrfName] = csrfHash;

    $.post("<?= site_url("admin/produk?load=true") ?>&page=" + page + "&perpage=" + perpage, postData, function(data) {
      updateToken(data.token);
      $("#load").html(data.result);
    }, "json");
  }

  function importProduk() {
    var modalEl = document.getElementById('modalimpor');
    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();
  }

  function hapus(id) {
    Swal.fire({
      title: "Yakin menghapus?",
      text: "Data yang sudah dihapus tidak akan bisa dikembalikan",
      icon: "warning",
      showCancelButton: true,
      cancelButtonText: "Batal",
      confirmButtonText: "Ya, Hapus"
    }).then((val) => {
      if (val.isConfirmed) {
        var postData = { "id": id };
        postData[csrfName] = csrfHash;

        $.post("<?= site_url("admin/api/hapusproduk") ?>", postData, function(data) {
          updateToken(data.token);
          if (data.success === true) {
            Swal.fire("Berhasil", "Data telah dihapus", "success").then(() => {
              window.location.href = "<?= site_url("admin/produk") ?>";
            });
          } else {
            Swal.fire("Gagal!", "Gagal menghapus data, cobalah beberapa saat lagi", "error");
          }
        }, "json");
      }
    });
  }
</script>

<div class="modal fade" id="modalimpor" tabindex="-1" aria-labelledby="modalimporLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalimporLabel">Impor Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-4 text-secondary small">
          Sebelum mengunggah, silakan ikuti format data untuk impor sesuai template yang telah disediakan.<br/>
          <a href="<?= base_url("import/Template_Import.xlsx") ?>" class="btn btn-link p-0 mt-2 text-decoration-none">
            <i class="fas fa-file-download me-1"></i> Download Template Impor
          </a>
        </div>
        
        <form id="impor">
          <div class="mb-3">
            <label for="file" class="form-label small fw-medium">File Excel (.xls / .xlsx / .csv)</label>
            <input type="file" id="file" name="file" class="form-control" required />
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-download me-1"></i> Impor
            </button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i> Batal
            </button>
          </div>
        </form>

        <div class="progress mt-3 d-none" style="height: 18px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>