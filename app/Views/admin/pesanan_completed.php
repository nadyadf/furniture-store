<div class="table-responsive">
  <table class="table table-hover table-sm align-middle fs-7" style="font-size: 0.85rem;">
    <thead>
      <tr>
        <th style="width: 18%;">Tanggal</th>
        <th style="width: 22%;">No Transaksi</th>
        <th style="width: 20%;">Nama Pembeli</th>
        <th style="width: 12%;">Total</th>
        <th style="width: 10%;">Total Ongkir</th>
        <th style="width: 10%;">Kurir</th>
        <th style="width: 8%; text-align: center;">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $rows = $completedOrders['data'] ?? [];
      if (!empty($rows)) {
        foreach ($rows as $r) {
    ?>
      <tr>
        <td class="text-center text-nowrap py-2">
          <i class="fas fa-check-circle text-success me-1"></i>
          <span style="font-size: 0.82rem;"><?= $r->tgl_formatted; ?></span>
          <?= $r->cod_html; ?>
        </td>
        <td class="py-2">
          <div class="mb-1">
            <small class="text-muted d-block" style="font-size: 10px; line-height: 1;">ID Transaksi:</small>
            <span class="fw-bold" style="font-size: 0.82rem;"><?= $r->orderid ?></span>
          </div>
          <div>
            <small class="text-muted d-block" style="font-size: 10px; line-height: 1;">No Invoice:</small>
            <span class="fw-bold text-primary" style="font-size: 0.82rem;"><?= $r->invoice ?></span>
          </div>
        </td>
        <td class="py-2" style="font-size: 0.82rem;"><?= $r->pembeli_html ?></td>
        <td class="fw-semibold py-2" style="font-size: 0.85rem;">Rp <?= number_format($r->pembayaran->total ?? 0, 0, ',', '.') ?></td>
        <td class="py-2" style="font-size: 0.82rem;">Rp <?= number_format($r->ongkir ?? 0, 0, ',', '.') ?></td>
        <td class="py-2" style="font-size: 0.8rem;">
          <small class="text-muted d-block" style="font-size: 10px;"><i class="fas fa-shipping-fast text-primary me-1"></i> <?= $r->nama_gudang ?></small>
          <?= $r->kurir_html ?>
        </td>
        <td class="text-center py-2" style="min-width: 100px;">
          <div class="dropdown">
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle py-1 px-2" style="font-size: 0.78rem;" data-bs-toggle="dropdown" aria-expanded="false">
              Aksi
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-7">
              <li>
                <a href="javascript:void(0)" onclick="detail(<?= $r->id ?>)" class="dropdown-item py-1 px-3" style="font-size: 0.8rem;">
                  <i class="fas fa-list text-primary me-2"></i> Detail
                </a>
              </li>
              <?php if ($r->kurir != "bayar" && $r->kurir != "toko" && $r->kurir != "cod") { ?>
              <li>
                <a href="javascript:lacakPaket('<?= $r->orderid ?>')" class="dropdown-item py-1 px-3" style="font-size: 0.8rem;">
                  <i class="fas fa-pallet text-primary me-2"></i> Lacak
                </a>
              </li>
              <?php } ?>
            </ul>
          </div>
        </td>
      </tr>
    <?php 
        }
      } else {
        echo "<tr><td colspan='7' class='text-center text-danger py-4' style='font-size: 0.85rem;'>Belum ada pesanan selesai</td></tr>";
      }
    ?>
    </tbody>
  </table>

  <?php if (isset($pager)): ?>
    <div class="d-flex justify-content-center mt-3 pagination-ajax" style="font-size: 0.78rem;">
      <?= $pager ?>
    </div>
  <?php endif; ?>
</div>