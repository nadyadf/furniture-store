<?=  $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-breadcrumb first-section">
  <nav aria-label="breadcrumb first-section">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= site_url(); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= site_url('katalog/'.$kategoriproduk->url); ?>"><?= ucwords(strtolower($kategoriproduk->nama)); ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $data->nama; ?></li>
      </ol>
  </nav>
</div>

<section class="sec-product-detail py-4">
    <div class="container">
        <div class="row">

            <div class="col-12 col-lg-8 mb-4">

                <?php if (!empty($data->gambar)) : ?>

                    <!-- Preview -->
                    <div class="prod-image-container border rounded overflow-hidden mb-3 shadow-lg">
                        <img
                            src="<?= base_url('cdn/uploads/' . $data->gambar[0]->nama) ?>"
                            alt="<?= esc($data->nama) ?>"
                            id="prod-img"
                            class="prod-image img-fluid w-100">
                    </div>

                    <!-- Thumbnail -->
                    <div class="d-flex flex-wrap gap-2">

                        <?php foreach ($data->gambar as $gambar) : ?>

                            <div
                                class="border rounded overflow-hidden thumb-item"
                                style="width:80px;height:80px;cursor:pointer;"
                                data-thumb="<?= base_url('cdn/uploads/' . $gambar->nama) ?>">

                                <img
                                    src="<?= base_url('cdn/uploads/' . $gambar->nama) ?>"
                                    alt="<?= esc($data->nama) ?>"
                                    class="w-100 h-100 object-fit-cover">

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

            <div class="col-lg-4 pb-3">
					<div class="bg-white rounded-4 shadow-sm p-4">
						<h1 class="h3 fw-semibold mb-3">
                            <?= esc($data->nama) ?>
                        </h1>

                        <div class="text-muted mb-4">
                            Dikirim dari:
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <strong><?= esc($kota->tipe) ?> <?= esc($kota->nama) ?></strong>
                        </div>

						

						<?php if ($data->harga_coret > 0): ?>
                            <div class="text-decoration-line-through fs-5 harga-coret">
                                Rp <?= number_format($data->harga_coret, 0, ',', '.') ?>
                            </div>
                        <?php endif; ?>

                        <div id="hargacetak" class="fs-3 fw-bold text-success mb-4">
                            Rp <?= number_format($data->harga, 0, ',', '.') ?>
                        </div>

						<?php
							if($data->totalstok > 0){
						?>
						<form id="keranjang">
						  <input type="hidden" name="idproduk" value="<?php echo $data->id; ?>" />
						  <input type="hidden" id="variasi" name="variasi" value="0" />
						  <input type="hidden" id="harga" name="harga" value="<?=$data->harga?>" />
							<div class="p-t-10">
								<div class="flex-w p-b-10">
									<?php
										if($varproduk !== null){
											foreach($varproduk as $var){
												$warnaid[] = $var->idwarna;
											
												$variasi[$var->idwarna][] = $var->id;
												$har[$var->idwarna][] = $var->harga;
												$stok[$var->idwarna][] = $var->stok;
											}

											$warnaid = array_values(array_unique($warnaid));
									?>
									<!-- <div class="col-12 p-lr-0 m-b-6">
									<?=ucwords(strtolower($data->variasi))?>
									</div> -->
									<input type="hidden" id="warna" >
									<div class="col-12 px-0 mb-2 d-flex flex-wrap gap-2" id="pilihwarna">
										<?php
											foreach($varproduk as $var){
												$hg = $var->harga;
										
												if($var->stok > 0){
													echo "<button type='button' class='btn btn-outline-secondary rounded-pill px-3 py-2 btn-variasi' data-warna='".$var->idwarna."' data-stok='".$var->stok."' data-harga='".$hg."' data-variasi='".$var->id."' data-thumb=". base_url('cdn/uploads/' . $var->gambar). ">".$var->warna."</button>";
												}
											}
										?>
									</div>
									<?php
										}
									?>
									<div class="col-12 px-0">
										Jumlah
									</div>
									<div class="mb-2 col-12 row px-0 mx-0 align-items-center">
                                        <div class="col-6 px-0 my-2">
                                            <div class="wrap-num-product input-group">
                                                <button class="btn btn-outline-secondary btn-num-product-down" type="button">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                
                                                <input class="form-control text-center num-product" type="number" min="<?= $data->min_order; ?>" name="jumlah" value="<?= $data->min_order; ?>" id="jumlahorder" required>
                                                    
                                                <button class="btn btn-outline-secondary btn-num-product-up" type="button">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
										<div class="col-6 px-3">
											<div class="small text-muted">
                                                Stok :
                                                <strong id="stokrefresh"><?= $data->totalstok ?></strong> Pcs
                                            </div>

                                            <div class="small text-muted">
                                                Min. Order :
                                                <strong><?= $data->min_order ?></strong> Pcs
                                            </div>
										</div>
									</div>
									<div class="col-12 mb-0 px-0">
										<label class="form-label mt-3">
                                            Catatan Pembeli
                                        </label>
									</div>
									<div class="col-12 px-0">
										<input class="form-control" type="text" name="keterangan" value="" placeholder="Tulis catatan untuk penjual...">
									</div>

									<?php if($isLogin) { ?>
									<div class="col-12 mt-5 px-0">
										<button type="button" class="btn btn-lg btn-success w-100 mb-3" onclick="pesanProduk(<?=$data->id?>)"><i class="fas fa-comment-dots"></i> &nbsp;Tanyakan Admin</button>
										<button type="submit" id="submit" class="btn btn-lg btn-primary w-100"><i class="fa-solid fa-cart-shopping"></i> &nbsp;Tambah ke Keranjang</button>
										<span id="proses" class="" style="display:none;"><b><i class="fa fa-spin fa-spinner text-primary"></i> Memproses pesanan</b></span>
										<span id="gagal" class="mt-3" style="display:none;"><i class="text-danger fa fa-exclamation-triangle"></i> Gagal memproses pesanan.</span>
									</div>
									<?php }  ?>
								</div>
							</div>
						</form>
						<?php }else{ ?>
						<div class="py-2 px-3 mb-3 mt-4 btn font-medium bg-danger text-light btn-block">
							Maaf, Stok telah habis
						</div>
						<?php } ?>
						<?php if(!$isLogin) { ?>
                            <div class="col-12 mt-5 px-0">
                                <a href="<?=site_url("signin")?>" class="btn btn-lg w-100 btn-buy">
                                    Beli Produk
                                </a>
                            </div>
                        <?php } ?>
					</div>
				</div>

                <div class="container my-5">
                    <div class="row g-4">

                        <!-- Deskripsi -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h4 class="mb-3">Deskripsi Produk</h4>

                                    <p class="text-muted mb-0">
                                        <?= $data->deskripsi ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Spesifikasi -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h4 class="mb-3">Spesifikasi</h4>

                                    <?php $spesifikasi = explode('|', $data->spesifikasi) ?>
                                    <table class="table table-sm align-middle mb-0">
                                        <tbody>
                                            <?php foreach ($spesifikasi as $spec) : 
                                                $sp = explode(':', $spec)?>
                                                <tr>
                                                    <td class="text-muted"><?= $sp[0] ?></td>
                                                    <td><?= $sp[1] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <section class="container my-5">
                            <div class="text-center mb-5">
                                <h3 class="mb-0 title-section">Produk Terkait</h3>
                            </div>

                            <div class="row g-4">
                                <?php foreach($produkterkait as $pt): ?>
                                    <div class="col-6 col-md-6 col-lg-3">
                                        <div class="product-card card border-0 shadow-sm h-100" onclick="window.location.href='<?php echo site_url('produk/'.$pt->url); ?>'">
                                            <img src="<?= base_url('cdn/uploads/'.$pt->gambar) ?>">

                                            <div class="price">

                                                <?php if($pt->harga_coret > 0): ?>
                                                <div class="price-old">
                                                Rp<?= number_format($pt->harga_coret,0,',','.') ?>
                                                </div>
                                                <?php endif ?>

                                                <div class="price-new">
                                                Rp<?= number_format($pt->harga,0,',','.') ?>
                                                </div>

                                            </div>

                                            <h3><?= esc($pt->nama) ?></h3>

                                        </div>    
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
			</div>
        </div>
    </div>
