<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<!-- Header & Action Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="page-title mb-0 fw-bold">Promo Slider</h4>
  <a class="btn btn-primary shadow-sm" href="<?= site_url("admin/sliderform") ?>">
    <i class="fas fa-plus-circle me-1"></i> Tambah Promo
  </a>
</div>

<!-- Table Card Container -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th scope="col" class="text-center" style="width: 140px;">Gambar</th>
            <th scope="col">Judul</th>
            <th scope="col">Sub Judul</th>
            <th scope="col">Status</th>
            <th scope="col">Masa Promo</th>
            <th scope="col" class="text-center" style="width: 180px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data['data'])): ?>
            <?php foreach ($data['data'] as $r): ?>
              <tr>
                <!-- Gambar Promo -->
                <td class="text-center">
                  <img 
                    src="<?= base_url("cdn/promo/" . $r->gambar) ?>" 
                    class="img-thumbnail rounded" 
                    style="max-height: 70px; max-width: 120px; object-fit: cover;" 
                    alt="Promo"
                  />
                </td>

                <!-- Caption / Judul -->
                <td class="fw-semibold text-uppercase">
                  <?= esc($r->judul) ?>
                </td>

                <td class="">
                  <?= esc($r->sub_judul) ?>
                </td>

                <!-- Badge Status (dari getPromoData) -->
                <td>
                  <?= $r->status_badge ?>
                </td>

                <!-- Masa Promo -->
                <td>
                  <small class="d-block text-muted">
                    <strong>Mulai:</strong> <?=$r->tgl?>
                  </small>
                  <small class="d-block text-muted">
                    <strong>Selesai:</strong> <?= $r->tgl_selesai ?>
                  </small>
                </td>

                <!-- Tombol Aksi -->
               <td class="text-center">
                  <div class="d-inline-flex gap-2">
                    <a href="<?= site_url("admin/sliderform/" . $r->id); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                      <i class="fas fa-pencil-alt me-1"></i> Edit
                    </a>
                    <a href="javascript:void(0)" onclick="hapusPromo(<?= $r->id; ?>)" class="btn btn-sm btn-outline-danger" title="Hapus">
                      <i class="fas fa-trash-alt me-1"></i> Hapus
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Empty State -->
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <i class="fas fa-info-circle me-1"></i> Belum ada data promo slider.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Footer & Pagination CI4 -->
  <?php if (isset($pager)): ?>
    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
      <?= $pager ?>
    </div>
  <?php endif; ?>
</div>

<script type="text/javascript">
	
	function hapusPromo(pro) {
    Swal.fire({
        title: "Anda yakin menghapus?",
        text: "Promo yang sudah dihapus tidak dapat dikembalikan",
        icon: "warning", // SweetAlert2 menggunakan 'icon', bukan 'type'
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: "Batal",
        confirmButtonText: "Tetap Hapus"
    }).then((result) => {
        if (result.isConfirmed) { // SweetAlert2 menggunakan 'isConfirmed', bukan 'result.value'
            
            // Siapkan payload data & CSRF token CI4
            var postData = { pro: pro };
            postData[$("#names").val()] = $("#tokens").val();

            $.post("<?= site_url('admin/hapus_slider') ?>", postData, function(data) {
                // Update CSRF Token
                if (data.token) {
                    updateToken(data.token);
                }

                if (data.success === true) {
                    Swal.fire("Berhasil!", "Berhasil menghapus data", "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Gagal!", data.msg || "Gagal menghapus data, terjadi kesalahan sistem", "error");
                }
            }, "json"); // Set dataType 'json' agar jQuery mengurai JSON secara otomatis (tanpa eval)
        }
    });
  }
	
	function refreshTabel(page){
		window.location.href = "<?php echo site_url('admin/slider/?page='); ?>"+page;
	}
</script>


<!-- Alert Sukses -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>