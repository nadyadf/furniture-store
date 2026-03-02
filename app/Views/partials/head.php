<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNavbar">
			<div class="container-fluid">
				<div class="row w-100 text-center text-md-left">

					<!-- BRAND -->
					<div class="col-12 col-md-6 col-lg-4 mb-2 mb-lg-0 d-flex align-items-center justify-content-center justify-content-md-start">
						<a class="navbar-brand d-flex align-items-center m-0" href="<?=site_url()?>">
							<img src="<?= base_url('cdn/assets/img/'.$set->favicon) ?>" height="50" />
							<span class="h3 brand-name ml-4 mb-0"><?= $set->nama ?></span>
						</a>
					</div>
			

					<!-- MENU -->
					<div class="col-12 col-md-6 col-lg-4 mb-2 mb-lg-0 d-flex align-items-center justify-content-center justify-content-md-end justify-content-lg-start">
						<ul class="navbar-nav flex-row">
							<li class="nav-item mx-2">
								<a class="nav-link" href="<?=site_url()?>">KATALOG</a>
							</li>
							<li class="nav-item dropdown mx-2">
								<a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false"></i> KATEGORI</a>
								<ul class="dropdown-menu">
								<?php foreach ($kategori as $k): ?>
									<li><a class="dropdown-item" href="#"><?= esc($k->nama); ?></a></li>		
								<?php endforeach; ?>
								</ul>
							</li>
							<?php if(!$isLogin){ ?>
							<li class="nav-item mx-2 d-flex align-items-center">
								<a class="btn btn-outline-light btn-sm ms-2" href="<?=site_url("signin")?>">
									<i class="fas fa-sign-in-alt me-1"></i> Masuk / Daftar
								</a>
							</li>
							<?php }else{ ?>
							<li class="nav-item mx-2">
								<a class="nav-link pesanan" href="<?=site_url('manage/pesanan')?>"><i class="fas fa-box"></i> Pesanan</a>
							</li>
							<?php } ?>
						</ul>
					</div>

					<!-- SEARCH -->
					<div class="col-12 col-md-12 col-lg-4 mb-2 mb-lg-0 d-flex align-items-center justify-content-md-start justify-content-center gap-3 pe-0">
						<form style="max-width:300px; width:100%;" action="<?=site_url("shop")?>">
							<div class="input-group">
								<input type="text" class="form-control rounded-start-pill" name="cari" placeholder="Cari Produk" />
								<button class="btn btn-light rounded-end-pill">🔍</button>
							</div>
						</form>
						<a href="#" class="text-white text-decoration-none d-inline-flex align-items-center">
              🛒<span class="fs-6"><?= $keranjang ?></span>
            </a>
						<?php if($isLogin){?>
						<div class="dropdown">
							<button class="btn border-0 p-0"
											style="padding-top:5px;"
											type="button"
											data-bs-toggle="dropdown"
											aria-expanded="false"
							>
									<span style="font-size:26px; color:white;">
										<i class="fa-solid fa-circle-user"></i>
									</span>
							</button>
							<ul class="dropdown-menu dropdown-menu-end">
								<li>
									<a class="dropdown-item" href="/akun">
										Akun Saya
									</a>
								</li>
								<li><hr class="dropdown-divider"></li>
								<li>
									<a class="dropdown-item text-danger"  href="javascript:voi(0)" onclick="signoutNow()">
										Log out
									</a>
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
	