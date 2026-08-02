<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<!-- Custom CSS untuk Warna Grafik (Hijau & Biru) -->
<style>
  /* Series A - Penjualan (PCS) -> Hijau Bootstrap */
  #salesChart .ct-series-a .ct-line,
  #salesChart .ct-series-a .ct-point {
    stroke: #198754 !important;
  }
  #salesChart .ct-series-a .ct-area {
    fill: #198754 !important;
    fill-opacity: 0.2;
  }

  /* Series B - Transaksi (Nota) -> Biru Bootstrap */
  #salesChart .ct-series-b .ct-line,
  #salesChart .ct-series-b .ct-point {
    stroke: #0d6efd !important;
  }
  #salesChart .ct-series-b .ct-area {
    fill: #0d6efd !important;
    fill-opacity: 0.2;
  }
</style>

<h4 class="page-title mb-4 fw-bold">Dashboard</h4>

<div class="row g-3 mb-4">
  <!-- Penjualan Hari Ini -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 border-0 shadow-sm border-start border-4 border-success">
      <div class="card-body">
        <h5 class="card-title text-success fw-bold fs-6 pb-2 mb-2 border-bottom">
          <i class="fas fa-dolly-flatbed me-2"></i> Penjualan Hari Ini
        </h5>
        <div class="numbers pt-1">
          <h4 class="card-title fw-bold fs-4 mb-2"><?= number_format($data['jualtoday'], 0, ',', '.') ?> PCS</h4>
          <p class="card-text text-muted mb-1 fs-7"><?= number_format($data['trxtoday'], 0, ',', '.') ?> Transaksi</p>
          <p class="card-text text-dark fw-semibold mb-0">Omset Rp <?= number_format($data['omsettoday'], 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Penjualan Kemarin -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 border-0 shadow-sm border-start border-4 border-info">
      <div class="card-body">
        <h5 class="card-title text-info fw-bold fs-6 pb-2 mb-2 border-bottom">
          <i class="fas fa-history me-2"></i> Penjualan Kemarin
        </h5>
        <div class="numbers pt-1">
          <h4 class="card-title fw-bold fs-4 mb-2"><?= number_format($data['jualkemarin'], 0, ',', '.') ?> PCS</h4>
          <p class="card-text text-muted mb-1 fs-7"><?= number_format($data['trxkemarin'], 0, ',', '.') ?> Transaksi</p>
          <p class="card-text text-dark fw-semibold mb-0">Omset Rp <?= number_format($data['omsetkemarin'], 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat. Bulan Ini -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 border-0 shadow-sm border-start border-4 border-primary">
      <div class="card-body">
        <h5 class="card-title text-primary fw-bold fs-6 pb-2 mb-2 border-bottom">
          <i class="fas fa-calendar-check me-2"></i> Stat. Bulan Ini
        </h5>
        <div class="numbers pt-1">
          <h4 class="card-title fw-bold fs-4 mb-2"><?= number_format($data['jualbulan'], 0, ',', '.') ?> PCS</h4>
          <p class="card-text text-muted mb-1 fs-7"><?= number_format($data['trxbulan'], 0, ',', '.') ?> Transaksi</p>
          <p class="card-text text-dark fw-semibold mb-0">Omset Rp <?= number_format($data['omsetbulan'], 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat. Bulan Lalu -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 border-0 shadow-sm border-start border-4 border-warning">
      <div class="card-body">
        <h5 class="card-title text-warning fw-bold fs-6 pb-2 mb-2 border-bottom">
          <i class="fas fa-calendar-alt me-2"></i> Stat. Bulan Lalu
        </h5>
        <div class="numbers pt-1">
          <h4 class="card-title fw-bold fs-4 mb-2"><?= number_format($data['juallalu'], 0, ',', '.') ?> PCS</h4>
          <p class="card-text text-muted mb-1 fs-7"><?= number_format($data['trxlalu'], 0, ',', '.') ?> Transaksi</p>
          <p class="card-text text-dark fw-semibold mb-0">Omset Rp <?= number_format($data['omsetlalu'], 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Grafik Penjualan -->
<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom-0">
        <h5 class="card-title fw-bold mb-1">Grafik Penjualan (20 hari terakhir)</h5>
        <p class="card-category text-muted mb-0 fs-7">
          <i class="fas fa-square text-success me-1"></i> Penjualan (PCS) &nbsp;&nbsp;
          <i class="fas fa-square text-primary me-1"></i> Transaksi (Nota)
        </p>
      </div>
      <div class="card-body">
        <div id="salesChart" class="chart" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    // Pastikan Chartist sudah ter-load
    if (typeof Chartist === 'undefined') {
      console.error('Chartist.js belum ter-load!');
      return;
    }

    var dataSales = {
      labels: [<?="'" . implode("','", $data['graphtgl']) . "'"?>],
      series: [
        { name: "pcs", data: [<?= implode(",", $data['pcsfix']) ?>] },
        { name: "nota", data: [<?= implode(",", $data['notafix']) ?>] }
      ]
    };

    var optionChartSales = {
      plugins: [
        // Pengecekan aman untuk plugin Tooltip
        Chartist.plugins && Chartist.plugins.tooltip ? Chartist.plugins.tooltip() : null
      ].filter(Boolean),
      series: {
        'pcs': { showArea: true },
        'nota': { showArea: true }
      },
      height: "280px",
      axisX: {
        showGrid: true
      },
      axisY: {
        onlyInteger: true
      }
    };

    // Render Grafik
    if ($('#salesChart').length > 0) {
      Chartist.Line('#salesChart', dataSales, optionChartSales);
    }
  });
</script>

<?= $this->endSection() ?>