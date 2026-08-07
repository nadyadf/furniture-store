<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Label Pengiriman</title>

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

    .label-container {
      max-width: 650px;
      margin: 20px auto;
    }

    .label-box {
      border: 2px dashed #000;
      padding: 24px;
      background-color: #fff;
    }

    .logo-img {
      max-height: 48px;
      max-width: 100%;
      object-fit: contain;
    }

    .produk-box {
      max-width: 650px;
      margin: 20px auto 40px auto;
    }

    @media print {
      body {
        background-color: #fff !important;
      }
      .label-container, .produk-box {
        max-width: 100% !important;
        margin: 0 0 20px 0 !important;
      }
      .label-box {
        border: 2px dashed #000 !important;
        page-break-inside: avoid;
      }
      .no-print {
        display: none !important;
      }
      .page-break {
        page-break-after: always;
        break-after: page;
      }
    }
  </style>
</head>
<body onload="setTimeout(function(){ window.print(); setTimeout(function(){ window.close(); }, 1000); }, 1000);">

  <!-- Tombol Akses Cepat (Tersembunyi saat diprint) -->
  <div class="no-print text-center my-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
      <i class="fas fa-print me-1"></i> Cetak Label
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
      <i class="fas fa-times me-1"></i> Tutup
    </button>
  </div>

  <div class="container-fluid">
    <?php if (!empty($list_transaksi)): ?>
      <?php foreach ($list_transaksi as $index => $itemTrx): ?>
        <?php 
          $trx = $itemTrx['detail']; 
          $hasResiMarketplace = !empty($trx->resimarketplace);
        ?>

        <div class="label-container <?= count($list_transaksi) > 1 ? 'page-break' : ''; ?>">
          <?php if ($hasResiMarketplace): ?>
            <!-- JIKA MENGGUNAKAN RESI MARKETPLACE (SHOPEE, TOKOPEDIA, DLL) -->
            <?php if (strpos($trx->resimarketplace, 'pdf') !== false): ?>
              <script type="text/javascript">
                window.location.href = "<?= $trx->resimarketplace; ?>";
              </script>
            <?php else: ?>
              <div class="text-center">
                <img src="<?= $trx->resimarketplace; ?>" class="img-fluid w-100" alt="Resi Marketplace" />
              </div>
            <?php endif; ?>

          <?php else: ?>
            <!-- LABEL PENGIRIMAN STANDAR -->
            <div class="label-box">
              <!-- Header Logo Toko & Kurir -->
              <div class="row align-items-center mb-3">
                <div class="col-6">
                  <img src="<?= base_url('cdn/assets/img/' . ($pengaturan->logo ?? 'logo.png')); ?>" class="logo-img" alt="Logo Toko" />
                </div>
                <div class="col-6 text-end">
                  <?php 
                    $courierCode = strtolower($trx->rajaongkir ?? $trx->kurir ?? '');
                    $courierLogoPath = "assets/img/kurir/" . $courierCode . ".png";
                  ?>
                  <?php if (!empty($courierCode) && file_exists(FCPATH . $courierLogoPath)): ?>
                    <img src="<?= base_url($courierLogoPath); ?>" class="logo-img" alt="Logo Kurir" />
                  <?php endif; ?>
                </div>
              </div>

              <hr class="my-2" />

              <!-- Info Kurir & Paket -->
              <div class="py-2 fs-4 fw-bold text-center text-danger text-uppercase">
                <?= strtoupper($trx->nama_kurir ?? '-') . " - " . strtoupper($trx->nama_paket ?? '-'); ?>
              </div>

              <!-- Status COD -->
              <?php if (($trx->cod ?? 0) == 1 || ($trx->biaya_cod ?? 0) > 0): ?>
                <div class="pb-2 fs-5 fw-bold text-center text-dark">
                  BAYAR DI TEMPAT (COD)
                </div>
              <?php endif; ?>

              <hr class="my-2" />

              <!-- Detail Pengirim & Penerima -->
              <?php
                $gudangName = $trx->gudang_detail->nama ?? '';
                $pengirim   = !empty($trx->dropship) 
                  ? strtoupper($trx->dropship) 
                  : (!empty($gudangName) ? ($pengaturan->nama . " - " . $gudangName) : ($pengaturan->nama ?? 'Toko Kami'));
                $nomorHp    = $trx->gudang_detail->no_hp ?? $pengaturan->notelp ?? '-';
                $kotaAsal   = $trx->kota_asal ?? '-';
                $alamatObj  = $itemTrx['alamat'];
              ?>
              <div class="row pt-3">
                <!-- Data Pengirim -->
                <div class="col-6 pe-3">
                  <div class="mb-2 text-muted">Dari:</div>
                  <strong class="text-dark fs-6"><?= $pengirim; ?></strong><br/>
                  <span><?= $kotaAsal; ?></span><br/>
                  <span>Telp. <strong><?= $nomorHp; ?></strong></span>
                </div>

                <!-- Data Penerima -->
                <div class="col-6 ps-3 border-start">
                  <div class="mb-2 text-muted">Kepada:</div>
                  <strong class="text-dark fs-6"><?= strtoupper($alamatObj->nama ?? $user->nama ?? 'Penerima'); ?></strong><br/>
                  <div class="text-muted mb-1">
                    <?= $itemTrx['alamat_lengkap']; ?>
                  </div>
                  <span>Telp. <strong><?= $user->no_hp ?? '-'; ?></strong></span>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- TABEL RINCIAN PRODUK (Tampil jika bukan resi marketplace) -->
        <?php if (!$hasResiMarketplace): ?>
          <div class="produk-box">
            <div class="fw-bold mb-2 text-secondary">Rincian Barang (Order #<?= $trx->orderid ?? $trx->id; ?>):</div>
            <table class="table table-sm table-bordered align-middle fs-6">
              <thead class="table-light">
                <tr>
                  <th style="width: 5%;" class="text-center">#</th>
                  <th>Nama Produk</th>
                  <th style="width: 20%;">SKU / Kode</th>
                  <th style="width: 25%;">Variasi</th>
                  <th style="width: 10%;" class="text-center">Qty</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($itemTrx['produk_list'])): ?>
                  <?php foreach ($itemTrx['produk_list'] as $no => $prod): ?>
                    <tr>
                      <td class="text-center"><?= $no + 1; ?></td>
                      <td class="fw-semibold"><?= $prod['nama_produk']; ?></td>
                      <td><code><?= $prod['kode']; ?></code></td>
                      <td><small class="text-muted"><?= !empty($prod['variasi']) ? $prod['variasi'] : '-'; ?></small></td>
                      <td class="text-center fw-bold"><?= $prod['jumlah']; ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada item produk.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>