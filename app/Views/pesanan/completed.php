<?php if(count($completedOrders) > 0) : ?>
    <div class="pesanan">
			<?php 
				foreach($completedOrders as $order) :
			?>
          <div class="mb-4">
			      <div class="pesanan-item p-4 mx-xl-0">
              <div class="row pb-4">
                <div class="col-6">
                  <span class="text-dark fw-medium fs-5">
                    No. Pesanan <span class="text-success">#<?= $order->orderid; ?></span>
                  </span>
                </div>
                <div class="col-6 text-end">
                  <a href="<?= site_url("manage/detailpesanan/?orderid=").$order->orderid; ?>" class="btn btn-sm btn-primary"><i class="fas fa-angle-double-right"></i> Rincian<span class="hidesmall"> Pesanan</span></a>
                </div>
              </div>
              <div class="row mx-0">
                <div class="col-md-8 px-0 mb-2">
                <?php
                  $totalproduk = 0;
                  $no = 1;
                  if (isset($order->produk_list)):
                    foreach ($order->produk_list as $item):
                      $totalproduk += $item->harga * $item->jumlah;
                      if($no == 2){
                ?>
                <div class="pb-4 show-product">
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
                      <p class="fw-medium text-dark btn-block">
                        <?php if($item->produk != null){ 
                          echo $item->produk->nama; 
                        } else { 
                          echo "Produk telah dihapus"; 
                        } ?>
                      </p>
                      <?php if (!empty($item->variasi_detail) && isset($item->variasi_detail->warna)): ?>
                        <small class='text-primary'>Warna: <?= $item->variasi_detail->warna->nama ?? '-'; ?></small>
                      <?php endif; ?>
                      <p>Rp <?= number_format($item->harga, 0, ',', '.') ?> <span style="font-size:11px">x<?= $item->jumlah; ?></span></p>
                    </div>
                  </div>
                <?php
                    $no++;
                    endforeach;
                    endif;
                  if($no > 2){
                ?>
                </div>
                <div class="row pb-4 mx-0" style="padding-right: 2px;">
                  <a href="javascript:void(0)" class="view-product text-info"><i class="fa fa-chevron-circle-down"></i> Lihat produk lainnya</a>
                  <a href="javascript:void(0)" class="view-product text-info" style="display:none;"><i class='fa fa-chevron-circle-up'></i> Sembunyikan produk</a>
                </div>
              <?php
                }
              ?>
              </div>
              <div class="col-md-4">
                Pesanan telah Anda terima pada<br/>
                <?php
                $timestamp = strtotime($order->selesai);
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
                <i class="text-succes fw-medium" style="padding-right: 8px;"><?= $tanggalIndo; ?> WIB</i>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-4">
                <h5 class="text-dark">Total Order &nbsp;<span class="text-success fw-bold text-end">Rp <?= number_format($order->ongkir + $order->biaya_cod + $totalproduk, 0, ',', '.') ?></span></h5>
              </div>
            </div>
          </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<script type="text/javascript">
	$(document).ready(function(){
		$(".show-product").hide();
		$(".view-product").click(function(){
			$(this).parent().parent().find(".show-product").slideToggle();
			$(this).parent().parent().find(".view-product").toggle();
		});
	});
</script>
