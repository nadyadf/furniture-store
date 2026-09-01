<div class="row mx-0 mb-4">
    <div class="col-md-3 mb-2 mb-md-0">
        <div class="mb-1 fw-bold">Pilihan Varian</div>
        <div class="fs-7 text-muted">tambahkan pilihan varian produk sesuai kebutuhan, maksimal 10 varian per produk</div>
    </div>
    <div class="col-md-9">
        <?php if (count($produkvariasi) > 0) : ?>
            <?php foreach ($produkvariasi as $val) : ?>
                <div class="var-item d-inline-block me-2 mb-2">
                    <div class="var-wrap d-flex align-items-center border rounded px-2 py-1">
                        <div class="name me-2"><?= $val->nama_warna ?></div>
                        <button type="button" class="btn-close btn-close-sm ms-auto" onclick="hapusVarian(<?= $val->idwarna ?>)" aria-label="Close"></button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (count($produkvariasi) <= 40) : ?>
            <div class="var-item d-inline-block mb-2">
                <div class="var-wrap">
                    <button type="button" onclick="tambahVarian()" class="btn btn-outline-primary btn-sm var-btn">
                        <i class="fas fa-plus me-1"></i> tambah
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="table-responsive px-3">
    <form id="variansimpan">
        <?= csrf_field() ?>
        <table class="table table-sm table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Varian</th>
                    <th class="text-center" style="width: 30%;">Harga</th>
                    <th class="text-center" style="width: 10%;">Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produkvariasi as $r) : ?>
                    <tr>
                        
                        <td><?= $r->nama_warna ?></td>
                        
                        <td>
                            <input type="text" name="harga[<?= $r->id ?>]" class="form-control form-control-sm" value="<?= $r->harga ?>" />
                        </td>
                        <td>
                            <input type="text" name="stok[<?= $r->id ?>]" class="form-control form-control-sm" value="<?= $r->stok ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>

<script type="text/javascript">
  // Deklarasikan fungsi delay (Debounce)
  
    $(function(){
        var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })();

        // BENAR: Pembungkus anonymous function
        $("#variansimpan .form-control").on("keyup", function(){
            delay(function(){
                simpanVariasi();
            }, 1500);
        });

        // Submit form varian simpan
        $("#variansimpan").on("submit", function(e){
            e.preventDefault();
            simpanVariasi();
        });

        // Submit form modal harga
        $("#simpanharga").on("submit", function(e){
            e.preventDefault();
            var modalEl = document.getElementById('modalharga');
            if (modalEl) {
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            }
            simpanVariasi();
        });

        // Submit form tambah varian
        $("#simpanvarian").on("submit", function(e){
            e.preventDefault();
            hideAllModals();

            var datar = $(this).serialize();
            datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();

            setTimeout(function(){
                $.post("<?= site_url("admin/api/varianadd") ?>", datar, function(response){
                    var data = (typeof response === 'object') ? response : JSON.parse(response);
                    updateToken(data.token);

                    if(data.success === true){
                        loadVariasi();
                    } else {
                        Swal.fire({
                            title: "Gagal menyimpan",
                            text: data.msg || "Gagal menyimpan data varian, silahkan refresh halaman ini lalu edit kembali datanya",
                            icon: "error"
                        });
                    }
                });
            }, 500);
        });
    });

    // Helper untuk menutup semua modal Bootstrap 5
    function hideAllModals(){
        document.querySelectorAll('.modal.show').forEach(function(modalEl){
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    }

    function tambahVarian(){
        $("#simpanvarian .form-control").val("");
        var modal = new bootstrap.Modal(document.getElementById('modalvarian'));
        modal.show();
    }

    function simpanVariasi(){
        var datar = $("#variansimpan").serialize();
        datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();

        $.ajax({
            type: "POST",
            url: "<?= site_url("admin/api/variansave/" . $id) ?>",
            data: datar,
            statusCode: {
                403: function() {
                    resetToken();
                    setTimeout(() => {
                        simpanVariasi();
                    }, 1000);
                }          
            }
        })
        .done(function(response){
            var data = (typeof response === 'object') ? response : JSON.parse(response);
            updateToken(data.token);

            if(data.success === true){
                if(typeof updateStok === 'function') updateStok(data.stok);
            } else {
                Swal.fire({
                    title: "Gagal menyimpan",
                    text: data.msg || "Gagal menyimpan data varian, silahkan refresh halaman ini lalu edit kembali datanya",
                    icon: "error"
                });
            }
        });
    }

    function hapusVarian(id){
        Swal.fire({
            title: "Yakin menghapus varian ini?",
            text: "Data yang sudah dihapus tidak dapat dikembalikan",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal"
        }).then((result) => {
            if(result.isConfirmed){
                var postData = {
                    "id": id,
                    "produk": <?= $id ?>
                };
                postData[$("#names").val()] = $("#tokens").val();

                $.post("<?= site_url("admin/api/varianhapus") ?>", postData, function(response){
                    var data = (typeof response === 'object') ? response : JSON.parse(response);
                    updateToken(data.token);

                    if(data.success === true){
                        loadVariasi();
                    } else {
                        Swal.fire({
                            title: "Gagal menyimpan",
                            text: data.msg || "Gagal menghapus data varian",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }

</script>

<!-- Modal Varian -->
<div class="modal fade" id="modalvarian" tabindex="-1" aria-labelledby="modalvarianLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalvarianLabel"><i class="fas fa-plus me-1"></i> Tambah Varian</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="simpanvarian" method="POST" action="javascript:void(0);">
                <?= csrf_field() ?>
                <input type="hidden" name="produk" value="<?= $id ?>" />
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Varian</label>
                        <input type="text" class="form-control" name="nama" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>



