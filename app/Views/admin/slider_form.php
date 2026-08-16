<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<form id="saveform" method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$id ?>" />
    
    <!-- CSRF Token bawaan CodeIgniter 4 -->
    <?= csrf_field() ?>

    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="page-title mb-0"><?= ($id == 0) ? 'Tambah Promo Slider' : 'Edit Promo Slider' ?></h4>
        </div>
        <div class="col-md-6 text-end">
            <a class="btn btn-danger" href="javascript:history.back()">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Detail Promo -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Promo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" value="<?= $r->judul ?? '' ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sub Judul</label>
                        <input type="text" name="sub_judul" class="form-control" value="<?= $r->sub_judul ?? '' ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link Promo</label>
                        <input type="text" name="link" class="form-control" value="<?= $r->url ?? '' ?>" />
                    </div>

                    <!-- Input Tanggal Native HTML5 (Solusi 1) -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal Tayang Promo</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="datetime-local" name="tgl" class="form-control" value="<?= !empty($r->tgl) ? date('Y-m-d\TH:i', strtotime($r->tgl)) : '' ?>" />
                            </div>
                            <div class="col-md-6">
                                <input type="datetime-local" name="tgl_selesai" class="form-control" value="<?= !empty($r->tgl_selesai) ? date('Y-m-d\TH:i', strtotime($r->tgl_selesai)) : '' ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Promo</label>
                        <select class="form-select" name="status" required>
                            <option value="1" <?= (isset($r->status) && $r->status == 1) ? 'selected' : '' ?>>AKTIF</option>
                            <option value="0" <?= (isset($r->status) && $r->status == 0) ? 'selected' : '' ?>>NON AKTIF</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Foto Display -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Foto Display</h5>
                </div>
                <div class="card-body text-center">
                    <!-- Hidden Input File -->
                    <input type="file" accept="image/*" name="gambar" id="imgInp" class="d-none" />

                    <!-- Tombol Pilih Foto -->
                    <a href="javascript:void(0)" class="btn btn-secondary mb-3" onclick="selectIMG()">
                        <i class="fas fa-image me-1"></i> <?= !empty($r->gambar) ? 'Ganti Foto' : 'Pilih Foto' ?>
                    </a>

                    <div class="border-top pt-3"></div>

                    <!-- Area Preview Gambar -->
                    <div class="imgInpPreview p-3 border rounded bg-light">
                        <?php if (!empty($r->gambar)) : ?>
                            <!-- Gambar Lama dari DB -->
                            <img id="blah" class="img-fluid rounded my-2" src="<?= base_url('cdn/promo/' . $r->gambar) ?>" alt="preview" style="max-height: 200px;" />
                            <div class="text text-muted d-none">Pilih foto</div>
                        <?php else : ?>
                            <!-- Tempat Preview Gambar Baru -->
                            <div class="text text-muted">Pilih foto</div>
                            <img id="blah" class="img-fluid rounded my-2" src="#" alt="preview" style="display:none; max-height: 200px;" />
                        <?php endif; ?>

                        <div class="delete mt-2" style="<?= !empty($r->gambar) ? '' : 'display:none;' ?>">
                            <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger" onclick="clearIMG()">
                                <i class="fas fa-times me-1"></i> Batal / Hapus Foto
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="mb-4">
        <button type="submit" class="btn btn-primary submit me-2">
            <i class="fas fa-check-circle me-1"></i> Simpan Promo
        </button>
        <button type="reset" class="btn btn-warning">
            <i class="fas fa-redo me-1"></i> Reset
        </button>
    </div>
</form>

<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#blah').attr('src', e.target.result).fadeIn(200);
                $('.delete').fadeIn(200);
                $('.text').hide();
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function selectIMG() {
        $("#imgInp").trigger("click");
    }

    function clearIMG() {
        $("#imgInp").val("");
        $('#blah').attr('src', '#').hide();
        $('.delete').hide();
        $('.text').fadeIn(200);
    }

    $(function() {
        // Handling Form Submit
        $("#saveform").on("submit", function() {
            $(".submit").html("<i class='fas fa-spinner fa-spin me-1'></i> Menyimpan...");
            $(".submit").prop("disabled", true);
        });
        
        // Event Listener Gambar
        $(document).on("change", "#imgInp", function() {
            if (this.files && this.files[0]) {
                readURL(this);
            } else {
                clearIMG();
            }
        });
    });
</script>

<!-- Alert Error Ekstensi / Ukuran File -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>