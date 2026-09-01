<?= $this->extend('admin/main') ?>

<?= $this->section('content') ?>

<!-- Header Halaman Flexbox -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <?php if ($id == 0 && !isset($_GET['copy'])): ?>
    <h5 class="fw-bold m-0" style="font-size: 16px;">Tambah Produk Baru</h5>
  <?php else: ?>
    <?php if (isset($_GET['copy'])): ?>
      <h5 class="fw-bold m-0" style="font-size: 16px;">Copy Produk</h5>
    <?php else: ?>
      <h5 class="fw-bold m-0" style="font-size: 16px;">Edit Produk</h5>
    <?php endif; ?>
  <?php endif; ?>

  <a href="javascript:history.back()" class="btn btn-danger btn-sm" style="font-size: 12px;">
    <i class="la la-times"></i> Batal
  </a>
</div>

<!-- Section Foto Produk -->
<div class="card mb-3">
  <div class="card-header bg-white py-2">
    <div class="card-title fw-semibold m-0" style="font-size: 13px;">Foto Produk</div>
  </div>
  <div class="card-body py-3">
    <div class="mb-2 overflow-hidden">
      <div id="foto-produk" class="uploadfoto-result"></div>
      <div class="uploadfoto">
        <label class="form-uploadfoto">
          <input type="file" name="fotoProduk" id="fotoUpload" accept="image/x-png,image/gif,image/jpeg">
          <img src="<?= base_url('cdn/assets/img/add-product.png') ?>" alt="Add Product" />
        </label>
        <span id="prosesUpload"></span>
      </div>
    </div>
    <div class="text-danger">
      <small style="font-size: 11px;"><i>Ukuran file maksimal 2MB, resolusi maksimal 2000 pixel</i></small>
    </div>
  </div>
</div>

