<?php $isFotoCopy = session()->get('fotoCopy'); ?>

<div class="d-flex flex-wrap gap-2">
  <?php if (!empty($foto)): ?>
    <?php foreach ($foto as $f): ?>
      <div class="card shadow-sm border p-1" style="width: 120px;">
        <!-- Thumbnail Foto -->
        <div class="ratio ratio-1x1 mb-2 rounded overflow-hidden bg-light">
          <img src="<?= base_url('cdn/uploads/' . $f->nama) ?>" class="object-fit-cover" alt="Foto Produk" />
        </div>

        <!-- Tombol Aksi -->
        <div class="d-grid gap-1">
          <?php if ($f->jenis == 1): ?>
            <button type="button" class="btn btn-success btn-sm py-0 disabled" style="font-size: 10px;">
              <i class="la la-check-circle"></i> Foto Utama
            </button>
          <?php else: ?>
            <?php if (!$isFotoCopy): ?>
              <div class="btn-group btn-group-sm w-100" role="group">
                <button type="button" class="btn btn-outline-primary py-0" style="font-size: 10px;" onclick="jadikanUtama(<?= $f->id ?>)">
                  Utama
                </button>
                <button type="button" class="btn btn-outline-danger py-0" style="font-size: 10px;" onclick="hapusFoto(<?= $f->id ?>)">
                  Hapus
                </button>
              </div>
            <?php else: ?>
              <button type="button" class="btn btn-danger btn-sm py-0 w-100" style="font-size: 10px;" onclick="hapusFoto(<?= $f->id ?>)">
                Hapus
              </button>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>