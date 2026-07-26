<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="first-section">
  <div class="container mb-5">
    <h3 class="text-primary fw-bold mb-4 pt-4">Akun Saya</h3>
    <div class="tab">
      <div class="tab-header d-flex flex-wrap gap-2 mb-3">
        <!-- Tab Profil (Pengganti Saldo) -->
        <a class="navlink btn btn-primary mb-2" href="javascript:void(0)" data-link="#dashboard">
          <i class="fas fa-user me-1"></i> Profil Saya
        </a>
        
        <!-- Tab Alamat -->
        <a id="navalamat" class="navlink btn btn-light border mb-2" href="javascript:void(0)" data-link="#alamat">
          <i class="fas fa-house-user me-1"></i> Alamat
        </a>
        
        <!-- Tab Pengaturan Akun -->
        <a id="navinformasi" class="navlink btn btn-light border mb-2" href="javascript:void(0)" data-link="#informasi">
          <i class="fas fa-users-cog me-1"></i> Pengaturan Akun
        </a>
        
        <!-- Tombol Logout -->
        <a class="btn btn-danger mb-2 ms-auto" href="javascript:void(0)" onclick="signoutNow()">
          <i class="fas fa-power-off me-1"></i> Logout
        </a>
      </div>

      <div class="tab-content">
        
        <!-- DASHBOARD -->
        <div class="tab-pane fade show active mb-5" id="dashboard">
          <!-- Card Utama Dashboard Profil -->
          <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
              <div class="row align-items-center">
                
                <!-- Sisi Kiri: Sapaan & Detail Kontak -->
                <div class="col-lg-5 mb-4 mb-lg-0">
                  <span class="text-muted small">Selamat datang kembali,</span>
                  <h3 class="fw-bold text-primary mb-2">
                    <?= strtoupper(strtolower($pro->nama ?? 'PELANGGAN')) ?>
                  </h3>
                  <p class="text-secondary small mb-0">
                    <i class="fas fa-envelope me-1"></i> <?= $usr->username ?? '-' ?>
                    <span class="mx-1">•</span>
                    <i class="fas fa-phone me-1"></i> <?= $pro->no_hp ?? '-' ?>
                  </p>
                </div>

                <!-- Sisi Kanan: Ringkasan Status Pesanan Aktif -->
                <div class="col-lg-7">
                  <div class="row g-2 text-center mb-3">
                    
                    <div class="col-4">
                      <a href="<?= site_url('manage/pesanan?status=belumbayar') ?>" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-4 fw-bold text-warning">
                            <?= $count_bayar ?? 0 ?>
                          </div>
                          <div class="small text-muted">Belum Bayar</div>
                        </div>
                      </a>
                    </div>

                    <div class="col-4">
                      <a href="<?= site_url('manage/pesanan?status=dikemas') ?>" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-4 fw-bold text-info">
                            <?= $count_proses ?? 0 ?>
                          </div>
                          <div class="small text-muted">Diproses</div>
                        </div>
                      </a>
                    </div>

                    <div class="col-4">
                      <a href="<?= site_url('manage/pesanan?status=dikirim') ?>" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-4 fw-bold text-primary">
                            <?= $count_kirim ?? 0 ?>
                          </div>
                          <div class="small text-muted">Dikirim</div>
                        </div>
                      </a>
                    </div>

                  </div>

                  <!-- Tombol Akses Cepat Ke Seluruh Pesanan -->
                  <div class="d-grid">
                    <a href="<?= site_url('manage/pesanan') ?>" class="btn btn-sm fw-semibold see-orders">
                      <i class="fas fa-box-open me-1"></i> Lihat Semua Pesanan Saya
                    </a>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Bagian Bawah: Daftar Transaksi / Pesanan Terakhir -->
          <div class="row my-4">
            <div class="col-12">
              <h4 class="text-primary fw-bold">Transaksi & Pesanan Terakhir</h4>
            </div>
          </div>

          <!-- Container AJAX untuk Load Pesanan Terakhir -->
          <div id="loadhistorypesanan">
            <!-- Nanti memanggil AJAX pesanan terakhir -->
          </div>
        </div>

        <!-- ALAMAT -->
        <div class="tab-pane" id="alamat">
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
        </div>

        <!-- Tab Informasi Akun -->
        <div class="tab-pane" id="informasi">
          <div class="row g-4 my-2">
            
            <!-- Sisi Kiri: Profil Pengguna -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                  <h4 class="fw-bold text-primary mb-4">Profil Pengguna</h4>
                  
                  <?php
                    // $pro dan $usr sudah dikirim dari Controller CI4
                    $profilNama = $pro->nama ?? '';
                    $userEmail  = $usr->username ?? '';
                    $profilHp    = $pro->nohp ?? ($pro->no_hp ?? '');
                    $kelamin    = $pro->kelamin ?? 0;
                  ?>

                  <form id="profil">
                    <div class="mb-3">
                      <label class="form-label fw-medium">Nama Lengkap</label>
                      <input class="form-control" type="text" name="nama" value="<?= esc($profilNama) ?>" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-medium">Email / Username</label>
                      <input class="form-control bg-light" type="email" name="email" value="<?= esc($userEmail) ?>" readonly>
                      <div class="form-text">Email digunakan untuk login dan tidak dapat diubah.</div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-medium">No. WhatsApp</label>
                      <input class="form-control" type="text" name="nohp" value="<?= esc($profilHp) ?>" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="d-grid pt-2">
                      <button type="button" onclick="simpanProfil()" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle me-1"></i> Simpan Profil
                      </button>
                      <div id="profilload" class="text-center text-success mt-2" style="display:none;">
                        <i class="fas fa-compact-disc fa-spin me-1"></i> Menyimpan...
                      </div>
                    </div>
                  </form>

                </div>
              </div>
            </div>

            <!-- Sisi Kanan: Ganti Password -->
            <div class="col-lg-6">
              <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                  <h4 class="fw-bold text-primary mb-4">Ganti Password</h4>

                  <form id="gantipassword">
                    <div class="mb-3">
                      <label class="form-label fw-medium">Password Baru</label>
                      <input class="form-control" type="password" name="password" placeholder="Masukkan password baru" required>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-medium">Ulangi Password</label>
                      <input class="form-control" type="password" name="ulang_password" placeholder="Ketik ulang password baru" required>
                    </div>

                    <div class="d-grid pt-2">
                      <button type="button" onclick="simpanPassword()" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle me-1"></i> Simpan Password
                      </button>
                      <div id="passwload" class="text-center text-success mt-2" style="display:none;">
                        <i class="fas fa-compact-disc fa-spin me-1"></i> Menyimpan...
                      </div>
                    </div>
                  </form>

                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(function(){
    // Menampilkan riwayat transaksi/pesanan terbaru pembeli
    $("#loadhistorypesanan").load("<?= site_url("assync/pesananterakhir"); ?>");

    $(".navlink").each(function(){
      var link = $(this);
      var tab = $(this).data("link");
      console.log(tab);
        
      $(this).click(function(){
        $(".navlink.btn-primary").addClass("btn-light").removeClass("btn-primary");
        link.removeClass("btn-light").addClass("btn-primary");
        $(".tab-pane").hide();
        $(tab).show();
      });
    });

    localStorage["isedit"] = "false";

    // Event manual saat USER mengubah dropdown Provinsi secara langsung
    $("#alamatprov").change(function(){
      if (localStorage["isedit"] !== "true") {
        changeKab($(this).val(), "");
      }
    });

    // Event manual saat USER mengubah dropdown Kabupaten secara langsung
    $("#alamatkab").change(function(){
      if (localStorage["isedit"] !== "true") {
        changeKec($(this).val(), "");
      }
    });

    // Submit form
    $("#tambahalamat form").on("submit", function(e) {
      e.preventDefault();

      var $form = $(this);
      var $submitbtn = $(".submitbutton", $form);
      var originalBtnText = $submitbtn.html();

      $submitbtn.prop("disabled", true)
                .html('<i class="fas fa-compact-disc fa-spin me-1"></i> Memproses...');

      var datar = $form.serialize();
      datar += "&" + $("#names").val() + "=" + $("#tokens").val();

      $.ajax({
        url: "<?= site_url('assync/tambahalamat') ?>",
        type: "POST",
        data: datar,
        dataType: "json",
        success: function(data) {
          if (data.token) {
            updateToken(data.token);
          }

          if (data.success === true) {
            var msgSuccess = data.message || "Data alamat berhasil disimpan!";
            
            Swal.fire({
              title: "Berhasil!",
              text: msgSuccess,
              icon: "success",
              timer: 1500,
              showConfirmButton: true
            }).then(function() {
              var modalElem = document.getElementById('tambahalamat');
              var modalInstance = bootstrap.Modal.getInstance(modalElem);
              if (modalInstance) {
                modalInstance.hide();
              }

              $submitbtn.prop("disabled", false).html(originalBtnText);
              loadAlamat();
            });
          } else {
            var msgError = data.message || "Gagal menyimpan alamat, silahkan ulangi beberapa saat lagi.";
            Swal.fire("Gagal!", msgError, "error");
            $submitbtn.prop("disabled", false).html(originalBtnText);
          }
        },
        error: function(xhr, status, error) {
          console.error("Error submit alamat:", error);
          Swal.fire("Error!", "Terjadi kesalahan sistem, coba beberapa saat lagi.", "error");
          $submitbtn.prop("disabled", false).html(originalBtnText);
        }
      });
    });
  });

  function editAlamat(rek) {
    // Set string "true" ke localStorage
    localStorage["isedit"] = "true";

    let postData = { rek: rek };
    postData[$("#names").val()] = $("#tokens").val();

    $.ajax({
      url: "<?= site_url('assync/getAlamat') ?>",
      type: "POST",
      data: postData,
      dataType: "json",
      success: function(data) {
        if (data.token) {
          updateToken(data.token);
        }

        if (data.success === true) {
          // 1. Isikan data ke input form
          $("#alamatid").val(rek);
          $("#alamatnama").val(data.nama);
          $("#alamatnohp").val(data.nohp);
          $("#alamatstatus").val(data.status).trigger("change");
          $("#alamatalamat").val(data.alamat);
          $("#alamatkodepos").val(data.kodepos);
          $("#alamatjudul").val(data.judul);

          // 2. Set Nilai Provinsi tanpa memicu listener change manual
          $("#alamatprov").val(data.prov);

          // 3. Panggil Kabupaten, dan Panggil Kecamatan SETELAH Kabupaten selesai di-render (Callback)
          changeKab(data.prov, data.kab, function() {
            changeKec(data.kab, data.idkec, function() {
              // Setelah semua opsi selesai dimuat dan dipilih, matikan status edit
              localStorage["isedit"] = "false";
            });
          });

          // 4. Sembunyikan modal lain jika ada
          $('.modal').each(function() {
            let activeModal = bootstrap.Modal.getInstance(this);
            if (activeModal) activeModal.hide();
          });

          // 5. Tampilkan Modal Edit
          let modalElem = document.getElementById('tambahalamat');
          let modalInstance = bootstrap.Modal.getOrCreateInstance(modalElem);
          modalInstance.show();

        } else {
          Swal.fire("Error!", data.message || "Terjadi kesalahan, silahkan ulangi beberapa saat lagi.", "error");
        }
      },
      error: function(xhr, status, error) {
        console.error("Error getAlamat:", error);
        Swal.fire("Error!", "Gagal mengambil data alamat dari server.", "error");
      }
    });
  }

  function loadAlamat() {
    $("#alamat").html(`
      <div class="text-center p-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Memuat ulang daftar alamat...</p>
      </div>
    `);

    $.ajax({
      url: "<?= site_url('assync/loadalamat') ?>",
      type: "GET",
      success: function(htmlData) {
        $("#alamat").html(htmlData);
      },
      error: function() {
        $("#alamat").html(`
          <div class="alert alert-danger m-3">
            Gagal memuat daftar alamat. Silahkan muat ulang halaman.
          </div>
        `);
      }
    });
  }

  function tambahAlamat() {
    localStorage["isedit"] = "false";
    $("#alamatid").val(0);
    $("#alamatnama").val("");
    $("#alamatnohp").val("");
    $("#alamatstatus").val(0).trigger("change");
    $("#alamatalamat").val("");
    $("#alamatkodepos").val("");
    $("#alamatjudul").val("");
    $("#alamatprov").val("");
    $("#alamatkab").html("<option value=''>Pilih Kabupaten/Kota</option>");
    $("#alamatkec").html("<option value=''>Pilih Kecamatan</option>");

    $('.modal').each(function() {
      let activeModal = bootstrap.Modal.getInstance(this);
      if (activeModal) activeModal.hide();
    });

    var modalElem = document.getElementById('tambahalamat');
    var modalInstance = bootstrap.Modal.getOrCreateInstance(modalElem);
    modalInstance.show();
  }

  function changeKab(proval, valu, callback) {
    $("#alamatkab").html("<option value=''>Loading...</option>");
    
    if (localStorage["isedit"] !== "true") {
      $("#alamatkec").html("<option value=''>Pilih Kecamatan</option>");
    }

    if (!proval) {
      $("#alamatkab").html("<option value=''>Pilih Kabupaten/Kota</option>");
      return;
    }

    let postData = { id: proval };
    postData[$("#names").val()] = $("#tokens").val();

    $.ajax({
      url: "<?= site_url('assync/getkab') ?>",
      type: "POST",
      data: postData,
      dataType: "json",
      success: function(data) {
        if (data.token) {
          updateToken(data.token);
        }

        $("#alamatkab").html(data.html).promise().done(function() {
          if (valu) {
            $("#alamatkab").val(valu);
          }
          
          // Jalankan callback jika disediakan
          if (typeof callback === "function") {
            callback();
          }
        });
      },
      error: function(xhr, status, error) {
        console.error("Error Get Kab:", error);
        $("#alamatkab").html("<option value=''>Gagal memuat data</option>");
      }
    });
  }

  function changeKec(kabval, valu, callback) {
    $("#alamatkec").html("<option value=''>Loading...</option>");

    if (!kabval) {
      $("#alamatkec").html("<option value=''>Pilih Kecamatan</option>");
      return;
    }

    let postData = { id: kabval };
    postData[$("#names").val()] = $("#tokens").val();

    $.ajax({
      url: "<?= site_url('assync/getkec') ?>",
      type: "POST",
      data: postData,
      dataType: "json",
      success: function(data) {
        if (data.token) {
          updateToken(data.token);
        }

        $("#alamatkec").html(data.html).promise().done(function() {
          if (valu) {
            $("#alamatkec").val(valu);
          }

          // Jalankan callback jika disediakan
          if (typeof callback === "function") {
            callback();
          }
        });
      },
      error: function(xhr, status, error) {
        console.error("Error Get Kec:", error);
        $("#alamatkec").html("<option value=''>Gagal memuat data</option>");
      }
    });
  }

  function hapusAlamat(rek) {
  Swal.fire({
    title: "Anda yakin?",
    text: "Menghapus alamat ini dari akun Anda?",
    icon: "warning",
    showCancelButton: true, // Lebih lazim menggunakan Cancel dibanding Deny
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d"
  }).then((result) => {
    if (result.isConfirmed) {
      
      // Payload CSRF + ID Alamat
      let postData = { rek: rek };
      postData[$("#names").val()] = $("#tokens").val();

      $.ajax({
        url: "<?= site_url('assync/hapusAlamat') ?>",
        type: "POST",
        data: postData,
        dataType: "json", // Menggunakan JSON native, tidak perlu eval() lagi
        success: function(data) {
          // Refresh CSRF Token CI4
          if (data.token) {
            updateToken(data.token);
          }

          if (data.success === true) {
            Swal.fire({
              title: "Berhasil!",
              text: data.message || "Berhasil menghapus alamat.",
              icon: "success",
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              // Reload partial bagian tabel alamat saja (tanpa refresh browser)
              loadAlamat(); 
            });
          } else {
            Swal.fire("Error!", data.message || "Terjadi kesalahan, silahkan ulangi beberapa saat lagi.", "error");
          }
        },
        error: function(xhr, status, error) {
          console.error("Error hapusAlamat:", error);
          Swal.fire("Error!", "Gagal menghapus alamat dari server.", "error");
        }
      });
    }
  });
}

