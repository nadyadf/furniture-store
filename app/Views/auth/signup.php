<?= $this->include('partials/head_blank') ?>

<div style="margin-top:5vh">
	<div class="container pb-3">
		<div class="mb-4 text-center">
			<img class="logo" src="<?= base_url('cdn/assets/img/'.$set->logo) ?>" style="max-width:70%;" />
		</div>

		<div class="row px-4">
			<div class="section col-md-6 ms-auto me-auto pt-4">
				<div class="fw-bold font-bold text-center mb-3 fs-4">Mendaftar</div>

				<div class="px-5 mx-xl-0 px-sm-3" id="load">
					<?php if($set->login_otp == 0){ ?>

					<form id="signup" class="pb-5 px-5">

						<div class="mb-3">
							<input class="form-control" type="text" id="nama" name="nama" placeholder="Nama Lengkap" required>
						</div>

						<div class="mb-3">
							<input onkeypress="return isNumber(event)" class="form-control" type="text" name="nohp" placeholder="No Whatsapp" required>
						</div>

						<div class="bor8 mb-3 how-pos4-parent">
							<input class="form-control" type="text" id="email" name="email" placeholder="Alamat Email" required>
						</div>

						<p id="imelerror" class="text-danger" style="display:none;">
							<small>terjadi kesalahan, mohon formulir dilengkapi dulu</small>
						</p>

						<div class="bor8 mt-3 mb-3 how-pos4-parent">
							<input class="form-control" type="password" name="pass" placeholder="Password" required>
						</div>

						<div class="rs1-select2 rs2-select2 bor8 how-pos4-parent mb-3">
							<select class="form-select js-select2" name="kelamin" required>
								<option value="">Jenis Kelamin</option>
								<option value="1">Laki - laki</option>
								<option value="2">Perempuan</option>
							</select>
							<div class="dropDownSelect2"></div>
						</div>

						<div class="row mt-2">
							<div class="col-md-12">

								<p class="text-warning imelcek" style="display:none;">
									<i class="fas fa-spin fa-compact-disc"></i> sedang memeriksa...
								</p>

								<div id="proses" style="display:none;">
									<h5 class="cl1">
										<i class="fas fa-compact-disc fa-spin text-success"></i> Memproses...
									</h5>
								</div>

								<button id="submit" type="submit" class="btn btn-submit btn-lg w-100">
									MENDAFTAR
								</button>

								<button type="button" class="btn btn-medium btn-lg w-100 imelcek" style="display:none;">
									MENDAFTAR
								</button>

								<p class="text-center mt-4 mb-2">
									Sudah punya akun?&nbsp;
									<a href="<?= site_url("signin"); ?>" class="fw-medium">Masuk</a>
								</p>

							</div>
						</div>
					</form>

					<?php } else { ?>

					<form id="signup_otp" class="pb-5 p-lr-30">

						<div class="mb-3">
							<input class="form-control p-tb-28 p-lr-24 fs-6 fw-medium text-center"
								   type="text"
								   name="nama"
								   placeholder="Masukkan Nama Lengkap"
								   required>
						</div>

						<div class="mb-2 text-center">
							masukkan nomor whatsapp atau alamat email anda untuk mengirimkan kode otp
						</div>

						<div class="mb-3">
							<input class="form-control p-tb-28 p-lr-24 fs-5 fw-medium text-center"
								   type="text"
								   id="emailhp"
								   name="email"
								   placeholder="No Handphone / Email"
								   required>

							<p id="imelerror" class="text-danger" style="display:none;">
								<small>terjadi kesalahan, mohon formulir dilengkapi dulu</small>
							</p>

							<p class="text-warning imelcek" style="display:none;">
								<i class="fas fa-spin fa-compact-disc"></i> sedang memeriksa...
							</p>
						</div>

						<div class="row mt-4">
							<div class="col-md-12">

								<div id="proses" style="display:none;">
									<h5 class="cl1">
										<i class="fas fa-compact-disc fa-spin text-success"></i> Memproses...
									</h5>
								</div>

								<button id="submit" type="submit" class="btn-submit btn btn-lg w-100">
									MENDAFTAR
								</button>

								<button type="button" class="btn btn-medium btn-lg w-100 imelcek" style="display:none;">
									MENDAFTAR
								</button>

								<p class="text-center mt-4 mb-2">
									Sudah punya akun?&nbsp;
									<a href="<?= site_url("home/signin"); ?>" class="fw-medium">Masuk</a>
								</p>

							</div>
						</div>
					</form>

					<?php } ?>

					<div class="line-text pt-4 pb-2">
						<div class="text"><span>metode lainnya</span></div>
					</div>

					<div class="text-center pb-4">
						<a href="<?=$google_url?>" class="btn btn-default btn-lg">
							<img src="<?=base_url("assets/img/google.png")?>" style="height:26px;" class="p-r-12" />
							<small><b>Signup with Google</b></small>
						</a>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->include('partials/foot_blank') ?>