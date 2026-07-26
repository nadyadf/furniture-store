<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice</title>
  
  <!-- Bootstrap 5 CSS CDN (Memastikan CSS pasti ter-load) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style type="text/css">
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      color: #212529;
      background-color: #fff;
    }
    .nota {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }
    .logo img {
      max-height: 50px;
      width: auto;
    }
    .invoice-title {
      letter-spacing: 2px;
      font-size: 28px;
      font-weight: 700;
      text-align: right;
    }
    
    /* Layout Tabel & Border Custom */
    .table-invoice th, .table-invoice td {
      padding: 8px 12px;
      vertical-align: middle;
    }
    .table-noborder td, .table-noborder th {
      border: none !important;
      padding: 4px 12px;
    }
    .tr-dash th, .tr-dash td {
      border-top: 2px dashed #333 !important;
      border-bottom: none !important;
    }

    /* Pengaturan Khusus Cetak */
    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      body {
        background: #fff;
      }
      .nota {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .bg-dark {
        background-color: #212529 !important;
        color: #fff !important;
      }
      .footer-print {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        font-size: 9pt;
      }
    }
  </style>
</head>
<body onload="setTimeout(function(){window.print();setTimeout(function(){window.close();},1000);},1000);">

  <div class="nota">
    <?php
      foreach($transaksi as $trx){
        $lkp = $trx->alamat->nama_kecamatan." ".$trx->alamat->nama_kabupaten." ".$trx->alamat->nama_provinsi." ".$trx->alamat->kodepos;
        $kontak = isset($trx->user->username) ? $trx->user->username : '-';
        $kontak = ($trx->user->no_hp != "") ? $trx->user->no_hp : $kontak;
        $kontak = " (".$kontak.")";
    ?>
      
      <!-- Header Logo & Invoice -->
      <div class="row align-items-center mb-4">
        <div class="col-6 logo">
          <img src="<?=base_url("cdn/assets/img/".$set->logo)?>" alt="Logo" />
        </div>
        <div class="col-6 invoice-title">
          INVOICE
        </div>
      </div>

      <!-- Info Transaksi -->
      <div class="row mb-4">
        <div class="col-8">
          <table class="table-noborder w-100">
            <tr>
              <td style="width: 150px;">No. Invoice</td>
              <td><strong>: <?=$trx->pembayaran->invoice?></strong></td>
            </tr>
            <tr>
              <td>No. Transaksi</td>
              <td><strong>: #<?=$trx->orderid?></strong></td>
            </tr>
            <tr>
              <td>Pembeli</td>
              <td><strong>: <?=strtoupper(strtolower($trx->user->nama)).$kontak?></strong></td>
            </tr>
            <tr>
              <td>Tanggal Pembelian</td>
              <td><strong>: 
              <?php 
              if (!empty($trx->tgl) && $trx->tgl != '0000-00-00 00:00:00') {
                  $date = new DateTime($trx->tgl, new DateTimeZone('Asia/Jakarta'));
                  $formatter = new IntlDateFormatter(
                      'id_ID', 
                      IntlDateFormatter::FULL, 
                      IntlDateFormatter::NONE, 
                      'Asia/Jakarta', 
                      IntlDateFormatter::GREGORIAN, 
                      "EEE, d MMM yyyy"
                  );
                  echo $formatter->format($date);
              } else {
                  echo '-';
              }
              ?>
              </strong></td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Tabel Produk & Perhitungan -->
      <div class="mb-4">
        <table class="table table-bordered table-invoice align-middle">
          <thead>
            <tr class="bg-dark text-white text-uppercase">
              <th scope="col">Kode</th>
              <th scope="col">Nama Produk</th>
              <th scope="col" class="text-center">QTY</th>
              <th scope="col" class="text-end">Harga Satuan</th>
              <th scope="col" class="text-end">Total Harga</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $total = 0;
              $totalqty = 0;
              $ket = "";
              foreach($trx->produk as $prod){
                $subtotal = $prod->harga * $prod->jumlah;
                $total += $subtotal;
                $kode = !empty($prod) ? $prod->kode : '-';
                $nama = !empty($prod) ? $prod->nama : "Produk dihapus";
                $totalqty += $prod->jumlah;
                if(!empty($prod->keterangan)) {
                  $ket .= $prod->keterangan."<br/>";
                }
                $variasi = $prod->nama_warna;
                echo "
                  <tr>
                    <td>".$kode."</td>
                    <td>".$nama."<br/><small class=\"text-muted\">".$variasi."</small></td>
                    <td class=\"text-center\">".$prod->jumlah."</td>
                    <td class=\"text-end\">Rp".number_format($prod->harga, 0, ',', '.')."</td>
                    <td class=\"text-end\">Rp".number_format($subtotal, 0, ',', '.')."</td>
                  </tr>
                ";
              }
              $beratkg = round($trx->berat/1000, 2, PHP_ROUND_HALF_UP);
            ?>
            <tr class="fw-bold">
              <td colspan="2"></td>
              <td colspan="2">TOTAL HARGA <small class="fw-normal">(<?=$totalqty?> BARANG)</small></td>
              <td class="text-end">Rp<?=number_format($total, 0, ',', '.');?></td>
            </tr>
            
            <!-- Rincian Tambahan -->
            <tr class="table-noborder">
              <td colspan="2" rowspan="5" class="align-top">
                <?php if(!empty($ket)){ ?>
                  <strong>KETERANGAN:</strong><br/>
                  <small class="text-muted"><?=$ket?></small>
                <?php } ?>
              </td>
              <td colspan="2">Total Ongkir (<?=$beratkg?>kg)</td>
              <td class="text-end">Rp<?=number_format($trx->ongkir, 0, ',', '.')?></td>
            </tr>
            <?php if($trx->pembayaran->biaya_cod > 0){ ?>
            <tr class="table-noborder">
              <td colspan="2">Biaya COD</td>
              <td class="text-end">Rp<?=number_format($trx->biaya_cod, 0, ',', '.')?></td>
            </tr>
            <?php } ?>
            <?php if($trx->pembayaran->kode_bayar > 0){ ?>
            <tr class="table-noborder">
              <td colspan="2">Kode Bayar</td>
              <td class="text-end">Rp<?=number_format($trx->pembayaran->kode_bayar, 0, ',', '.')?></td>
            </tr>
            <?php } ?>
            <tr class="tr-dash fw-bold">
              <td colspan="2">Grand Total</td>
              <td class="text-end">Rp<?=number_format($total+$trx->biaya_cod+$trx->ongkir+$trx->pembayaran->kode_bayar, 0, ',', '.')?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Detail Pengirim & Alamat -->
      <?php 
        $pengirim = ($trx->gudang > 0) ? $set->nama." - ".$trx->gudang_detail->nama : $set->nama;
        $nomor = ($trx->gudang > 0) ? $trx->gudang_detail->no_hp : $set->no_telp;
        $kota = $trx->kota_asal;
      ?>
      <div class="pt-3 border-top mb-5">
        <div class="row">
          <div class="col-6">
            <div class="mb-1 text-muted">Pengirim:</div>
            <strong><?=strtoupper(strtolower($pengirim))?></strong><br/>
            (<?=$nomor?>)<br/>
            <?=$kota?>
            
            <div class="mt-3">
              <div class="mb-1 text-muted">Kurir:</div>
              <strong><?=$trx->nama_kurir." - ".$trx->nama_paket?></strong>
            </div>
          </div>
          <div class="col-6">
            <div class="mb-1 text-muted">Alamat Pengiriman:</div>
            <strong><?=strtoupper(strtolower($trx->alamat->nama))?></strong> (<?=$trx->alamat->no_hp?>)<br/>
            <?=$trx->alamat->alamat."<br/>".$lkp?>
          </div>
        </div>
      </div>

      <!-- Footer Nota -->
      <div class="footer-print text-muted mt-5">
        Invoice ini sah dan diproses oleh komputer<br/>
        Silakan hubungi <strong>Admin <?=ucwords($set->nama)?></strong> apabila kamu membutuhkan bantuan.
      </div>
    <?php
      }
    ?>
  </div>

</body>
</html>