<!-- Form Produk -->
<form id="produk" action="" method="POST" >
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= ($id > 0 && !isset($_GET['copy'])) ? $id : 0 ?>" />

  <!-- Informasi Dasar -->
  <div class="card mb-3">
    <div class="card-header bg-white py-2">
      <div class="card-title fw-semibold m-0" style="font-size: 13px;">Nama & Kategori Produk</div>
    </div>
    <div class="card-body py-3">
      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Nama Produk</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Nama min. 5 kata, terdiri dari jenis produk, merek, dan keterangan seperti warna, bahan, atau tipe.</div>
        </div>
        <div class="col-md-8">
          <input type="text" class="form-control form-control-sm" name="nama" value="<?= ($id != 0 && isset($produk->nama)) ? esc($produk->nama) : '' ?>" required />
        </div>
      </div>

      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Kode Produk</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Masukkan kode unik untuk produk dan pastikan tidak sama dengan produk yang lain.</div>
        </div>
        <div class="col-md-8">
          <input type="text" class="form-control form-control-sm" name="kode" value="<?= ($id != 0 && !isset($_GET['copy']) && isset($produk->kode)) ? esc($produk->kode) : date('dHis') ?>" required />
        </div>
      </div>


      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Kategori</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Pilih kategori yang sesuai dengan produk.</div>
        </div>
        <div class="col-md-6">
          <select class="select2 form-select form-select-sm" name="idcat" required style="font-size: 12px;">
            <option value="">- Pilih Kategori -</option>
            <?php if (!empty($kategori)): ?>
              <?php foreach ($kategori as $k): ?>
                <?php $selec = ($id != 0 && isset($produk->idcat) && $produk->idcat == $k->id) ? 'selected' : ''; ?>
                <option value="<?= $k->id ?>" <?= $selec ?>><?= esc($k->nama) ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Harga & Stok -->
  <div class="card mb-3">
    <div class="card-header bg-white py-2">
      <div class="card-title fw-semibold m-0" style="font-size: 13px;">Detail Harga & Stok</div>
    </div>
    <div class="card-body py-3">
      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Gudang Pengiriman</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Pilih gudang tempat asal pengiriman produk.</div>
        </div>
        <div class="col-md-8">
          <select class="select2 form-select form-select-sm" name="gudang" required style="font-size: 12px;">
            <option value="">- Pilih Gudang Pengiriman -</option>
            <option value="0" <?= ($id == 0 || (isset($produk->gudang) && $produk->gudang == 0)) ? 'selected' : '' ?>>
              PUSAT - <?= esc(($kabs->tipe ?? '') . ' ' . ($kabs->nama ?? '')) ?>
            </option>
            <?php if (!empty($gudang)): ?>
              <?php foreach ($gudang as $g): ?>
                <?php $selec = ($id != 0 && isset($produk->gudang) && $produk->gudang == $g->id) ? 'selected' : ''; ?>
                <option value="<?= $g->id ?>" <?= $selec ?>><?= esc($g->nama) ?> - <?= esc($g->namakota) ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <div class="row mb-3 align-items-center novariasi">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Stok Barang</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Hanya masukkan angka saja. Contoh: 200</div>
        </div>
        <div class="col-md-3 col-6">
          <input type="number" class="form-control form-control-sm" id="stok" name="stok" value="<?= ($id != 0 && isset($produk->stok)) ? $produk->stok : 0 ?>" required <?= ($varjum > 0) ? 'readonly' : '' ?> />
        </div>
        <?php if ($varjum > 0): ?>
          <div class="col-md-3 col-6">
            <span class="text-danger" style="font-size: 11px;">Atur stok di variasi produk</span>
          </div>
        <?php endif; ?>
      </div>

      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Minimal Order</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Jumlah produk minimal setiap order.</div>
        </div>
        <div class="col-md-3 col-6">
          <input type="number" class="form-control form-control-sm" name="min_order" value="<?= ($id != 0 && isset($produk->min_order)) ? $produk->min_order : 1 ?>" required />
        </div>
      </div>

      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Harga Coret</b></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Harga normal sebelum diskon (angka saja). Contoh: 200000</div>
        </div>
        <div class="col-md-3 col-6">
          <input type="number" class="form-control form-control-sm" name="harga_coret" value="<?= ($id != 0 && isset($produk->harga_coret)) ? $produk->harga_coret : 0 ?>" required />
        </div>
      </div>

      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Harga Normal</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Harga normal setelah diskon (angka saja). Contoh: 200000</div>
        </div>
        <div class="col-md-3 col-6">
          <input type="number" class="form-control form-control-sm" name="harga" value="<?= ($id != 0 && isset($produk->harga)) ? $produk->harga : 0 ?>" required />
        </div>
      </div>

      <!-- Toggle Produk Unggulan -->
      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Produk Unggulan</b></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Tampilkan produk ini di section produk unggulan pada halaman utama.</div>
        </div>
        <div class="col-md-8">
          <div class="form-check form-switch">
            <input type="hidden" name="is_unggulan" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="featuredToggle" name="is_unggulan" value="1" <?= (isset($produk->is_unggulan) && $produk->is_unggulan == 1) ? 'checked' : '' ?> style="cursor: pointer; width: 2.5em; height: 1.25em;">
            <label class="form-check-label ms-2" for="featuredToggle" style="font-size: 12px;">Ya, jadikan produk unggulan</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Spesifikasi Produk (Dynamic Key-Value) -->
