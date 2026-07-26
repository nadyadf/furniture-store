<?php if(count($unpaidPayments) > 0) { ?>
    <div class="pesanan">
			<?php 
				foreach($unpaidPayments as $unp) {
					$link = $unp->id; 
					$klik = "openLink('".site_url("home/invoice?inv=".$link."&ubahmetode=true")."')";
				?>
					
				<div class="mb-4">
					<div class="pesanan-item px-4 py-4">
							<div class="row pb-4">
									<div class="col-7">
											<span class="text-dark fw-medium fs-5">
													No. Invoice&nbsp; 
													<span class="text-success">#<?= $unp->invoice; ?></span>
											</span>
									</div>

									<div class="col-5 text-end">
										<!-- Menambahkan kelas me-1 untuk memberi sedikit margin kanan pada tombol ini -->
										<a href="javascript:void(0)" 
											onclick="batal(<?= $unp->id; ?>)" 
											class="btn btn-danger btn-sm me-1">
												<i class="fas fa-times-circle"></i> 
												batal<span class="d-none d-sm-inline">kan pesanan</span>
										</a>
										<a href="<?= site_url("manage/detailpesanan/?orderid=").$unp->transaksi->orderid; ?>" class="btn btn-sm btn-primary">
												<i class="fas fa-angle-double-right"></i> Rincian<span class="hidesmall"> Pesanan</span>
										</a>
									</div>
							</div>

							<div class="row mx-0">
								<div class="col-md-8 px-0 mb-2">
									<?php 
									$no = 1;
									if (isset($unp->transaksi) && isset($unp->transaksi->produk_list)):
										foreach ($unp->transaksi->produk_list as $item):
											if ($no == 2){ ?>
												<div class="mb-4 show-product"> 
											<?php
											}
											?>
											
											<div class="row pb-4 mx-0 produk-item"> 
												<div class="col-4 col-md-2">
													<?php 
														$gambarUrl = (!empty($item->variasi_detail->gambar->nama)) 
															? base_url('cdn/uploads/'. $item->variasi_detail->gambar->nama) 
															: base_url('cdn/uploads/default.jpg'); // Sediakan gambar default jika kosong
													?>
													<div class="img" style="background-image:url('<?= $gambarUrl ?>')" alt="IMG"></div>
												</div>
												
												<div class="col-8 col-md-10">
													<span class="fw-medium text-dark w-100 d-block"> 
														<?php if($item->produk != null){ 
															echo $item->produk->nama; 
														} else { 
															echo "Produk telah dihapus"; 
														} ?>
													</span>

													<?php if (!empty($item->variasi_detail) && isset($item->variasi_detail->warna)): ?>
														<span class="text-muted d-block small">Warna: <?= $item->variasi_detail->warna->nama ?? '-'; ?></span>
													<?php endif; ?>

													<div class="mt-1">
														Rp <?= number_format($item->harga, 0, ',', '.') ?> 
														<span class="text-secondary" style="font-size:11px">x<?= $item->jumlah ?></span>
													</div>
												</div>
											</div>
											
										<?php
										$no++;
										endforeach; 

										if ($no > 2) { ?>
											</div>
												<div class="pb-4 pe-2"> <!-- Mengubah p-b-30 ke pb-4, dan p-r-10 ke pe-2 -->
													<a href="javascript:void(0)" class="view-product text-info text-decoration-none"><i class="fas fa-chevron-circle-down"></i> Lihat produk lainnya</a>
													<a href="javascript:void(0)" class="view-product text-info text-decoration-none" style="display:none;"><i class='fas fa-chevron-circle-up'></i> Sembunyikan produk</a>
												</div>
         					 <?php
										}
									endif; 
									?>
								</div>

								<div class="row mx-0 px-3 col-md-4">
									<div class="text-dark fs-5 px-0 col-6 col-md-12">
										Total<span class="d-none d-sm-inline"> Pembayaran</span>
									</div>
									
									<div class="text-danger fs-4 px-0 col-6 col-md-12 fw-bold">
										Rp <?= number_format($unp->total, 0, ',', '.') ?>
									</div>
								</div>
							</div>
							<hr>
							<div class="row">
								<!-- Sisi Informasi Pembayaran -->
								<div class="col-md-6 mb-3">
									<p class="mb-0">Segera lakukan pembayaran dalam <b class="text-danger">1 x 24 jam</b>, atau pesanan Anda akan Otomatis Dibatalkan.</p>
								</div>
								
								<div class="col-md-6">
									<div class="row">
										<?php if (isset($unp->konfirmasi) && !empty($unp->konfirmasi)): ?>
											
											<div class="col-md-12 text-center">
												<div class="alert alert-info py-2 px-3 mb-0">
													<p class="mb-1"><b>Status Pembayaran:</b> <span class="fst-italic">Menunggu verifikasi sistem</span></p>
													
													<?php foreach ($unp->konfirmasi as $konf): 
														$timestamp = strtotime($konf->tgl);
          
														$formatter = new IntlDateFormatter(
																'id_ID', 
																IntlDateFormatter::LONG, 
																IntlDateFormatter::SHORT, 
																'Asia/Jakarta', 
																IntlDateFormatter::GREGORIAN, 
																"dd MMM yyyy HH:mm" 
														);
														$tanggalIndo = $formatter->format($timestamp);
													?>
														<p class="mb-0 text-muted small">
															<b>Waktu Konfirmasi:</b> 
															<span><?= $tanggalIndo ?> WIB</span>
														</p>
													<?php endforeach; ?>
													
												</div>
											</div>

										<?php else: ?>
											
											<div class="col-md-8 d-grid mb-2 mb-md-0"> 
												<a href="javascript:void(0)" onclick="<?= $klik ?>" class="btn btn-success w-100">
													Ubah Metode Pembayaran
												</a>
											</div>
											
											<div class="col-md-4 d-grid">
												<a href="javascript:void(0)" onclick="konfirmasi(<?= is_array($unp) ? $unp['id'] : $unp->id; ?>)" class="btn btn-warning w-100">
													Konfirmasi
												</a>
											</div>

										<?php endif; ?>

									</div>
								</div>
							</div>
							<hr/>

							<div class="row align-items-center">
								<!-- Sisi Informasi / Deskripsi -->
								<div class="col-md-6 mb-3 mb-md-0">
									<b class="text-danger d-block mb-1">LABEL PENGIRIMAN / RESI MARKETPLACE</b>
									<span class="text-muted small">Upload resi / label pengiriman dari marketplace untuk pesanan Anda.</span>
								</div>
								
								<!-- Sisi Tombol Aksi -->
								<div class="col-md-6">
									<div class="row g-2"> <!-- Menggunakan g-2 untuk jarak antar kolom tombol yang lebih rapi -->
										
										<!-- Tombol Upload Label -->
										<div class="col-6 d-grid">
											<button onclick="resimarketplace(<?= $item->id ?>)" class="btn btn-success w-100">
												Upload Label
											</button>
										</div>
										
										<!-- Tombol Lihat Label (Dengan Proteksi Kondisional) -->
										<div class="col-6 d-grid">
											<?php if (!empty($item->resimarketplace)): ?>
												<!-- Aktif jika data resi sudah ada -->
												<button onclick="lihatResi('<?= $item->resimarketplace ?>')" class="btn btn-primary w-100">
													Lihat Label
												</button>
											<?php else: ?>
												<button class="btn btn-secondary w-100" disabled title="Belum ada label yang diunggah">
													Lihat Label
												</button>
											<?php endif; ?>
										</div>

									</div>
								</div>
							</div>
					</div>
			</div>
				<?php } ?>

		</div>
		<div class="d-flex justify-content-center mt-3 pagination-ajax">
				<?= $pager->links('default', 'bootstrap_full'); ?>
		</div>

<?php } else { ?>
    <div class="text-center py-4 mt-3 section">
			<i class="fas fa-box-open text-danger mb-3" style="font-size: 120px;"></i> 
			<h5 class="text-dark fw-bold">TIDAK ADA PESANAN</h5>
		</div>
<?php } ?>


<script type="text/javascript">
		$(document).ready(function(){
			$(".show-product").hide();
			$(".view-product").click(function(){
				$(this).parent().parent().find(".show-product").slideToggle();
				$(this).parent().parent().find(".view-product").toggle();
			});
		});
		function openLink(id){
			window.location.href = id;
		}
	</script>