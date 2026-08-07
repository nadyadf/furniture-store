<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #<?= $pembayaran->invoice ?? ''; ?></title>

  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style type="text/css">
    body {
      background-color: #fff;
      font-size: 13px;
      color: #212529;
    }

    @media screen {
      .nota {
        max-width: 800px;
        margin: 20px auto;
        padding: 24px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      }
      .invoice-block {
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px dashed #ccc;
      }
      .invoice-block:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
      }
    }

    @media print {
      body {
        background-color: #fff !important;
      }
      .nota {
        max-width: 100% !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
      }
      .invoice-block {
        page-break-after: always;
        break-after: page;
        margin-bottom: 0 !important;
        border-bottom: none !important;
      }
      .invoice-block:last-child {
        page-break-after: auto;
        break-after: auto;
      }
      .bg-dark-print {
        background-color: #212529 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .no-print {
        display: none !important;
      }
    }

    .invoice-title {
      letter-spacing: 2px;
      font-size: 26px;
      font-weight: 700;
    }

    .table-dash {
      border-bottom: 2px dashed #000 !important;
    }
  </style>
</head>
<body onload="setTimeout(function(){ window.print(); setTimeout(function(){ window.close(); }, 1000); }, 1000);">

  <!-- Tombol Akses Cepat (Sembunyi saat diprint) -->
  <div class="no-print text-center my-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
      <i class="fas fa-print me-1"></i> Cetak Invoice
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
      <i class="fas fa-times me-1"></i> Tutup
    </button>
  </div>

  <div class="nota">
    <!-- LOOPING DIMULAI DARI HEADER INVOICE -->
    <?php foreach ($list_transaksi as $itemTrx): ?>
      <?php $trx = $itemTrx['detail']; ?>

      <div class="invoice-block">
        <!-- 1. Header Invoice -->
        <div class="row align-items-center mb-4">
          <div class="col-6">
            <img src="<?= base_url('cdn/assets/img/' . ($pengaturan->logo ?? 'logo.png')); ?>" alt="Logo" style="max-height: 48px;" class="img-fluid" />
          </div>
          <div class="col-6 text-end text-uppercase text-dark invoice-title">
            INVOICE
          </div>
        </div>

        <!-- 2. Info Detail Transaksi -->
        <div class="row mb-4">
          <div class="col-8">
            <table class="table table-borderless table-sm m-0">
              <tr>
                <td style="width: 140px;">No. Invoice</td>
                <th class="text-dark">: <?= $pembayaran->invoice ?? '-'; ?></th>
              </tr>
              <tr>
                <td>No. Transaksi</td>
                <th class="text-dark">: #<?= $trx->orderid ?? $trx->id; ?></th>
              </tr>
              <tr>
                <td>Pembeli</td>
                <th class="text-dark">: <?= strtoupper($user->nama ?? 'Tamu') . $kontak; ?></th>
              </tr>
              <tr>
                <td>Tanggal Pembelian</td>
                <th class="text-dark">: <?= $trx->tgl_formatted ?? '-'; ?></th>
              </tr>
            </table>
          </div>
        </div>

        <!-- 3. Tabel Produk Transaksi -->
        <div class="table-responsive mb-3">
          <table class="table table-sm align-middle">
            <thead>
              <tr class="bg-dark text-white bg-dark-print text-uppercase">
                <th style="width: 15%;">Kode</th>
                <th>Nama Produk</th>
                <th class="text-center" style="width: 10%;">QTY</th>
                <th class="text-end" style="width: 20%;">Harga Satuan</th>
                <th class="text-end" style="width: 20%;">Total Harga</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($itemTrx['produk_list'] as $item): ?>
                <tr>
                  <td><code><?= $item['kode']; ?></code></td>
                  <td>
                    <span class="fw-semibold"><?= $item['nama_produk']; ?></span>
                    <?php if (!empty($item['variasi'])): ?>
                      <br/><small class="text-muted"><?= $item['variasi']; ?></small>
                    <?php endif; ?>
                  </td>
                  <td class="text-center fw-semibold"><?= $item['jumlah']; ?></td>
                  <td class="text-end">Rp <?= $item['harga_formatted']; ?></td>
                  <td class="text-end fw-semibold">Rp <?= $item['subtotal_formatted']; ?></td>
                </tr>
              <?php endforeach; ?>

              <!-- Subtotal Produk -->
              <tr class="border-top">
                <th colspan="2"></th>
                <th colspan="2" class="text-uppercase">
                  TOTAL HARGA<br/>
                  <small class="text-muted fw-normal">(<?= $itemTrx['total_qty']; ?> BARANG)</small>
                </th>
                <th class="text-end fw-bold">Rp <?= $itemTrx['total_formatted']; ?></th>
              </tr>

              <!-- Keterangan & Rincian Pembayaran -->
              <tr class="border-0">
                <td colspan="2" rowspan="5" class="align-top pe-3" style="width: 50%;">
                  <?php if (!empty($itemTrx['keterangan'])): ?>
                    <div class="p-2 bg-light rounded border mt-1">
                      <strong>KETERANGAN:</strong><br/>
                      <small class="text-muted"><?= $itemTrx['keterangan']; ?></small>
                    </div>
                  <?php endif; ?>
                </td>
                <td colspan="2">Total Ongkir (<?= $itemTrx['berat_kg']; ?>kg)</td>
                <td class="text-end">Rp <?= number_format($trx->ongkir ?? 0, 0, ',', '.'); ?></td>
              </tr>

              <?php if (($trx->biaya_cod ?? 0) > 0): ?>
                <tr class="border-0">
                  <td colspan="2">Biaya COD</td>
                  <td class="text-end">Rp <?= number_format($trx->biaya_cod, 0, ',', '.'); ?></td>
                </tr>
              <?php endif; ?>

              <?php if (($pembayaran->kodebayar ?? 0) > 0): ?>
                <tr class="border-0">
                  <td colspan="2">Kode Bayar</td>
                  <td class="text-end">Rp <?= number_format($pembayaran->kodebayar, 0, ',', '.'); ?></td>
                </tr>
              <?php endif; ?>

              <?php if (($pembayaran->diskon ?? 0) > 0): ?>
                <tr class="border-0">
                  <td colspan="2">Diskon</td>
                  <td class="text-end text-danger">-Rp <?= number_format($pembayaran->diskon, 0, ',', '.'); ?></td>
                </tr>
              <?php endif; ?>

              <!-- Grand Total Transaksi Ini -->
              <tr class="table-dash">
                <th colspan="2" class="fs-6">Grand Total</th>
                <th class="text-end fs-6 text-primary">Rp <?= $itemTrx['grand_total_formatted']; ?></th>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 4. Alamat Pengirim & Penerima (Non-Digital) -->
        <?php
          $gudangName = $trx->gudang_detail->nama ?? '';
          $pengirim   =  ($pengaturan->nama . " - " . $gudangName);
          $nomorHp    = ($trx->gudang_detail->no_hp ?? '');
          $kotaAsal   = $trx->kota_asal ?? '-';
        ?>
        <div class="mt-4 pt-3 border-top">
          <div class="row">
            <!-- Pengirim -->
            <div class="col-6">
              <div class="mb-1 text-muted">Pengirim:</div>
              <strong class="text-dark"><?= $pengirim; ?></strong><br/>
              <span>(<?= $nomorHp; ?>)</span><br/>
              <span><?= $kotaAsal; ?></span>

              <div class="mt-3">
                <div class="mb-1 text-muted">Kurir:</div>
                <strong class="text-dark">
                  <?= strtoupper($trx->nama_kurir ?? '-') . " - " . strtoupper($trx->nama_paket ?? '-'); ?>
                </strong>
              </div>
            </div>

            <!-- Penerima -->
            <div class="col-6">
              <div class="mb-1 text-muted">Alamat Pengiriman:</div>
              <strong class="text-dark"><?= strtoupper($itemTrx['alamat']->nama ?? $user->nama ?? ''); ?></strong> 
              (<?= $itemTrx['alamat']->nohp ?? $user->no_hp ?? ''; ?>)<br/>
              <div class="text-muted mt-1">
                <?= $itemTrx['alamat']->alamat ?? ''; ?><br/>
                <?= $itemTrx['alamat_lengkap']; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. Footer Nota -->
        <div class="footer text-muted text-center pt-4 mt-3 border-top">
          Invoice ini sah dan diproses oleh komputer.<br/>
          Silakan hubungi <strong>Admin <?= ucwords($pengaturan->nama ?? 'Toko'); ?></strong> apabila kamu membutuhkan bantuan.
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>