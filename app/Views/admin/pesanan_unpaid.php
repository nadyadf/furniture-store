<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th scope="col" class="text-center">Tanggal</th>
        <th scope="col">No. Invoice</th>
        <th scope="col">Nama Pembeli</th>
        <th scope="col">Total</th>
        <th scope="col">Kode Bayar</th>
        <th scope="col">Kurir</th>
        <th scope="col" class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($unpaidPayments) && count($unpaidPayments) > 0): ?>
        <?php foreach ($unpaidPayments as $unp): ?>
          <tr>
            <!-- Tanggal & Indicator Status -->
            <td class="text-center">
              <i class="fas fa-circle text-danger me-1 mb-1"></i>
              <br/>
              <small><?= $unp->tgl_format ?></small>
            </td>

            <!-- Invoice & Order ID -->
            <td>
              <div class="mb-1">
                <small class="text-muted">ID Transaksi:</small><br/>
                <span class="fw-bold text-dark"><?= esc($unp->raw_trx->orderid ?? '-') ?></span>
              </div>
              <div>
                <small class="text-muted">No Invoice:</small><br/>
                <span class="fw-bold text-primary"><?= esc($unp->raw_payment->invoice ?? '-') ?></span>
              </div>
            </td>

            <!-- Data Pembeli -->
            <td><?= $unp->pembeli_html ?></td>

            <!-- Total (Total Bayar - Kode Unik + Biaya COD) -->
            <?php 
              $totalBersih = ($unp->raw_payment->total ?? 0) - ($unp->raw_payment->kode_bayar ?? 0) + ($unp->raw_payment->biaya_cod ?? 0);
            ?>
            <td class="fw-bold text-success">
              Rp <?= number_format($totalBersih, 0, ',', '.') ?><br/><small class='text-primary'><?=$unp->metode_nama?></small>
            </td>

            <!-- Kode Unik -->
            <td class="text-muted">
              <?= number_format($unp->raw_payment->kode_bayar ?? 0, 0, ',', '.') ?>
            </td>

            <!-- Gudang & Kurir -->
            <td>
              <small class="text-muted d-block mb-1">
                <i class="fas fa-shipping-fast text-primary me-1"></i> <?= esc($unp->namagudang) ?>
              </small>
              <?= $unp->kurir_html ?>
            </td>

            <!-- Dropdown Aksi BS5 -->
            <td class="text-center" style="min-width: 140px;">
              <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Pilih Aksi
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="konfirm(<?= $unp->raw_payment->id ?>)">
                      <i class="fas fa-check text-success me-2"></i> Verifikasi
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="detail(<?= $unp->trxid ?>)">
                      <i class="fas fa-list text-primary me-2"></i> Detail
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="batalin(<?= $unp->raw_payment->id ?>)">
                      <i class="fas fa-times me-2"></i> Batalkan
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" class="text-center py-4 text-danger">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            Belum ada pesanan yang perlu diverifikasi.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination AJAX CI4 -->
  <?php if (isset($pager)): ?>
    <div class="d-flex justify-content-center mt-4 pagination-ajax">
      <?= $pager ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Bukti Transfer (Bootstrap 5) -->
<div class="modal fade" id="modalbukti" tabindex="-1" aria-labelledby="modalbuktiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalbuktiLabel">Bukti Transfer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img id="bukti" src="<?= base_url('assets/img/no-image.png') ?>" class="img-fluid rounded-bottom" alt="Bukti Transfer" />
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  // Function Konfirmasi Pembayaran
  function konfirm(id) {
    Swal.fire({
      title: "Perhatian!",
      text: "Pastikan uang sudah benar-benar masuk/ditransfer, lebih baik cek kembali mutasi rekening.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Verifikasi",
      cancelButtonText: "Batal",
      customClass: {
        confirmButton: 'btn btn-success me-2',
        cancelButton: 'btn btn-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      loadingDulu();
      if (result.isConfirmed) {
        $.post("<?= site_url("admin/api/updatepesanan") ?>", {
          "id": id,
          "statusbayar": 1,
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
        }, function(e) {
          var data = (typeof e === 'object') ? e : JSON.parse(e);
          if (typeof updateToken === 'function' && data.token) {
            updateToken(data.token);
          }
          if (data.success === true || data.status === 200) {
            Swal.fire("Berhasil!", "Pesanan siap untuk segera dikirim", "success");
            loadPesanan('bayar', 1);
          } else {
            Swal.fire("Gagal!", data.message || "Terjadi kendala saat mengupdate data", "error");
          }
        });
      }
    });
  }

  // Function Pembatalan Pesanan
  function batalin(id) {
    Swal.fire({
      title: "Perhatian!",
      text: "Pesanan akan dibatalkan dan stok akan bertambah kembali.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Batalkan",
      cancelButtonText: "Tidak Jadi",
      customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      loadingDulu();
      if (result.isConfirmed) {
        $.post("<?= site_url('admin/api/batalkanpesanan') ?>", {
          "id": id,
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
        }, function(e) {
          var data = (typeof e === 'object') ? e : JSON.parse(e);
          if (typeof updateToken === 'function' && data.token) {
            updateToken(data.token);
          }
          if (data.success === true || data.status === 200) {
            Swal.fire("Berhasil!", "Pesanan telah dibatalkan", "success");
            loadPesanan('bayar', 1);
          } else {
            Swal.fire("Gagal!", data.message || "Terjadi kendala saat mengupdate data", "error");
          }
        });
      }
    });
  }

  // Function Tampil Bukti Transfer (BS5 Native Modal)
  function bukti(url) {
    $("#bukti").attr("src", url);
    var modalBukti = new bootstrap.Modal(document.getElementById('modalbukti'));
    modalBukti.show();
  }
</script>