<div class="mb-4">
  <div class="mb-3">
    <div class="fw-bold text-dark">Pembeli</div>
    <div class="text-primary mt-1">
      <?= ($user->nama ?? '-') . "<br/>" . ($user->no_hp ?? '') ?>
    </div>
  </div>

  <div class="mb-3">
    <div class="fw-bold text-dark">Tanggal Pesanan</div>
    <div class="text-primary mt-1"><?= $tgl_transaksi ?></div>
    <?= $cod_html ?>
  </div>

  <div class="mb-3">
    <div class="fw-bold text-dark">Informasi Penerima</div>
    <div class="w-100">
      <div class="text-primary mt-1">
        <?= ucwords(($alamat->nama ?? '') . " (" . ($alamat->no_hp ?? '') . ")<br/>" . ($alamat->alamat ?? '')) ?>
      </div>
    </div>
  </div>

  <div class="mb-3">
    <div class="fw-bold text-dark">Kurir Pengiriman</div>
    <div class="w-100">
      <div class="text-primary mt-1">
        <?= $kurir ?>
      </div>
    </div>
  </div>

  <?php if (!empty($transaksi->resi)): ?>
    <div class="mb-3">
      <div class="fw-bold text-dark">Resi Pengiriman / Kurir Pengirim</div>
      <div class="w-100">
        <div class="text-primary mt-1">
          <?= $transaksi->resi ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="mb-3">
    <div class="fw-bold text-dark">Gudang / Asal Pengiriman</div>
    <div class="w-100">
      <div class="text-primary mt-1">
        <i class="fas fa-shipping-fast text-primary me-1"></i> <?= $nama_gudang ?>
      </div>
    </div>
  </div>
</div>

<div class="fw-bold text-dark mb-2">PRODUK PESANAN</div>

<?php foreach ($produk_list as $r): ?>
  <div class="border rounded-3 p-3 mb-2 bg-white">
    <div class="row align-items-center g-3">
      <div class="col-4 col-sm-3 col-md-2">
        <img class="img-fluid rounded border" src="<?= $r['gambar'] ?>" alt="<?= $r['nama_produk'] ?>" />
      </div>
      <div class="col-8 col-sm-9 col-md-10">
        <div class="row align-items-center">
          <div class="col-12 col-md-7 mb-2 mb-md-0">
            <div class="fw-bold text-dark"><?= $r['nama_produk'] ?></div>
            <?php if (!empty($r['variasi'])): ?>
              <small class="text-muted d-block"><?= $r['variasi'] ?></small>
            <?php endif; ?>
            <?php if (!empty($r['keterangan'] ?? '')): ?>
              <div class="text-primary small mt-1"><?= $r['keterangan'] ?></div>
            <?php endif; ?>
          </div>
          <div class="col-12 col-md-5 text-md-end fw-semibold">
            <?= $r['jumlah'] ?> x Rp <?= number_format($r['harga'], 0, ',', '.') ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>