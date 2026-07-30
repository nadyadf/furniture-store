<html>

<head>
	<style>
		
	</style>
</head>

<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNavbar">
			<div class="container-fluid">
				<div class="row w-100 mx-0 text-center text-md-start">

					<!-- BARIS 1: BRAND (Rata Tengah di Mobile) -->
					<div class="col-12 col-md-6 col-lg-4 mb-2 mb-lg-0 d-flex align-items-center justify-content-center justify-content-md-start">
						<a class="navbar-brand d-flex align-items-center m-0" href="<?=site_url()?>">
							<img src="<?= base_url('cdn/assets/img/'.$set->favicon) ?>" height="40" />
							<span class="h4 brand-name ms-2 mb-0"><?= $set->nama ?></span>
						</a>
					</div>

					<!-- BARIS 2: MENU NAVIGASI (Rata Tengah di Mobile) -->
					<div class="col-12 col-md-6 col-lg-5 mb-3 mb-lg-0 d-flex align-items-center justify-content-center justify-content-md-end justify-content-lg-start">
						<ul class="navbar-nav flex-row justify-content-center align-items-center w-100">
							<li class="nav-item mx-2 mx-md-3">
								<a class="nav-link py-1" href="<?=site_url("katalog")?>">KATALOG</a>
							</li>
							
							<li class="nav-item dropdown mx-2 mx-md-3 position-relative">
								<a class="nav-link dropdown-toggle py-1" role="button" data-bs-toggle="dropdown" aria-expanded="false">KATEGORI</a>
								<ul class="dropdown-menu shadow">
									<?php foreach ($kategori as $k): ?>
										<li><a class="dropdown-item" href="<?=site_url('katalog/'. $k->url) ?>"><?= esc($k->nama)?> </a></li>   
									<?php endforeach; ?>
								</ul>
							</li>

							<?php if(!$isLogin){ ?>
								<li class="nav-item mx-2 mx-md-3 d-flex align-items-center">
									<a class="btn btn-outline-light btn-sm" href="<?=site_url("signin")?>">
										<i class="fas fa-sign-in-alt me-1"></i> Masuk
									</a>
								</li>
							<?php } else { ?>
								<li class="nav-item mx-2 mx-md-3">
									<a class="nav-link pesanan py-1" href="<?=site_url('manage/pesanan')?>"><i class="fas fa-box"></i> Pesanan</a>
								</li>
							<?php } ?>
						</ul>
					</div>

					<!-- BARIS 3: SEARCH BAR + KERANJANG + PROFIL (Rata Tengah di Mobile) -->
					<div class="col-12 col-md-12 col-lg-3 mb-1 mb-lg-0 d-flex align-items-center justify-content-center justify-content-md-start gap-3">
						<form style="max-width:240px; width:100%;" action="<?= !empty($slug) ? site_url('katalog/'.$slug) : site_url('katalog') ?>">
							<div class="input-group input-group-sm">
								<input type="text" class="form-control rounded-start-pill" name="cari" placeholder="Cari Produk" value="<?= esc($cari ?? '') ?>" />
								<button class="btn btn-light rounded-end-pill px-3" type="submit">🔍</button>
							</div>
						</form>

						<a href="<?= site_url('keranjang') ?>" class="text-white text-decoration-none d-inline-flex align-items-center ms-1">
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