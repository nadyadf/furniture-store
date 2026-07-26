<?php
  $isExist = (!empty($alamat) && $alamat[0]->id !== null);
  $totalAlamat = $isExist ? count($alamat) : 0;

  if ($totalAlamat <= 10) {
?>
<div class="row mt-4 mb-3 align-items-center">
  <div class="col-md-6 hidesmall fw-bold text-primary mb-2 mb-md-0">
    <h4 class="fw-bold mb-0">Daftar Alamat</h4>
  </div>
  <div class="col-md-6 text-md-end">
    <a href="javascript:tambahAlamat();" class="btn btn-success">
      <i class="fas fa-plus me-1"></i> Tambah Alamat
    </a>
  </div>
</div>
<?php
  }
?>

<div class="card border-0 shadow-sm p-3 p-md-4 mb-4">
  <div class="table-responsive">
    <table class="table table-hover table-bordered table-striped align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-3" style="width: 20%;">#</th>
          <th>Nama Penerima</th>
          <th>No Handphone</th>
          <th>Alamat</th>
          <th class="text-center" style="width: 12%;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if ($isExist) {
            $no = 1;
            foreach ($alamat as $al) {
        ?>
        <tr>
          <td class="ps-3">
            <p class="fw-semibold mb-1"><?php echo $al->judul; ?></p>
            <?php if ($al->status == 1) { echo '<span class="badge bg-warning text-dark">Alamat Utama</span>'; } ?>
          </td>
          <td>
            <p class="mb-0"><?php echo $al->nama; ?></p>
          </td>
          <td>
            <p class="mb-0"><?php echo $al->no_hp; ?></p>
          </td>
          <td>
            <p class="mb-0">
              <?php echo $al->alamat; ?><br/>
              <small class="text-muted">Kodepos <?php echo $al->kodepos; ?></small>
            </p>
          </td>
          <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
              <a href="javascript:editAlamat(<?php echo $al->id; ?>)" class="btn btn-success btn-sm" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
              <a href="javascript:hapusAlamat(<?php echo $al->id; ?>)" class="btn btn-danger btn-sm" title="Hapus">
                <i class="fas fa-trash-alt"></i>
              </a>
            </div>
          </td>
        </tr>
        <?php
              $no++;
            }
          } else {
        ?>
        <tr>
          <td class="p-4 text-center" colspan="5">
            <p class="mb-0 text-muted">
              <i class="fas fa-exclamation-triangle text-warning me-2 fs-5"></i>
              Belum ada daftar alamat, silahkan tambah data pengiriman pesanan.
            </p>
          </td>
        </tr>
        <?php
          }
        ?>
      </tbody>
    </table>
  </div>
</div>