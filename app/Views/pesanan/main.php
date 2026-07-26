<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="pb-5 first-section">
    <div class="container">
        <h3 class="title-theme fw-bold mb-4 pt-4">Status Pesanan</h3>

        <div class="tab">
            <div class="tab-header d-flex flex-wrap gap-2 mb-4">
                <a class="navlink belumbayar btn btn-light active"
                    href="javascript:void(0)"
                    data-link="#belumbayar">
                    <i class="fas fa-money-check-alt me-1"></i> Belum Bayar
                </a>

                <a class="navlink dikemas btn btn-light"
                    href="javascript:void(0)"
                    data-link="#dikemas">
                    <i class="fas fa-box me-1"></i> Dikemas
                </a>

                <a class="navlink dikirim btn btn-light"
                    href="javascript:void(0)"
                    data-link="#dikirim">
                    <i class="fas fa-shipping-fast me-1"></i> Dikirim
                </a>

                <a class="navlink selesai btn btn-light"
                    href="javascript:void(0)"
                    data-link="#selesai">
                    <i class="fas fa-clipboard-check me-1"></i> Selesai
                </a>

                <a class="navlink batal btn btn-light"
                    href="javascript:void(0)"
                    data-link="#batal">
                    <i class="fas fa-times me-1"></i> Dibatalkan
                </a>
            </div>

            <div class="tab-content">

                <!-- BELUM BAYAR -->
                <div class="tab-pane active" id="belumbayar">
                    <div class="text-center py-4">
                        <h4>
                            <i class="fas fa-compact-disc fa-spin text-success"></i>
                            Loading...
                        </h4>
                    </div>
                </div>

                <!-- DIKEMAS -->
                <div class="tab-pane" id="dikemas"></div>

                <!-- DIKIRIM -->
                <div class="tab-pane" id="dikirim"></div>

                <!-- SELESAI -->
                <div class="tab-pane" id="selesai"></div>

                <!-- BATAL -->
                <div class="tab-pane" id="batal"></div>

                <!-- DIGITAL -->
                <div class="tab-pane" id="digital"></div>

            </div>
        </div>
    </div>
</div>