<div class="card mb-3">
  <div class="card-header bg-white py-2">
    <div class="card-title fw-semibold m-0" style="font-size: 13px;">Spesifikasi Produk</div>
  </div>
  <div class="card-body py-3">
    <div id="wrapper-spesifikasi">
      <?php 
        $spesifikasiData = [];
        if ($id != 0 && !empty($produk->spesifikasi)) {
            // 1. Pecah string berdasarkan separator '|'
            $items = explode('|', $produk->spesifikasi);
            foreach ($items as $item) {
                // 2. Pecah tiap item berdasarkan ':'
                $parts = explode(':', $item, 2);
                if (count($parts) === 2) {
                    $spesifikasiData[trim($parts[0])] = trim($parts[1]);
                }
            }
        }
        
        if (empty($spesifikasiData)) {
            $spesifikasiData = ['' => ''];
        }
        
        $i = 0;
        foreach ($spesifikasiData as $key => $val): 
      ?>
        <div class="row mb-2 align-items-center item-row-spek">
          <div class="col-md-5 col-5">
            <input type="text" class="form-control form-control-sm" name="spek_key[]" placeholder="Nama Spek (mis: Bentuk)" value="<?= esc($key) ?>">
          </div>
          <div class="col-md-6 col-5">
            <input type="text" class="form-control form-control-sm" name="spek_val[]" placeholder="Nilai (mis: Geometris)" value="<?= esc($val) ?>">
          </div>
          <div class="col-md-1 col-2 text-end action-btn-container">
            <?php if ($i === 0): ?>
              <button type="button" class="btn btn-success btn-sm btn-add-spek" style="font-size: 11px;"><i class="fas fa-plus"></i></button>
            <?php else: ?>
              <button type="button" class="btn btn-danger btn-sm remove-row-spek" style="font-size: 11px;"><i class="fas fa-minus"></i></button>
            <?php endif; ?>
          </div>
        </div>
      <?php 
          $i++;
        endforeach; 
      ?>
    </div>
  </div>
</div>

<!-- Ukuran Produk (Dynamic Key-Value) -->
<div class="card mb-3">
  <div class="card-header bg-white py-2">
    <div class="card-title fw-semibold m-0" style="font-size: 13px;">Detail Ukuran Produk</div>
  </div>
  <div class="card-body py-3">
    <div id="wrapper-ukuran">
      <?php 
        $ukuranData = [];
        if ($id != 0 && !empty($produk->ukuran)) {
            // 1. Pecah string berdasarkan separator '|'
            $items = explode('|', $produk->ukuran);
            foreach ($items as $item) {
                // 2. Pecah tiap item berdasarkan ':'
                $parts = explode(':', $item, 2);
                if (count($parts) === 2) {
                    $ukuranData[trim($parts[0])] = trim($parts[1]);
                }
            }
        }

        if (empty($ukuranData)) {
            $ukuranData = ['' => ''];
        }
        
        $j = 0;
        foreach ($ukuranData as $key => $val): 
      ?>
        <div class="row mb-2 align-items-center item-row-ukuran">
          <div class="col-md-5 col-5">
            <input type="text" class="form-control form-control-sm" name="ukuran_key[]" placeholder="Nama Ukuran (mis: Diameter)" value="<?= esc($key) ?>">
          </div>
          <div class="col-md-6 col-5">
            <input type="text" class="form-control form-control-sm" name="ukuran_val[]" placeholder="Nilai (mis: 50 cm)" value="<?= esc($val) ?>">
          </div>
          <div class="col-md-1 col-2 text-end action-btn-container">
            <?php if ($j === 0): ?>
              <button type="button" class="btn btn-success btn-sm btn-add-ukuran" style="font-size: 11px;"><i class="fas fa-plus"></i></button>
            <?php else: ?>
              <button type="button" class="btn btn-danger btn-sm remove-row-ukuran" style="font-size: 11px;"><i class="fas fa-minus"></i></button>
            <?php endif; ?>
          </div>
        </div>
      <?php 
          $j++;
        endforeach; 
      ?>
    </div>
  </div>
