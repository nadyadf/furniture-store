<table class="table align-middle">
  <thead>
    <tr>
      <th style="width: 80px;">Foto</th>
      <th>Nama Produk</th>
      <th>Detail Harga</th>
      <th class="text-center" style="width: 140px;">Stok Produk</th>
      <th class="text-center" style="width: 160px;">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($produkByLimit)): ?>
      <tr>
        <td class="text-center text-danger py-4" colspan="5">Belum ada produk.</td>
      </tr>
    <?php else: ?>
      <?php 
        $default = base_url("assets/img/no-image.png");
        $no      = 1 + $offset;
      ?>
      <?php foreach ($produkByLimit as $p): ?>
        <?php 
          // Validasi Gambar
          if (!empty($p->gambar)) {
              $imgSrc = filter_var($p->gambar, FILTER_VALIDATE_URL) ? $p->gambar : base_url('cdn/uploads/' . $p->gambar);
          } else {
              $imgSrc = $default;
          }

          // Format Tampilan Stok & Variasi
          $stl  = ($p->stok > 2) ? " class='text-primary'" : " class='text-danger'";
          $stok = ($p->jml_variasi > 0) 
                  ? "<b{$stl}>{$p->stok}</b><br/><small><i>dari <b>{$p->jml_variasi}</b> varian</i></small>" 
                  : "<b{$stl}>{$p->stok}</b>";
        ?>
        <tr>
          <!-- Foto Produk -->
          <td style="width: 140px;" class="py-3">
            <div class="bg-white p-2 rounded-4 shadow-sm border text-center d-flex align-items-center justify-content-center" style="width: 115px; height: 115px;">
              <img src="<?= $imgSrc ?>" class="img-fluid rounded-3" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="<?= esc($p->nama) ?>">
            </div>
          </td>

          <!-- Nama Produk & Gudang -->
          <td>
            <div class="fw-bold mb-1"><?= esc(ucwords($p->nama)) ?></div>
            <?= $p->gudang_html ?>
          </td>

          <!-- Detail Harga -->
          <td>
            Normal: IDR <?= number_format($p->harga, 0, ',', '.') ?>
          </td>

          <!-- Stok -->
          <td class="text-center">
            <?= $stok ?>
          </td>

          <!-- Tombol Aksi -->
          <td class="text-center">
            <a href="<?= site_url('ngadimin/produkform/?copy=' . $p->id) ?>" title="copy" class="btn btn-warning btn-sm">
              <i class="fas fa-copy"></i>
            </a>
            <a href="<?= site_url('admin/produkform/' . $p->id) ?>" title="edit" class="btn btn-primary btn-sm">
              <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="javascript:void(0)" onclick="hapus(<?= $p->id ?>)" title="hapus" class="btn btn-danger btn-sm">
              <i class="fas fa-trash-alt"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<!-- Navigasi Pagination Mandiri (Tanpa Helper/Function) -->
<?php if (isset($totalRows) && $totalRows > $perpage): ?>
  <?php 
    $totalPages = ceil($totalRows / $perpage); 
  ?>
  <div class="d-flex justify-content-between align-items-center pt-3 border-top">
    <!-- Informasi Jumlah Data -->
    <div class="small text-muted">
      Menampilkan <?= $offset + 1 ?> - <?= min($offset + $perpage, $totalRows) ?> dari <?= $totalRows ?> data
    </div>

    <!-- Tombol Angka Halaman -->
    <ul class="pagination pagination-sm mb-0">
      <!-- Tombol Prev -->
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="javascript:void(0)" onclick="refreshTabel(<?= $page - 1 ?>)">
          &laquo;
        </a>
      </li>

      <!-- Loop Nomor Halaman -->
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="javascript:void(0)" onclick="refreshTabel(<?= $i ?>)"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <!-- Tombol Next -->
      <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
        <a class="page-link" href="javascript:void(0)" onclick="refreshTabel(<?= $page + 1 ?>)">
          &raquo;
        </a>
      </li>
    </ul>
  </div>
<?php endif; ?>