<script>
    function loadPesanan(status, page = 1) {
        let targetPane = (status === 'dibatalkan') ? '#batal' : '#' + status;
        
        // Tampilkan loading spinner bawaanmu
        $(targetPane).html(`
            <div class="text-center py-4">
                <h4><i class="fas fa-compact-disc fa-spin text-success"></i> Loading...</h4>
            </div>
        `);

        $.ajax({
            url: "<?= site_url('assync/pesanan') ?>", // Arahkan ke method fetch di controller
            type: 'GET',
            data: { status: status, page: page },
            dataType: 'html',
            success: function(response) {
                $(targetPane).html(response);
            },
            error: function() {
                $(targetPane).html('<div class="text-center py-4 mt-3 section"><i class="fas fa-box-open text-danger mb-3" style="font-size: 120px;"></i> <h5 class="text-dark fw-bold">TIDAK ADA PESANAN</h5></div>');
            }
        });
    }

    $(document).ready(function() {
      
        var statusAwal = "<?= $status_aktif ?>"; 
        if (!statusAwal) statusAwal = "belumbayar";

        let targetPaneAwal = (statusAwal === 'dibatalkan' || statusAwal === 'batal') ? '#batal' : '#' + statusAwal;

        $('.navlink').removeClass('active');
        $('.tab-pane').removeClass('active');

        $('.navlink.' + statusAwal).addClass('active');
        $(targetPaneAwal).addClass('active');

        loadPesanan(statusAwal, 1);

        // Event saat tab diklik
        $('.navlink').on('click', function(e) {
            e.preventDefault();

            let target = $(this).data('link'); // Ambil #belumbayar, #dikemas, dll
            let status = target.replace('#', '');
            if (status === 'batal') status = 'dibatalkan';

            // 1. UPDATE URL DI BROWSER TANPA RELOAD HALAMAN
            let currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('status', status);
            window.history.pushState({ status: status }, '', currentUrl.toString());

            // 2. Ganti status Active tombol navlink
            $('.navlink').removeClass('active');
            $(this).addClass('active');

            // 3. Pindahkan class active pada tab-pane
            $('.tab-pane').removeClass('active');
            $(target).addClass('active');

            // 4. Panggil AJAX untuk memuat data pesanan
            loadPesanan(status, 1);
        });

        // BONUS: Event agar tombol Back/Forward browser berfungsi mulus dengan tab
        window.onpopstate = function(event) {
            let urlParams = new URLSearchParams(window.location.search);
            let status = urlParams.get('status') || 'belumbayar';
            
            let targetPane = (status === 'dibatalkan' || status === 'batal') ? '#batal' : '#' + status;

            $('.navlink').removeClass('active');
            $('.navlink.' + status).addClass('active');

            $('.tab-pane').removeClass('active');
            $(targetPane).addClass('active');

            loadPesanan(status, 1);
        };

        // Event saat tombol page angka/next/prev diklik
        // Handle klik tombol pagination bawaan CI4 via AJAX
        $(document).on('click', '.pagination-ajax a', function(e) {
            e.preventDefault(); // Mencegah halaman reload pindah ke URL assync
            
            let urlHref = $(this).attr('href');
            if (!urlHref || urlHref === '#' || urlHref === 'javascript:void(0)') {
                return;
            }

            // Ekstrak query parameter (?status=...&page=...) dari URL href menggunakan URL object
            let urlObj = new URL(urlHref, window.location.origin);
            let page = urlObj.searchParams.get('page') || 1;
            let status = urlObj.searchParams.get('status') || 'belumbayar';

            // Panggil kembali fungsi loadPesanan global yang sudah kamu buat kemarin
            loadPesanan(status, page);
        });

         $("#upload").on("submit",function(e){
            $("#upload button").hide();
            $("#upload").append("<h5 class='text-success'><i class='fas fa-spin fa-compact-disc'></i> Mengunggah...</h5>");
        });
    });

    function resimarketplace(id) {
        $('#idtrx').val(id);
        $('#resimodal').modal('show');
    }

    function konfirmasi(bayar){
        $("#bayar").val(bayar);
        $('#konfirmasimodal').modal('show');  
    }

    function batal(bayar) {
        Swal.fire({
            title: "Anda yakin?",
            text: "Pesanan akan dibatalkan.",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "Oke",
            denyButtonText: "Batal",
            customClass: {
                confirmButton: 'btn btn-danger mx-2',
                denyButton: 'btn btn-secondary mx-2'
            },
            buttonsStyling: false
        })
        .then((willDelete) => {
            if (willDelete.isConfirmed) {
            
                var postData = {
                    "pesanan": bayar
                };
                
                postData['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';

                $.post("<?php echo site_url("assync/batalkanPesanan"); ?>", postData, function(data) {
                    
                    if (data.token) {
                        updateToken(data.token);
                    }

                    if (data.success === true) {
                        Swal.fire("Berhasil!", "Pesanan Anda telah dibatalkan.", "success").then(() => {
                            $('.navlink').removeClass('active');
        
                            $('.navlink[data-link="#batal"]').addClass('active');
                            
                            $('.tab-pane').removeClass('active');
                            $('#batal').addClass('active'); 
                            
                            loadPesanan('dibatalkan', 1);
                        });
                    } else {
                        Swal.fire("Gagal!", "Gagal membatalkan pesanan, coba ulangi beberapa saat lagi.", "error");
                    }
                    
                }, "json"); 
            }
        });
    }
  
</script>

<div class="modal fade" id="konfirmasimodal" tabindex="-1" aria-labelledby="konfirmasimodalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="konfirmasimodalLabel">Konfirmasi Pembayaran</h5>
        <!-- Menggunakan tombol close standar Bootstrap 5 -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mx-0 py-3">
          
          <form id="upload" class="row px-0 mx-0 w-100" method="POST" enctype="multipart/form-data" action="<?php echo site_url("manage/konfirmasi"); ?>">
          
            <?= csrf_field() ?>
            <input name="idbayar" type="hidden" id="bayar" value="0"/>
            
            <!-- Label Upload -->
            <div class="col-md-12 mb-2">
              <label class="form-label fw-medium">
                Upload Bukti Transfer <span class="text-muted small">(jpg, png, pdf)</span>
              </label>
            </div>
            
            <!-- Input File -->
            <div class="col-md-12 mb-4">
              <input type="file" name="bukti" class="form-control" accept="image/*,application/pdf" required />
            </div>
            
            <!-- Tombol Aksi -->
            <div class="row mx-0 px-0">
              <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-chevron-circle-up me-2"></i>Upload
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="resimodal" tabindex="-1" aria-labelledby="resimodalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resimodalLabel">Label Pengiriman / Resi Marketplace</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="py-3">
          <form id="upload" class="w-100" method="POST" enctype="multipart/form-data" action="<?php echo site_url("manage/resimarketplace"); ?>">
            <input name="id" type="hidden" id="idtrx" value="0"/>
            <?= csrf_field() ?>
            
            <div class="mb-3">
              <label class="form-label fw-medium">Nomor Resi</label>
              <input type="text" name="resi" class="form-control" required>
            </div>
            
            <div class="mb-4">
              <label class="form-label fw-medium">Label / Resi Marketplace (jpg, png, pdf)</label>
              <input type="file" name="resi" class="form-control" accept="application/pdf,image/*" required>
            </div>
            
            <div class="row pt-2">
              <div class="col-md-6 d-grid">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-chevron-circle-up me-2"></i>Upload
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>