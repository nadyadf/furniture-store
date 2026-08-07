<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th scope="col" class="text-center">Tgl Transaksi</th>
        <th scope="col">No Transaksi</th>
        <th scope="col">Nama Pembeli</th>
        <th scope="col">Kurir</th>
        <th scope="col" class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($packedOrders['data']) && count($packedOrders['data']) > 0): ?>
        <?php foreach ($packedOrders['data'] as $ord): ?>
          <tr>
            <td class="text-center text-nowrap">
              <?= esc($ord->tgl_formatted ?? $ord->formatted_date ?? '-').$ord->cod_html ?>
            </td>
            <td>
              <div class="mb-1">
                <small class="text-muted">ID Transaksi:</small><br/>
                <span class="fw-bold"><?= esc($ord->orderid) ?></span>
              </div>
              <div>
                <small class="text-muted">No Invoice:</small><br/>
                <span class="fw-bold text-primary"><?= esc($ord->invoice ?? $ord->no_invoice ?? '-') ?></span>
              </div>
            </td>
            <td><?= $ord->pembeli_html ?></td>
            <td>
              <small class="text-muted">
                <i class="fas fa-warehouse text-primary me-1"></i> <?= esc($ord->nama_gudang) ?>
              </small><br/>
              <?= $ord->kurir_html; ?>
            </td>
            <td class="text-center" style="min-width: 140px;">
              <div class="dropdown">
                <!-- Bootstrap 5: data-bs-toggle="dropdown" -->
                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  Pilih Aksi
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                  <li>
                    <a href="javascript:void(0)" onclick="cetak(<?= $ord->id ?>)" class="dropdown-item py-2">
                      <i class="fas fa-print text-warning me-2"></i> Invoice
                    </a>
                  </li>
                  <li>
                    <a href="javascript:void(0)" onclick="detail(<?= $ord->id ?>)" class="dropdown-item py-2">
                      <i class="fas fa-list text-primary me-2"></i> Detail
                    </a>
                  </li>
                  <li>
                    <a href="javascript:void(0)" onclick="inputResi(<?= $ord->id ?>)" class="dropdown-item py-2 text-danger">
                      <i class="fas fa-shipping-fast me-2"></i> Update Resi
                    </a>
                  </li>
                  <li>
                    <a href="<?= site_url('admin/api/cetakLabel?id=' . $ord->id); ?>" target="_blank" class="dropdown-item py-2">
                      <i class="fas fa-print text-secondary me-2"></i> Label
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="batalkan(<?= $ord->pembayaran->id ?>)">
                      <i class="fas fa-times me-2"></i> Batalkan
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-danger py-4">Belum ada pesanan</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if (isset($pager)): ?>
    <div class="d-flex justify-content-center mt-4 pagination-ajax">
      <?= $pager ?>
    </div>
  <?php endif; ?>
</div>

<script type="text/javascript">
  $(function(){
    $("#simpan").on("submit", function(e){
      e.preventDefault();
      
      var datar = $(this).serialize();
      datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
      
      $.post("<?=site_url("admin/api/inputresi")?>", datar, function(data){
        if (typeof updateToken === 'function' && data.token) {
          updateToken(data.token);
        }
        
        // Tutup modal menggunakan instance Bootstrap 5
        var modalEl = document.getElementById('modal');
        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
        
        if (data.success == true) {
          Swal.fire({
            title: "Berhasil",
            text: "Resi telah disimpan",
            icon: "success"
          }).then((result) => {
            if (typeof loadDikirim === 'function') {
              loadDikirim(1);
            }
          });
          
          // Reset form setelah berhasil
          $("#simpan")[0].reset();
        } else {
          Swal.fire({
            title: "Gagal",
            text: data.msg || "Terjadi kesalahan saat menyimpan data, coba ulangi beberapa saat lagi",
            icon: "error"
          });
        }
      }, "json").fail(function(){
        Swal.fire({
          title: "Error!",
          text: "Gagal terhubung ke server. Silakan coba lagi.",
          icon: "error"
        });
      });
    });
  });
    
  function inputResi(id){
    $("#theid").val(id);
    
    // Tampilkan modal menggunakan JS API Bootstrap 5
    var modalEl = document.getElementById('modal');
    var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstance.show();
  }
</script>

<!-- Modal Input Resi Bootstrap 5 -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalResiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="modalResiLabel">
          <i class="fas fa-shipping-fast me-1"></i> Input Nomer Resi
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="simpan">
        <input type="hidden" id="theid" name="theid" value="0" />
        <div class="modal-body">
          <div class="mb-3">
            <label for="inputResiField" class="form-label">Masukkan Nomer Resi</label>
            <input type="text" class="form-control" id="inputResiField" name="resi" placeholder="Contoh: JNE12345678" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function konfirm(id) {
  Swal.fire({
    title: "Perhatian!",
    text: "Pastikan uang sudah benar-benar masuk/ditransfer. Lebih baik cek kembali mutasi rekening Anda.",
    icon: "warning", // Di SweetAlert2 v10+ menggunakan 'icon', bukan 'type'
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, Konfirmasi",
    cancelButtonText: "Batal"
  }).then((result) => {
    // Di SweetAlert2 v10+ menggunakan result.isConfirmed
    if (result.isConfirmed) {
      loadingDulu();

      // Menyiapkan data POST beserta CSRF Token CI4
      var postData = {
        id: id,
        statusbayar: 1
      };
      
      // Sisipkan CSRF token jika ada input #names dan #tokens
      if ($("#names").val() && $("#tokens").val()) {
        postData[$("#names").val()] = $("#tokens").val();
      }

      $.ajax({
        url: "<?= site_url("api/updatepesanan") ?>",
        type: "POST",
        data: postData,
        dataType: "json", // Menggunakan dataType 'json' otomatis mem-parse response tanpa eval()
        success: function(data) {
          // Update CSRF Token jika ada di response
          if (data.token) {
            updateToken(data.token);
          }

          if (data.success === true) {
            Swal.fire({
              title: "Berhasil!",
              text: "Pesanan siap untuk segera dikirim.",
              icon: "success"
            }).then(() => {
              loadBayar(1); // Reload data tabel setelah menekan OK
            });
          } else {
            Swal.fire(
              "Gagal!",
              data.msg || "Terjadi kendala saat mengupdate data, cobalah beberapa saat lagi.",
              "error"
            );
          }
        },
        error: function(xhr, status, error) {
          Swal.fire("Error!", "Terjadi kesalahan sistem (" + error + ")", "error");
        }
      });
    }
  });
}
</script>