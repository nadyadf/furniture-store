
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
  <!-- Slider -->	
  <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
    <?php $i = 0; foreach ($promo as $p): ?>
      <button 
        type="button"
        data-bs-target="#promoCarousel"
        data-bs-slide-to="<?= $i ?>"
        class="<?= $i == 0 ? 'active' : '' ?>"
        aria-current="<?= $i == 0 ? 'true' : 'false' ?>">
      </button>
    <?php $i++; endforeach; ?>
    </div>
    <div class="carousel-inner">
    <?php $i = 0; foreach($promo as $p): ?>
      <div class="carousel-item <?= $i==0 ? 'active' : '' ?>">

        <div class="container py-5">
          <div class="row min-vh-75">

            <!-- KIRI (4) -->
            <div class="col-lg-4">
              <div class="promo-content">
                <h1><?= $p->judul ?></h1>
                <h2><?= $p->sub_judul ?></h2>
                <a class="btn btn-primary mt-3"
                  href="<?= $p->url ?>">
                  Lihat Detail Produk
                </a>
              </div>
            </div>

            <!-- KANAN (6) -->
            <div class="col-lg-6 offset-md-2 text-center">
              <div class="image-wrapper position-relative">
                <div class="frame"></div>
                <img
                  src="<?= base_url('cdn/promo/'.$p->gambar) ?>"
                  alt="promo <?= $p->sub_judul ?>"
                  class="img-fluid hero-image">
              </div>
            </div>

          </div>
        </div>

      </div>
    <?php $i++; endforeach; ?>
    </div>
  </div>

  <!-- Category -->
  <section class="category-section py-5">
    <div class="container">

      <h2 class="mb-4 text-center fw-bold">Kategori Produk</h2>

      <div class="row g-4">

      <?php foreach ($kategori as $k): ?>
        <!-- ITEM -->
        <div class="col-6 col-md-4 col-lg-3">
          <a href="katalog/<?= $k->url ?>" class="category-card d-block">
            <img src="<?= base_url('cdn/uploads/'.$k->icon) ?>" alt="<?= $k->nama ?>" class="img-fluid">
            <div class="overlay">
              <span class="count"><?= $jmlProdukPerKategori[$k->id] ?? 0 ?> produk</span>
              <span class="label"><?=  $k->nama ?></span>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- PRODUK UNGGULAN -->
  <section class="featured-section py-5">
    <div class="container">

      <div class="text-center mb-4">
        <h2>Produk Unggulan</h2>
        <p class="subtitle">
          Produk yang paling banyak diminati oleh pelanggan kami
        </p>
      </div>

      <div class="row g-4">

      <?php foreach ($produkUnggulan as $pu): ?>
        <!-- PRODUCT -->
        <div class="col-6 col-md-4 col-lg-3">
          <div class="product-card h-100"  onclick="window.location.href='<?php echo site_url('produk/'.$pu->url); ?>'">
            <img src="<?= base_url('cdn/uploads/'.$pu->nama_gambar)?>" alt="" alt="<?= $pu->nama_gambar ?>" >
            <div class="price">
              <div class="price-old">
                Rp<?= number_format($pu->harga_coret, 0, ',', '.') ?>
              </div>

              <div class="price-new">
                Rp<?= number_format($pu->harga, 0, ',', '.') ?>
              </div>
            </div>
            <h3><?= $pu->nama ?></h3>
            <button class="add-to-cart-btn" onclick="event.stopPropagation(); addtocart(<?=$pu->id?>)">TAMBAH KE KERANJANG</button>
          </div>
        </div>
      <?php endforeach; ?>

      </div>

    </div>
  </section>


  <!-- PRODUK TERBARU -->
  <section class="featured-section py-5">
    <div class="container">

      <div class="text-center mb-4">
        <h2>Produk Terbaru</h2>
        <p class="subtitle">
          Temukan produk yang sesuai dengan konsep rumah impian Anda
        </p>
      </div>

      <div class="row g-4">

      <?php foreach ($produkTerbaru as $pt): ?>
        <!-- PRODUCT -->
        <div class="col-6 col-md-4 col-lg-3">
          <div class="product-card h-100" onclick="window.location.href='<?php echo site_url('produk/'.$pt->url); ?>'">
            <img src="<?= base_url('cdn/uploads/'.$pt->gambar)?>" alt="" alt="<?= $pt->gambar ?>" >
            <div class="price">
              <div class="price-old">
                Rp<?= number_format($pt->harga_coret, 0, ',', '.') ?>
              </div>

              <div class="price-new">
                Rp<?= number_format($pt->harga, 0, ',', '.') ?>
              </div>
            </div>
            <h3><?= $pt->nama ?></h3>
            <button class="add-to-cart-btn" onclick="event.stopPropagation(); addtocart(<?=$pt->id?>)">TAMBAH KE KERANJANG</button>
          </div>
        </div>
      <?php endforeach; ?>

      </div>
      <div class="show-more">
          <a href="<?= site_url('katalog') ?>" class="btn-show-more">
              Tampilkan Lebih Banyak
          </a>
      </div>
    </div>
  </section>

<?= $this->endSection() ?>