</div>

  <!-- Deskripsi Produk -->
  <div class="card mb-3">
    <div class="card-header bg-white py-2">
      <div class="card-title fw-semibold m-0" style="font-size: 13px;">Deskripsi Produk</div>
    </div>
    <div class="card-body py-3">
      <div class="row mb-3 align-items-center">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Berat Produk (gram)</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Hanya isi angka, misal 1kg maka isi: 1000</div>
        </div>
        <div class="col-md-4 col-6">
          <input type="number" class="form-control form-control-sm" name="berat" value="<?= ($id != 0 && isset($produk->berat)) ? $produk->berat : 250 ?>" required />
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <div class="mb-1" style="font-size: 13px;"><b>Deskripsi Produk</b> &nbsp;<span class="badge bg-danger" style="font-size: 9px;">wajib</span></div>
          <div class="text-muted" style="font-size: 11px; line-height: 1.3;">Masukkan deskripsi lengkap produk agar pembeli lebih mudah mengerti.</div>
        </div>
        <div class="col-md-8">
          <textarea class="form-control form-control-sm" id="deskripsi" name="deskripsi" rows="4"><?= ($id != 0 && isset($produk->deskripsi)) ? esc($produk->deskripsi) : '' ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <!-- Tombol Aksi -->
  <div class="card mb-3">
    <div class="card-body text-end py-2">
      <button type="submit" class="btn btn-primary btn-sm" style="font-size: 12px;"><i class="la la-check-circle"></i> Simpan</button>
      <button type="reset" class="btn btn-warning btn-sm" style="font-size: 12px;"><i class="la la-refresh"></i> Reset</button>
      <button type="button" onclick="history.back()" class="btn btn-danger btn-sm" style="font-size: 12px;"><i class="la la-times"></i> Batal</button>
    </div>
  </div>
</form>

<!-- Section Varian Produk -->
<div class="card mb-3">
  <div class="card-header bg-white py-2">
    <div class="card-title fw-semibold m-0" style="font-size: 13px;" id="stokvariasi">Varian Stok Produk</div>
  </div>
  <div class="card-body py-3">
    <div id="judulvarian" style="display:none;">
      <div class="row mb-3 align-items-center">
        <div class="col-md-3 text-end">
          <b style="font-size: 12px;">Judul Varian</b>
        </div>
        <div class="col-md-3">
          <input class="form-control form-control-sm" name="variasi" id="jdlvariasi" placeholder="cth: Warna" value="<?= ($id != 0 && isset($produk->variasi)) ? esc($produk->variasi) : '' ?>" />
        </div>
      </div>
    </div>

    <div id="loadvar"></div>

    <?php if ($id != 0 && !isset($_GET['copy'])): ?>
      <?php if (!empty($vars)): ?>
        <script type="text/javascript">
          $(function(){
            $("#judulvarian").show();
            loadVariasi();
          });
        </script>
      <?php else: ?>
        <div class="text-center py-3 text-muted" id="notifar" style="font-size: 12px;">
          Belum ada pilihan varian untuk produk ini<br/>&nbsp;<br/>
          <button type="button" class="btn btn-primary btn-sm" style="font-size: 12px;" onclick="$('#judulvarian').show();$('#notifar').hide();loadVariasi()">Aktifkan Varian Produk</button>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="text-center py-3 text-muted" id="notifar" style="font-size: 12px;">
        Untuk membuat varian produk, silahkan simpan dulu produk ini setelah itu <b>edit</b> produk yang telah disimpan.
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/f3q96btvm5j3drd14wf1r7z8ci4f4vxlhs8se07ky596ng4l/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea#deskripsi', // Sesuaikan ID textarea Anda
    height: 350,
    menubar: false,
    plugins: 'lists link code table',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link table | code'
  });
</script>

