<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<h4 class="page-title mb-4">Pesanan</h4>

<div class="mb-5">
  <div class="card shadow-sm">
    <div class="card-header row align-items-center g-3">
      <!-- Tabs Pesanan -->
      <div class="col-md-8">
        <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="pesananTab">
          <li class="nav-item">
            <a href="javascript:loadBayar(1)" class="nav-link active bayar" data-item="bayar">
              Belum Dibayar
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:loadDikemas(1)" class="nav-link dikemas" data-item="dikemas">
              Perlu Dikirim
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:loadDikirim(1)" class="nav-link dikirim" data-item="dikirim">
              Dikirim
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:loadSelesai(1)" class="nav-link selesai" data-item="selesai">
              Selesai
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:loadBatal(1)" class="nav-link batal" data-item="batal">
              Dibatalkan
            </a>
          </li>
        </ul>
      </div>

      <!-- Input Cari -->
      <div class="col-md-4">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Cari pesanan..." id="cari" />
          <button class="btn btn-info text-white" type="button" id="btn-cari">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Container Content -->
    <div class="card-body" id="load">
      <div class="text-center py-4 text-muted">
        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
        <p class="mb-0">Loading data...</p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>