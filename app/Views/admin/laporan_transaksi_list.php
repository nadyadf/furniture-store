<div class="text-center my-4">
    <h4 class="fw-bold">LAPORAN TRANSAKSI PENJUALAN</h4>
    <?= $headerHtml ?? '' ?>
</div>

<div class="table-responsive">
    <table class="table table-sm table-hover table-bordered align-middle">
        <thead class="table-light text-center">
            <tr>
                <th scope="col" width="5%">No</th>
                <th scope="col">Tanggal</th>
                <th scope="col">ID Transaksi</th>
                <th scope="col">Nama</th>
                <th scope="col">Status</th>
                <th scope="col">Metode Pembayaran</th>
                <th scope="col">Total</th>
                <th scope="col">Ongkir</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                // Cek data transaksi (Mendukung variabel $trx berbentuk Array maupun Object)
                $listData = is_array($trx) ? ($trx['list'] ?? []) : ($trx->list ?? []);
                $totalNominal = is_array($trx) ? ($trx['total_formatted'] ?? 'Rp0') : ($trx->total_formatted ?? 'Rp0');
                $totalOngkir = is_array($trx) ? ($trx['ongkir_formatted'] ?? 'Rp0') : ($trx->ongkir_formatted ?? 'Rp0');
                $rawTotal = is_array($trx) ? ($trx['total'] ?? 0) : ($trx->total ?? 0);
            ?>

            <?php if (!empty($listData) && is_array($listData)) : ?>
                <?php $no = 1; foreach ($listData as $r) : ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= esc($r->tgl_formatted ?? '-') ?></td>
                        <td><?= esc($r->orderid ?? $r->kode ?? '-') ?></td>
                        <td><?= $r->nama_pembeli ?? '-' ?></td>
                        <td><span class="badge bg-secondary"><?= esc($r->status_text ?? '-') ?></span></td>
                        <td><?= $r->metode_text ?? '-' ?></td>
                        <td class="text-end">Rp<?=$r->total_transaksi?></td>
                        <td class="text-end">Rp<?= esc($r->ongkir_formatted ?? 'Rp 0') ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($rawTotal > 0) : ?>
                    <tr class="table-light fw-bold">
                        <td class="text-end" colspan="6">TOTAL</td>
                        <td class="text-end"><?= esc($totalNominal) ?></td>
                        <td class="text-end"><?= esc($totalOngkir) ?></td>
                    </tr>
                <?php endif; ?>

            <?php else : ?>
                <tr>
                    <td colspan="8" class="text-center text-danger py-3">Belum ada data transaksi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>