<script>
$(document).ready(function() {
  // Tambah Baris Spesifikasi
  $(document).on('click', '.btn-add-spek', function() {
    let html = `
      <div class="row mb-2 align-items-center item-row-spek">
        <div class="col-md-5 col-5">
          <input type="text" class="form-control form-control-sm" name="spek_key[]" placeholder="Nama Spek (mis: Material)">
        </div>
        <div class="col-md-6 col-5">
          <input type="text" class="form-control form-control-sm" name="spek_val[]" placeholder="Nilai (mis: Metal finishing gold)">
        </div>
        <div class="col-md-1 col-2 text-end action-btn-container">
          <button type="button" class="btn btn-danger btn-sm remove-row-spek" style="font-size: 11px;"><i class="fas fa-minus"></i></button>
        </div>
      </div>`;
    $('#wrapper-spesifikasi').append(html);
  });

  // Hapus Baris Spesifikasi
  $(document).on('click', '.remove-row-spek', function() {
    $(this).closest('.item-row-spek').remove();
  });

  // Tambah Baris Ukuran
  $(document).on('click', '.btn-add-ukuran', function() {
    let html = `
      <div class="row mb-2 align-items-center item-row-ukuran">
        <div class="col-md-5 col-5">
          <input type="text" class="form-control form-control-sm" name="ukuran_key[]" placeholder="Nama Ukuran (mis: Tinggi)">
        </div>
        <div class="col-md-6 col-5">
          <input type="text" class="form-control form-control-sm" name="ukuran_val[]" placeholder="Nilai (mis: 120 cm)">
        </div>
        <div class="col-md-1 col-2 text-end action-btn-container">
          <button type="button" class="btn btn-danger btn-sm remove-row-ukuran" style="font-size: 11px;"><i class="fas fa-minus"></i></button>
        </div>
      </div>`;
    $('#wrapper-ukuran').append(html);
  });

  // Hapus Baris Ukuran
  $(document).on('click', '.remove-row-ukuran', function() {
    $(this).closest('.item-row-ukuran').remove();
  });

  // 1. Simpan HTML awal saat pertama kali halaman dimuat
    const initialSpekHTML = $('#wrapper-spesifikasi').html();
    const initialUkuranHTML = $('#wrapper-ukuran').html();

    // 2. Tangkap event reset pada form (ganti '#form-produk' sesuai ID/class form kamu)
    $('form').on('reset', function() {
        // Kembalikan struktur HTML ke kondisi awal
        $('#wrapper-spesifikasi').html(initialSpekHTML);
        $('#wrapper-ukuran').html(initialUkuranHTML);
    });
});
</script>

