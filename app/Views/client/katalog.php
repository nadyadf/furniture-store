<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<section class="pt-5 pb-5 first-section">
  <div class="container">

    <!-- PENCARIAN -->
    <?php if(!empty($cari)): ?>
      <button class="btn btn-outline-secondary bg-white text-start mb-3" disabled>
        Hasil Pencarian: "<b class="text-danger"><?= esc($cari) ?></b>"
      </button>
    <?php endif; ?>

    <!-- FILTER KATEGORI -->
     <?php
      $kategoriAktif = "Semua Produk";

      if($slug){
          foreach($kategori as $k){
              if($k->url == $slug){
                  $kategoriAktif = ucwords(strtolower($k->nama));
              }
          }
      }
    ?>
    <div class="dropdown mb-3">
      <button 
        class="btn btn-secondary dropdown-toggle" 
        type="button" 
        id="kategoriDropdown" 
        data-bs-toggle="dropdown" 
        aria-expanded="false">
        <?= esc($kategoriAktif) ?>
      </button>
      <ul class="kategori-menu dropdown-menu" aria-labelledby="kategoriDropdown">
        <li>
          <a class="dropdown-item <?= (!$slug ? 'active' : '') ?>" 
            href="<?= site_url('katalog') ?>">
            Semua Produk
          </a>
        </li>
        <?php foreach($kategori as $r): ?>
          <li>
            <a class="dropdown-item <?= ($slug == $r->url ? 'active' : '') ?>" 
              href="<?= site_url('katalog/'.$r->url) ?>">
              <?= esc(ucwords(strtolower($r->nama))) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="row g-3">

      <?php foreach($produk as $p): ?>

      <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="product-card h-100" onclick="window.location.href='<?php echo site_url('produk/'.$p->url); ?>'">

          <!-- WRAPPER GAMBAR + HARGA + TOMBOL -->
          <div class="product-img-wrapper">
            <img src="<?= base_url('cdn/uploads/'.$p->gambar) ?>" alt="<?= esc($p->nama) ?>">

            <!-- BADGE HARGA -->
            <div class="price">
              <?php if($p->harga_coret > 0): ?>
                <div class="price-old">
                  Rp<?= number_format($p->harga_coret, 0, ',', '.') ?>
                </div>
              <?php endif; ?>

              <div class="price-new">
                Rp<?= number_format($p->harga, 0, ',', '.') ?>
              </div>
            </div>

            <!-- TOMBOL KERANJANG -->
            <button class="add-to-cart-btn" onclick="event.stopPropagation(); addtocart(<?=$p->id?>)">
              TAMBAH KE KERANJANG
            </button>
          </div>

          <!-- JUDUL PRODUK -->
          <h3><?= esc($p->nama) ?></h3>

        </div>
      </div>

      <?php endforeach ?>

    </div>
    <div class="pagination d-flex justify-content-center mt-4">

      <?= $pager->links('produk','bootstrap_full') ?>
    </div>
  </div>
</section>


<?= $this->endSection() ?>