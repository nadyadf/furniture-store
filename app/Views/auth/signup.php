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
									<a href="<?= site_url("signin"); ?>" class="fw-medium">Masuk</a>
								</p>

							</div>
						</div>
					</form>

					<?php } ?>


				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->include('partials/foot_blank') ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}

	$(function(){
		localStorage["error"] = 1;

		$("#signup").on("submit", function(e){
			e.preventDefault();

			if(localStorage["error"] == 0){

				if($("#email").val().length > 8){

					$("input").prop("readonly", true);
					// $("select").prop("disabled", true);

					$("#proses").show();
					$("#submit").hide();

					$.post({
						url: "<?= site_url('signup') ?>",
						data: $(this).serialize(),
						dataType: "json",
						success: function(res){

							updateToken(res.token);
							if(res.success){
								$("#load").html(res.result);
								$('html, body').animate({
									scrollTop: $("#load").offset().top - 300
								});
							}else{
								Swal.fire(
									"Belum sesuai",
									"Cek kembali alamat email atau nomor handphone",
									"error"
								);
							}
						}
					});

				}
			}
		});

		$("#email,#emailhp").keyup(function(){
			$("#submit").hide();
			$(".imelcek").show();
			$("#imelerror").hide();
		});

		$("#email").change(function(){

			$("#submit").hide();
			$(".imelcek").show();

			let email = $(this).val();

			if(email.includes("@") && email.includes(".")){

				$.ajax({
					url: "<?= site_url('signup/cekemail') ?>",
					type: "POST",
					dataType: "json",
					data: {
						email: email,
						[$("#names").val()] : $("#tokens").val()
					},

					success: function(result){

						$("#submit").show();
						$(".imelcek").hide();

						updateToken(result.token);

						if(result.success){
							$("#imelerror").hide();
							localStorage["error"] = 0;
						}else{
							localStorage["error"] = 1;
							$("#imelerror").show();
							$("#imelerror small").html(result.message);
						}
					}
				});

			}else{
				$("#submit").show();
				$(".imelcek").hide();
				localStorage["error"] = 1;

				$("#imelerror").show();
				$("#imelerror small").html("masukkan format email dengan benar");
			}
		});

		$("#emailhp").change(function(){

			$("#submit").hide();
			$(".imelcek").show();

			$.ajax({
				url: "<?= site_url('signup/cekemail') ?>",
				type: "POST",
				dataType: "json",
				data: {
					email: $("#emailhp").val(),
					[$("#names").val()] : $("#tokens").val()
				},

				success: function(result){

					$("#submit").show();
					$(".imelcek").hide();

					updateToken(result.token);

					if(result.success){
						$("#imelerror").hide();
						localStorage["error"] = 0;
					}else{
						$("#imelerror").show();
						localStorage["error"] = 1;
						$("#imelerror small").html(result.message);
					}
				}
			});

		});
	});
</script>