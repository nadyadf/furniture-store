<div class="table-responsive">
  <!-- Menggunakan table-sm & base font 0.85rem (13.5px) -->
  <table class="table table-sm table-hover align-middle" style="font-size: 0.85rem;">
    <thead class="table-light">
      <tr>
        <th scope="col" class="text-center" style="width: 15%;">Tgl Transaksi</th>
        <th scope="col" style="width: 25%;">No Transaksi</th>
        <th scope="col" style="width: 30%;">Nama Pembeli</th>
        <th scope="col" style="width: 22%;">Kurir</th>
        <th scope="col" class="text-center" style="width: 8%;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($packedOrders['data']) && count($packedOrders['data']) > 0): ?>
        <?php foreach ($packedOrders['data'] as $ord): ?>
          <tr>
            <td class="text-center text-nowrap">
              <span><?= esc($ord->tgl_formatted ?? $ord->formatted_date ?? '-'); ?></span>
              <?= $ord->cod_html; ?>
            </td>
            <td>
              <div class="mb-1">
                <small class="text-muted d-block lh-1" style="font-size: 0.725rem;">ID Transaksi:</small>
                <strong class="text-dark"><?= esc($ord->orderid); ?></strong>
              </div>
              <div>
                <small class="text-muted d-block lh-1" style="font-size: 0.725rem;">No Invoice:</small>
                <strong class="text-primary"><?= esc($ord->invoice ?? $ord->no_invoice ?? '-'); ?></strong>
              </div>
            </td>
            <td><?= $ord->pembeli_html; ?></td>
            <td>
              <small class="text-muted d-block mb-1" style="font-size: 0.725rem;">
                <i class="fas fa-warehouse text-primary me-1"></i> <?= esc($ord->nama_gudang); ?>
              </small>
              <?= $ord->kurir_html; ?>
            </td>
            <td class="text-center">
              <div class="dropdown">
                <button type="button" 
                        class="btn btn-primary btn-sm dropdown-toggle py-1 px-2" 
                        style="font-size: 0.775rem;" 
                        data-bs-toggle="dropdown" 
                        data-bs-popper-config='{"strategy":"fixed"}' 
                        aria-expanded="false">
                  Pilih Aksi
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 0.825rem;">
                  <li>
                    <a href="javascript:void(0)" onclick="cetak(<?= $ord->id; ?>)" class="dropdown-item py-1.5">
                      <i class="fas fa-print text-warning me-2"></i> Invoice
                    </a>
                  </li>
                  <li>
                    <a href="javascript:void(0)" onclick="detail(<?= $ord->id; ?>)" class="dropdown-item py-1.5">
                      <i class="fas fa-list text-primary me-2"></i> Detail
                    </a>
                  </li>
                  <li>
                    <a href="javascript:void(0)" onclick="inputResi(<?= $ord->id; ?>)" class="dropdown-item py-1.5 text-danger">
                      <i class="fas fa-shipping-fast me-2"></i> Update Resi
                    </a>
                  </li>
                  <li>
                    <a href="<?= site_url('admin/api/cetakLabel?id=' . $ord->id); ?>" target="_blank" class="dropdown-item py-1.5">
                      <i class="fas fa-print text-secondary me-2"></i> Label
                    </a>
                  </li>
                  <li><hr class="dropdown-divider my-1"></li>
                  <li>
                    <a class="dropdown-item py-1.5 text-danger" href="javascript:void(0)" onclick="batalkan(<?= $ord->pembayaran->id ?? $ord->idbayar ?? $ord->id; ?>)">
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
          <td colspan="5" class="text-center py-4 text-danger">
            <i class="fas fa-box-open fa-2x mb-2 d-block text-muted"></i>
            Belum ada pesanan yang perlu dikirim.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination AJAX CI4 -->
  <?php if (isset($pager)): ?>
    <div class="d-flex justify-content-center mt-3 pagination-ajax" style="font-size: 0.8rem;">
      <?= $pager ?>
    </div>
  <?php endif; ?>
</div>

<script type="text/javascript">
  $(function(){
    // 1. Submit Form Input Resi
    $("#simpan").on("submit", function(e){
      e.preventDefault();
      
      var datar = $(this).serializeArray();
      // Tambahkan CSRF Token CI4 ke payload
      datar.push({ name: "<?= csrf_token() ?>", value: "<?= csrf_hash() ?>" });
      
      $.post("<?= site_url("admin/api/inputresi") ?>", $.param(datar), function(data){
        if (typeof updateToken === 'function' && data.token) {
          updateToken(data.token);
        }
        
        // Tutup modal menggunakan instance Bootstrap 5
        var modalEl = document.getElementById('modal');
        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
        
        if (data.success === true) {
          Swal.fire({
            title: "Berhasil",
            text: "Nomor resi telah disimpan",
            icon: "success"
          }).then(() => {
            // Reload otomatis data pada tab aktif
            var activeStatus = $("#pesananTab .nav-link.active").data("item") || "perlu_dikirim";
            if (typeof loadPesanan === 'function') {
              loadPesanan(activeStatus, 1);
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
    
  // 2. Buka Modal Input Resi
  function inputResi(id){
    $("#theid").val(id);
    $("#inputResiField").val(""); // Clear input sebelumnya
    
    var modalEl = document.getElementById('modal');
    var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstance.show();
  }

  // 3. Konfirmasi Verifikasi Pembayaran
  function konfirm(id) {
    Swal.fire({
      title: "Perhatian!",
      text: "Pastikan uang sudah benar-benar masuk/ditransfer. Lebih baik cek kembali mutasi rekening Anda.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#198754",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, Konfirmasi",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        if (typeof loadingDulu === 'function') {
          loadingDulu();
        }

        // Payload data POST + CSRF Token CI4
        var postData = {
          id: id,
          statusbayar: 1,
          "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
        };

        $.ajax({
          url: "<?= site_url("admin/api/updatepesanan") ?>",
          type: "POST",
          data: postData,
          dataType: "json",
          success: function(data) {
            if (typeof updateToken === 'function' && data.token) {
              updateToken(data.token);
            }

            if (data.success === true) {
              Swal.fire({
                title: "Berhasil!",
                text: "Pesanan siap untuk segera dikirim.",
                icon: "success"
              }).then(() => {
                // Reload data pada tab aktif
                var activeStatus = $("#pesananTab .nav-link.active").data("item") || "bayar";
                if (typeof loadPesanan === 'function') {
                  loadPesanan(activeStatus, 1);
                }
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

<!-- Modal Input Resi (Disesuaikan ukurannya) -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalResiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content shadow-sm border-0">
      <div class="modal-header py-2 px-3 bg-light">
        <h6 class="modal-title fw-bold text-dark mb-0" id="modalResiLabel" style="font-size: 0.875rem;">
          <i class="fas fa-shipping-fast text-primary me-1"></i> Input Nomor Resi
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
      </div>
      <form id="simpan">
        <input type="hidden" id="theid" name="theid" value="0" />
        <div class="modal-body p-3" style="font-size: 0.85rem;">
          <div class="mb-2">
            <label for="inputResiField" class="form-label fw-semibold text-muted mb-1" style="font-size: 0.775rem;">Nomor Resi Pengiriman</label>
            <input type="text" class="form-control form-control-sm" id="inputResiField" name="resi" placeholder="Contoh: JNE12345678" required style="font-size: 0.825rem;" />
          </div>
        </div>
        <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-end gap-1">
          <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="font-size: 0.775rem;">Batal</button>
          <button type="submit" id="submit" class="btn btn-success btn-sm px-3" style="font-size: 0.775rem;">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>