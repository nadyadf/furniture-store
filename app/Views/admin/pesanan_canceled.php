<div class="table-responsive">
  <table class="table table-hover table-sm align-middle mb-0" style="table-layout: fixed; width: 100%; font-size: 0.82rem;">
    <thead>
      <tr class="text-secondary">
        <th scope="col" style="width: 17%;">Tanggal</th>
        <th scope="col" style="width: 20%;">No Transaksi</th>
        <th scope="col" style="width: 25%;">Nama Pembeli</th>
        <th scope="col" style="width: 13%;">Total</th>
        <th scope="col" style="width: 13%;">Kurir</th>
        <th scope="col" style="width: 12%;" class="text-end pe-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php 
      $orders = $cancelledOrders['data'] ?? [];
      $totalRows = $cancelledOrders['total'] ?? 0;

      if (!empty($orders)): 
        foreach ($orders as $r): 
    ?>
      <tr>
        <!-- Tanggal & Icon Batal -->
        <td class="align-top py-2">
          <div class="d-flex align-items-center gap-1 mb-1">
            <i class="fas fa-times-circle text-danger"></i> 
            <span class="fw-bold text-dark"><?=$r->tgl_formatted?></span>
          </div>
          <?=$r->cod_html?>
        </td>

        <!-- ID Transaksi & Invoice -->
        <td class="align-top py-2">
          <div class="mb-1">
            <small class="text-muted d-block" style="font-size: 0.75rem;">ID Transaksi:</small>
            <strong class="text-dark"><?=$r->orderid?></strong>
          </div>
          <div>
            <small class="text-muted d-block" style="font-size: 0.75rem;">No Invoice:</small>
            <span class="text-primary fw-semibold"><?=$r->invoice?></span>
          </div>
        </td>

        <!-- Informasi Pembeli -->
        <td class="align-top py-2">
          <?=$r->pembeli_html?>
        </td>

        <!-- Total Harga -->
        <td class="align-top py-2 text-nowrap fw-bold text-dark">
          Rp <?=number_format($r->pembayaran->total ?? 0, 0, ',', '.')?>
        </td>

        <!-- Gudang & Kurir -->
        <td class="align-top py-2">
          <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">
            <i class="fas fa-shipping-fast text-primary"></i> <?=$r->nama_gudang?>
          </small>
          <?=$r->kurir_html?>
        </td>

        <!-- Tombol Aksi -->
        <td class="align-top py-2 text-end pe-2 text-nowrap">
          <a href="javascript:detail(<?=$r->id?>)" class="btn btn-sm btn-primary py-1 px-2" style="font-size: 0.75rem;">
            <i class="fas fa-list"></i> Detail
          </a>
        </td>
      </tr>
    <?php 
        endforeach; 
      else: 
    ?>
      <tr>
        <td colspan="6" class="text-center text-danger py-3">Belum ada pesanan dibatalkan</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>

  <?php if (isset($pager)): ?>
    <div class="d-flex justify-content-center mt-3 pagination-ajax" style="font-size: 0.8rem;">
      <?= $pager ?>
    </div>
  <?php endif; ?>
</div>