function simpanProfil() {
  $("#profil a").hide();
  $("#profilload").show();

  var datar = $("#profil").serialize();
  
  datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();

  $.post("<?= site_url('assync/updateprofil'); ?>", datar, function(msg) {
    var data = (typeof msg === "object") ? msg : JSON.parse(msg);

    if (data.token) {
      updateToken(data.token);
    }

    $("#profil a").show();
    $("#profilload").hide();

    if (data.success === true) {
      Swal.fire("Berhasil!", "Berhasil menyimpan informasi pengguna", "success");

      var namaBaru = $("input[name='nama']").val();
      var emailBaru = $("input[name='email']").val();
      var nohpBaru = $("input[name='nohp']").val();
      var kelaminBaru = $("select[name='kelamin']").val(); // Atau radio button sesuai struktur form kamu

      $("input[name='nama']").attr("value", namaBaru);
      $("input[name='email']").attr("value", emailBaru);
      $("input[name='nohp']").attr("value", nohpBaru);

      $(".label-nama-user").text(namaBaru);
      $(".label-email-user").text(emailBaru);
    } else {
      Swal.fire("Gagal!", "Gagal menyimpan informasi pengguna<br/>" + (data.msg || ''), "error");
    }
  }, "json"); 
}

function simpanPassword() {
  $("#gantipassword a").hide();
  $("#passwload").show();

  // Serialisasi data form password
  var datar = $("#gantipassword").serialize();
  
  // Menambahkan token CSRF jika dikirim manual lewat input hidden
  datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();

  $.post("<?= site_url('assync/updatepass'); ?>", datar, function(msg) {
    // Parsing respons secara aman
    var data = (typeof msg === "object") ? msg : JSON.parse(msg);

    // Update token CSRF untuk request berikutnya
    if (data.token) {
      updateToken(data.token);
    }

    $("#gantipassword a").show();
    $("#passwload").hide();

    if (data.success === true) {
      // Kosongkan seluruh input password setelah berhasil
      $("#gantipassword input[type='password']").val("");
      
      Swal.fire("Berhasil!", "Berhasil menyimpan password baru", "success");
    } else {
      Swal.fire("Gagal!", "Gagal menyimpan informasi password<br/>" + (data.msg || ''), "error");
    }
  }, "json"); // Mengatur dataType secara eksplisit sebagai JSON
}
</script>