<script type="text/javascript">
  
  $(function(){
    
    $("#variasi").on('click', '.hapusvariasion', function(){
      var therem = $(this).closest(".form-group, .row");
      var varid = $(this).data("varid");
      
      Swal.fire({
        title: "Yakin menghapus variasi?",
        text: "Variasi akan dihapus dan tidak dapat dikembalikan lagi, termasuk stok juga akan habis",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
        customClass: {
          confirmButton: 'btn btn-danger btn-sm me-2',
          cancelButton: 'btn btn-secondary btn-sm'
        },
        buttonsStyling: false
      }).then((result) => {
        if(result.isConfirmed){
          $.post("<?=site_url('admin/hapusvariasi')?>", {"theid": varid, [$("#names").val()]: $("#tokens").val()}, function(e){
            var data = (typeof e === 'object') ? e : JSON.parse(e);
            updateToken(data.token);
            if(data.success == true){
              therem.remove();
              if(!$("#variasi input").val()){
                $("#stok").show();
                $("#belumada").show();
                $(".novariasi").show();
              }
            } else {
              Swal.fire("Gagal", "Gagal menghapus variasi, coba ulangi beberapa saat lagi", "error");
            }
          });
        }
      });
    });
    
    $("#produk").on("submit", function(e){
      e.preventDefault();
      var $btnSubmit = $(this).find("button[type='submit']");
      var suk = $btnSubmit.html();
      
      $btnSubmit.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...').prop("disabled", true);
      
      var datar = $(this).serialize();
      datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val() + "&variasi=" + $("#jdlvariasi").val() + "&subvariasi=" + $("#jdlsubvariasi").val();

      $.post("<?=$url?>", datar, function(msg){
        var data = (typeof msg === 'object') ? msg : JSON.parse(msg);
        updateToken(data.token);
        
        $btnSubmit.html(suk).prop("disabled", false);
        
        if(data.success == true){
          Swal.fire({
            title: "Selesai",
            text: "Data produk telah disimpan",
            icon: "success",
            confirmButtonText: "OK",
            customClass: { confirmButton: 'btn btn-primary btn-sm' },
            buttonsStyling: false
          }).then(() => {
            window.location.href = "<?=site_url("admin/produk")?>";
          });
        } else {
          Swal.fire("Gagal", data.msg, "error");
        }
      });
    });
    
    <?php if($id == 0){ ?>
      hapusFoto("all");
    <?php } ?>
    
    $("#fotoUpload").change(function(){
      var formData = new FormData();
      formData.append("fotoProduk", $("#fotoUpload").get(0).files[0]);
      formData.append("jenis", 1);
      formData.append("idproduk", <?= $id; ?>);
      formData.append($("#names").val(), $("#tokens").val());
      
      $.ajax({
        url: '<?= site_url("admin/api/uploadFotoProduk"); ?>',
        type: 'POST',
        contentType: false,
        cache: false,
        processData: false,
        data: formData,
        xhr: function () {
          var jqXHR = $.ajaxSettings.xhr();
          if (jqXHR.upload) {
            jqXHR.upload.addEventListener("progress", function (evt) {
              if (evt.lengthComputable) {
                var percentComplete = Math.round((evt.loaded * 100) / evt.total);
                $("#prosesUpload").html("<small class='text-muted ms-2'>Mengunggah: " + percentComplete + "%</small>");
              }
            }, false);
          }
          return jqXHR;
        },
        success: function (data) {
          var datas = (typeof data === 'object') ? data : JSON.parse(data);
          updateToken(datas.token);
          $("#prosesUpload").html("");
          loadResult();
        }
      });
    });
    loadResult();
  });
  

  function loadResult(){
    $("#foto-produk").html("<small class='text-muted'>Mohon tunggu sebentar...</small>");
    $.post('<?php echo site_url("admin/api/uploadFotoResult/".$id); ?>', {"response": 212, [$("#names").val()]: $("#tokens").val()}, function(msg){
      console.log('oke');
      var data = (typeof msg === 'object') ? msg : JSON.parse(msg);
      updateToken(data.token);
      if(data.success == true){
        $("#foto-produk").html(data.data);
      }
    });
  }

  function loadVariasi(){
    $("#loadvar").html("<small class='text-muted'>Mohon tunggu sebentar...</small>");
    $.post('<?php echo site_url("admin/api/variasiform/".$id); ?>', {"response": 212, [$("#names").val()]: $("#tokens").val()}, function(msg){
      var data = (typeof msg === 'object') ? msg : JSON.parse(msg);
      updateToken(data.token);
      if(data.success == true){
        $("#loadvar").html(data.data);
      } else {
        $("#loadvar").html("<span class='text-danger fs-12'>Gagal memuat variasi</span>");
      }
    });
  }

  function hapusFoto(id){
    if(id != "all"){
      Swal.fire({
        title: "Yakin menghapus foto?",
        text: "Data yang sudah dihapus tidak dapat dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
        customClass: {
          confirmButton: 'btn btn-danger btn-sm me-2',
          cancelButton: 'btn btn-secondary btn-sm'
        },
        buttonsStyling: false
      }).then((result) => {
        if(result.isConfirmed){
          $.post('<?php echo site_url("admin/api/hapusFotoProduk/"); ?>' + id, {"response": 212, [$("#names").val()]: $("#tokens").val()}, function(msg){
            var data = (typeof msg === 'object') ? msg : JSON.parse(msg);
            updateToken(data.token);
            if(data.success == true){
              loadResult();
            } else {
              Swal.fire("GAGAL", "Gagal menghapus data", "error");
            }
          });
        }
      });
    } else {
      $.post('<?php echo site_url("admin/api/hapusFotoProduk/"); ?>' + id, {"response": 212, [$("#names").val()]: $("#tokens").val()}, function(msg){
        var data = (typeof msg === 'object') ? msg : JSON.parse(msg);
        updateToken(data.token);
      });
    }
  }

  function jadikanUtama(id){
    $.post('<?= site_url("admin/api/jadikanFotoUtama/"); ?>' + id, {"idproduk": <?= $id; ?>, [$("#names").val()]: $("#tokens").val()}, function(msg){
      var data = (typeof msg === 'object') ? msg : JSON.parse(msg);
      updateToken(data.token);
      if(data.success == true){
        loadResult();
      } else {
        Swal.fire("GAGAL", "Gagal mengubah foto utama", "error");
      }
    });
  }

  function updateStok(stok){
    $("#stok").val(stok);
  }
</script>


<?= $this->endSection() ?>