</section>

<script>

    <?php if(!(empty($varproduk))){ ?>
	    var variasi = true;
	<?php } else { ?>
	    var variasi = false;
	<?php }?>

    $(function () {

        $("#pilihwarna .btn").on("click", function () {

            $("#pilihwarna .btn")
                .removeClass("btn-active")
                .addClass("btn-outline-secondary");

            $(this)
                .removeClass("btn-outline-secondary")
                .addClass("btn-active");

            $("#warna").val($(this).data("warna"));

            $("#variasi").val($(this).data("variasi"));

            $("#jumlahorder").prop("max", $(this).data("stok"));

            $("#harga").val($(this).data("harga"));

            $("#hargacetak").text(
                "Rp " + Number($(this).data("harga")).toLocaleString("id-ID")
            );

            $("#stokrefresh").text(
                $(this).data("stok")
            );

            $("#prod-img").attr("src",$(this).data("thumb"));
        });

        $(".thumb-item").on("click",function(){
			$("#prod-img").attr("src",$(this).data("thumb"));
		});

        $("#jumlahorder").change(function(){
			if(parseInt($(this).val()) < parseInt($(this).attr("min"))){
				$(this).val($(this).attr("min")).trigger("change");
			}
			
			if(parseInt($(this).val()) > parseInt($(this).attr("max"))){
				$(this).val($(this).attr("max")).trigger("change");
			}
		});

        $("#keranjang").on("submit",function(e){
			e.preventDefault();
			<?php if($isLogin){ ?>
                if(variasi == true && $("#variasi").val() == 0){
                    swal.fire("Pilih Varian", "pilih varian produk terlebih dahulu sebelum menambahkan produk ke keranjang", "warning");
                } else {
                    
                    var submit = $("#submit");
                    let original = submit.html();

                    submit.html("<i class='fas fa-compact-disc fa-spin'></i> Memproses...");
                    // $("#submit").html("<i class='fas fa-compact-disk fa-spin'></i> memproses...");
                    var datar = $(this).serialize();
                    datar += "&" + $("#names").val() + "=" + $("#tokens").val();
                    $.ajax({

                        url: "<?= site_url('assync/prosesbeli') ?>",
                        type: "POST",
                        data: datar,
                        dataType: "json",

                        success: function(data){

                            updateToken(data.token);

                            closeatc();

                            submit.html(original);

                            if(data.success){

                                // fbq('track','AddToCart',{
                                //     content_ids: "<?=$data->id?>",
                                //     content_type: "<?=$kategoriproduk->nama?>",
                                //     content_name: "<?=$data->nama?>",
                                //     currency: "IDR",
                                //     value: data.total
                                // });

                                updateKeranjang();

                                Swal.fire(
                                    "<?=$data->nama?>",
                                    "Berhasil ditambahkan ke keranjang",
                                    "success"
                                );

                            }else{

                                Swal.fire(
                                    "Gagal",
                                    "Tidak dapat memproses pesanan\n" + data.msg,
                                    "error"
                                );

                            }

                        },

                        error: function(){

                            btn.html(original);

                            Swal.fire(
                                "Server Error",
                                "Terjadi kesalahan pada server",
                                "error"
                            );

                        }

                    });
                }
            <?php } ?>
        });
    });

</script>

<?= $this->endSection() ?>