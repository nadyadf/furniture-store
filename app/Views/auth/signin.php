<?= $this->include('partials/head_blank') ?>

<div style="margin-top:5vh">
	<div class="container pb-3">
		<div class="mb-4 text-center">
			<img class="logo" src="<?= base_url('cdn/assets/img/'.$set->logo) ?>" style=";max-width:70%;" />
		</div>

		<div class="row px-3">
			<div class="section col-md-6 ms-auto me-auto pt-4">
				<div class="fw-bold font-bold text-center mb-3 fs-4">Masuk ke Akun Anda</div>

				<div class="px-3 mx-0-xl px-2-sm" id="load">
					<?php if($set->login_otp == 0){ ?>

						<form id="signin" class="pb-4 px-4">
							<div class="mb-2">
								<input class="form-control" type="text" name="email" placeholder="Email" required>
							</div>

							<div class="mt-3 mb-2">
								<input class="form-control" type="password" name="pass" placeholder="Password" required>
							</div>

							<div class="row mb-4">
								<div class="col-6">
									<div class="form-check checkbox checkbox-danger">
										<input id="checkbox6" class="form-check-input dis-inline" type="checkbox" name="remember">
										<label for="checkbox6" class="form-check-label dis-inline cursor-pointer">
											Ingat Saya
										</label>
									</div>
								</div>

								<div class="col-6 text-end">
									<a href="javascript:void(0)" id="reset" class="text-danger">
										<b>Lupa Password?</b>
									</a>
								</div>
							</div>

							<div class="row mt-3">
								<div class="col-md-12">
									<button type="submit" id="submit" class="btn btn-submit w-100 btn-lg">
										MASUK
									</button>

									<p class="text-center mt-3 mb-2">
										Belum punya akun?&nbsp;
										<a href="<?= site_url("signup"); ?>" class="font-medium">
											Mendaftar
										</a>
									</p>
								</div>
							</div>
						</form>

					<?php } else { ?>

						<form id="signin_otp" class="pb-4 px-4">
							<div class="mb-2 text-center">
								Masukkan nomor whatsapp atau alamat email anda untuk mengirimkan kode otp
							</div>

							<div class="mb-3">
								<input class="form-control py-3 px-3 fs-20 font-medium text-center"
									   type="text"
									   name="email"
									   placeholder="No Handphone / Email"
									   required>
							</div>

							<div class="row mt-3">
								<div class="col-md-12">
									<button type="submit" id="submit" class="btn btn-primary w-100 btn-lg">
										MASUK
									</button>

									<p class="text-center mt-3 mb-2">
										Belum punya akun?&nbsp;
										<a href="<?= site_url("home/signup"); ?>" class="font-medium">
											Mendaftar
										</a>
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
	$(function(){
    $("#signin").on("submit", function(e){
        e.preventDefault();

        let submit = $("#submit").html();

        $(".form").prop("readonly", true);
        $("#submit").html(
            "<i class='fas fa-spin fa-compact-disc'></i> Tunggu sebentar..."
        );

        $.post({
            url: "<?= site_url('signin') ?>",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data){

                updateToken(data.token);

                if(data.success){
                    window.location.href = data.redirect;
                }else{
                    $("#submit").html(submit);
                    Swal.fire(
                        "Warning!",
                        "alamat email atau password salah",
                        "error"
                    );
                }
            }
        });
    });
});
</script>