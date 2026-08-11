<html>

<head>
	<style>
		
	</style>
</head>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNavbar">
      <div class="container-fluid px-lg-4">
        <!-- Menggunakan d-flex justify-content-between agar 3 bagian utama otomatis terbagi rata -->
        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap flex-lg-nowrap gap-2">

          <!-- BAGIAN 1: BRAND / LOGO (Kiri) -->
          <div class="d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center m-0" href="<?=site_url()?>">
              <img src="<?= base_url('cdn/assets/img/'.$set->favicon) ?>" height="36" />
              <span class="h4 brand-name ms-2 mb-0 text-nowrap"><?= $set->nama ?></span>
            </a>
          </div>

          <!-- BAGIAN 2: MENU NAVIGASI (Tengah Auto-Center) -->
          <div class="d-flex align-items-center justify-content-center mx-lg-auto">
            <ul class="navbar-nav flex-row align-items-center mb-0">
              <li class="nav-item mx-2 mx-xl-3">
                <a class="nav-link py-1 text-nowrap" href="<?=site_url("katalog")?>">KATALOG</a>
              </li>
              
              <li class="nav-item dropdown mx-2 mx-xl-3 position-relative">
                <a class="nav-link dropdown-toggle py-1 text-nowrap" role="button" data-bs-toggle="dropdown" aria-expanded="false">KATEGORI</a>
                <ul class="dropdown-menu shadow">
                  <?php foreach ($kategori as $k): ?>
                    <li><a class="dropdown-item" href="<?=site_url('katalog/'. $k->url) ?>"><?= esc($k->nama)?> </a></li>   
                  <?php endforeach; ?>
                </ul>
              </li>

              <!-- DROPDOWN LACAK PESANAN (KHUSUS NON-MEMBER) -->
              <?php if(!$isLogin){ ?>
                <li class="nav-item dropdown mx-2 mx-xl-3 position-relative">
                  <a class="nav-link dropdown-toggle py-1 text-nowrap" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    LACAK PESANAN
                  </a>
                  <ul class="dropdown-menu shadow">
                    <li>
                      <a class="dropdown-item" href="<?=site_url('cek-pesanan')?>">
                        <i class="fas fa-search me-2 text-primary"></i> Cek Status Pesanan
                      </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <a class="dropdown-item" href="<?=site_url('konfirmasi')?>">
                        <i class="fas fa-file-invoice-dollar me-2 text-success"></i> Konfirmasi Pembayaran
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>

              <!-- TOMBOL MASUK / PESANAN -->
              <?php if(!$isLogin){ ?>
                <li class="nav-item mx-2 mx-xl-3 d-flex align-items-center">
                  <a class="btn btn-outline-light btn-sm text-nowrap px-3" href="<?=site_url("signin")?>">
                    <i class="fas fa-sign-in-alt me-1"></i> Masuk
                  </a>
                </li>
              <?php } else { ?>
                <li class="nav-item mx-2 mx-xl-3">
                  <a class="nav-link pesanan py-1 text-nowrap" href="<?=site_url('manage/pesanan')?>"><i class="fas fa-box"></i> Pesanan</a>
                </li>
              <?php } ?>
            </ul>
          </div>

          <!-- BAGIAN 3: SEARCH BAR + KERANJANG + PROFIL (Kanan) -->
          <div class="d-flex align-items-center justify-content-end gap-2 gap-xl-3">
            <form style="max-width:180px; width:100%;" action="<?= !empty($slug) ? site_url('katalog/'.$slug) : site_url('katalog') ?>">
              <div class="input-group input-group-sm">
                <input type="text" class="form-control rounded-start-pill" name="cari" placeholder="Cari Produk" value="<?= esc($cari ?? '') ?>" />
                <button class="btn btn-light rounded-end-pill px-2" type="submit">🔍</button>
              </div>
            </form>

            <a href="<?= site_url('keranjang') ?>" class="text-white text-decoration-none d-inline-flex align-items-center text-nowrap">
              🛒<span class="fs-6 jmlkeranjang ms-1"><?= $keranjang ?></span>
            </a>

            <?php if($isLogin){?>
              <div class="dropdown">
                <button class="btn border-0 p-0 text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <span style="font-size:24px;">
                    <i class="fa-solid fa-circle-user"></i>
                  </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li><a class="dropdown-item" href="/akun">Akun Saya</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <button type="button" class="dropdown-item text-danger" onclick="signoutNow()">Log out</button>
                  </li>
                </ul>
              </div>
            <?php } ?>
          </div>

        </div>
      </div>
    </nav>
  </header>
	<form id="logoutForm"
				action="<?= site_url('signout') ?>"
				method="post"
				class="d-none">
		<?= csrf_field() ?>
	</form>
	<script src="https://kit.fontawesome.com/6d173f80fe.js" crossorigin="anonymous"></script>

</html>