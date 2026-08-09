<div class="table-responsive">
  <!-- Menambahkan table-sm dan mengecilkan base font tabel ke 0.85rem (13.5px) -->
  <table class="table table-sm table-hover align-middle" style="font-size: 0.85rem;">
    <thead class="table-light">
      <tr>
        <th scope="col" style="width: 15%;">Tanggal</th>
        <th scope="col" style="width: 22%;">No Transaksi</th>
        <th scope="col" style="width: 25%;">Nama Pembeli</th>
        <th scope="col" style="width: 18%;">No Resi</th>
        <th scope="col" style="width: 12%;">Kurir</th>
        <th scope="col" class="text-center" style="width: 8%;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($shippedOrders['data']) && count($shippedOrders['data']) > 0): ?>
        <?php foreach ($shippedOrders['data'] as $r): ?>
          <tr>
            <td>
              <i class="fas fa-shipping-fast text-success me-1"></i>
              <span><?= $r->tgl_formatted; ?></span>
              <?= $r->cod_html; ?>
            </td>
            <td>
              <div class="mb-1">
                <small class="text-muted d-block lh-1" style="font-size: 0.725rem;">ID Transaksi:</small>
                <strong class="text-dark"><?= esc($r->orderid); ?></strong>
              </div>
              <div>
                <small class="text-muted d-block lh-1" style="font-size: 0.725rem;">No Invoice:</small>
                <strong class="text-dark"><?= esc($r->invoice); ?></strong>
              </div>
            </td>
            <td><?= $r->pembeli_html; ?></td>
            <td>
              <!-- Badge No Resi disesuaikan ukurannya -->
              <span class="badge bg-light text-dark border px-2 py-1 fw-semibold" style="font-size: 0.775rem; letter-spacing: 0.3px;">
                <?= esc($r->resi); ?>
              </span>
            </td>
            <td>
              <small class="text-muted d-block mb-1" style="font-size: 0.725rem;">
                <i class="fas fa-warehouse text-primary me-1"></i><?= esc($r->nama_gudang); ?>
              </small>
              <?= $r->kurir_html; ?>
            </td>
            <td class="text-center">
							<div class="dropdown">
								<button type="button" 
												class="btn btn-primary btn-sm dropdown-toggle py-1 px-2" 
												style="font-size: 0.775rem;" 
												data-bs-toggle="dropdown" 
												data-bs-popper-config='{"strategy":"fixed"}' 
												aria-expanded="false">
									Aksi
								</button>
								<ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.825rem;">
									<li>
										<a class="dropdown-item py-1.5" href="javascript:cetak(<?= $r->id; ?>)">
											<i class="fas fa-print text-warning me-2"></i> Invoice
										</a>
									</li>
									<li>
										<a class="dropdown-item py-1.5" href="javascript:detail(<?= $r->id; ?>)">
											<i class="fas fa-list text-primary me-2"></i> Detail
										</a>
									</li>
									<li>
										<a class="dropdown-item py-1.5 text-danger" href="javascript:void(0)" onclick="inputResi(<?= $r->id; ?>)">
											<i class="fas fa-shipping-fast me-2"></i> Update Resi
										</a>
									</li>
									<li>
										<a class="dropdown-item py-1.5" href="javascript:lacakPaket('<?= $r->orderid; ?>')">
											<i class="fas fa-route text-info me-2"></i> Lacak
										</a>
									</li>
									<li><hr class="dropdown-divider my-1"></li>
									<li>
										<a class="dropdown-item py-1.5 text-success fw-bold" href="javascript:selesai(<?= $r->id; ?>)">
											<i class="fas fa-check-circle me-2"></i> Selesai
										</a>
									</li>
								</ul>
							</div>
						</td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-danger py-4">
            <i class="fas fa-box-open fa-2x mb-2 d-block text-muted"></i>
            Belum ada pesanan dikirim
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination AJAX Bootstrap 5 -->
<?php if (!empty($shippedOrders['total']) && $shippedOrders['total'] > 0): ?>
  <?php 
    $totalPages = ceil($shippedOrders['total'] / $shippedOrders['perPage']);
    $currentPage = $shippedOrders['page'];
  ?>
  <?php if ($totalPages > 1): ?>
    <nav class="d-flex justify-content-between align-items-center mt-3 pagination-ajax" style="font-size: 0.8rem;">
      <small class="text-muted">
        Menampilkan <?= count($shippedOrders['data']); ?> dari <?= $shippedOrders['total']; ?> data
      </small>
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?load=dikirim&page=<?= $currentPage - 1; ?>">&laquo; Prev</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= ($i == $currentPage) ? 'active' : ''; ?>">
            <a class="page-link" href="?load=dikirim&page=<?= $i; ?>"><?= $i; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?load=dikirim&page=<?= $currentPage + 1; ?>">Next &raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
	$(function(){
		$("#simpan").on("submit",function(e){
			e.preventDefault();
			var datar = $(this).serialize();
			datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
			$.post("<?=site_url("api/inputresi")?>",datar,function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#modal").modal("hide");
				if(data.success == true){
					swal.fire("Berhasil","Resi telah disimpan","success").then((val)=>{
						loadDikirim(1);
					});
				}else{
					swal.fire("Gagal","Terjadi kesalahan saat menyimpan data, coba ulangi beberapa saat lagi","error");
				}
			});
		});
	});
		
	function inputResi(id){
		$("#theid").val(id);
		$("#modal").modal();
	}
</script>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title"><i class="fas fa-shipping-fast"></i> Input Nomer Resi</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="simpan">
				<input type="hidden" id="theid" name="theid" value="0" />
				<div class="modal-body">
					<div class="form-group">
						<label>Masukkan Nomer Resi</label>
						<input type="text" class="form-control" name="resi" required />
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="submit" class="btn btn-success">Simpan</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
				</div>
			</form>
		</div>
	</div>
</div>