<!-- MODAL TAMBAH / EDIT ALAMAT -->
<div class="modal fade" id="tambahalamat" tabindex="-1" aria-labelledby="tambahalamatLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="tambahalamatLabel">Informasi Alamat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <form id="formAlamat" class="row g-3">
          <input type="hidden" name="id" id="alamatid" value="0" />

          <!-- Judul Alamat -->
          <div class="col-md-12">
            <label for="alamatjudul" class="form-label fw-medium">
              Simpan sebagai? <small class="text-muted">(cth: Alamat Rumah, Alamat Kantor, dll)</small>
            </label>
            <input class="form-control" id="alamatjudul" type="text" name="judul" placeholder="Masukkan nama/label alamat" required />
          </div>

          <!-- Nama Penerima & No HP -->
          <div class="col-md-6">
            <label for="alamatnama" class="form-label fw-medium">Nama Penerima</label>
            <input class="form-control" id="alamatnama" type="text" name="nama" placeholder="Nama lengkap penerima" required />
          </div>

          <div class="col-md-6">
            <label for="alamatnohp" class="form-label fw-medium">No Handphone</label>
            <input class="form-control" id="alamatnohp" type="text" name="nohp" placeholder="08xxxxxxxxxx" required />
          </div>

          <!-- Alamat Lengkap -->
          <div class="col-md-12">
            <label for="alamatalamat" class="form-label fw-medium">Alamat Lengkap</label>
            <textarea class="form-control" id="alamatalamat" name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, patokan" required></textarea>
          </div>

          <!-- Provinsi & Kabupaten -->
          <div class="col-md-6">
            <label for="alamatprov" class="form-label fw-medium">Provinsi</label>
            <select class="form-select js-select2" id="alamatprov" required>
              <option value="">Pilih Provinsi</option>
              <?php
                foreach ($provinsi as $prov) {
                  echo "<option value='".$prov->id."'>".$prov->nama."</option>";
                }
              ?>
            </select>
          </div>

          <div class="col-md-6">
            <label for="alamatkab" class="form-label fw-medium">Kabupaten/Kota</label>
            <select class="form-select js-select2" id="alamatkab" required>
              <option value="">Pilih Kabupaten/Kota</option>
            </select>
          </div>

          <!-- Kecamatan, Kodepos & Status -->
          <div class="col-md-4">
            <label for="alamatkec" class="form-label fw-medium">Kecamatan</label>
            <select class="form-select js-select2" id="alamatkec" name="idkec" required>
              <option value="">Pilih Kecamatan</option>
            </select>
          </div>

          <div class="col-md-4">
            <label for="alamatkodepos" class="form-label fw-medium">Kodepos</label>
            <input class="form-control" id="alamatkodepos" type="text" name="kodepos" placeholder="Kodepos" required />
          </div>

          <div class="col-md-4">
            <label for="alamatstatus" class="form-label fw-medium">Status Alamat</label>
            <select class="form-select" id="alamatstatus" name="status" required>
              <option value="0">Alamat Biasa</option>
              <option value="1">Alamat Utama</option>
            </select>
          </div>

          <!-- Tombol Submit -->
          <div class="col-12 pt-3">
            <button type="submit" class="submitbutton btn btn-success btn-lg w-100">
              <i class="fas fa-save me-1"></i> Simpan Alamat
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>