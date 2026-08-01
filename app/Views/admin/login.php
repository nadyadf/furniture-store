<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Masuk | <?= esc($set->nama ?? 'Admin') ?> Manager</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('cdn/assets/img/' . esc($set->favicon ?? 'favicon.png')) ?>"/>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light d-flex flex-column min-vh-100 align-items-center justify-content-center py-4" style="font-family: 'Poppins', sans-serif;">

    <div class="container" style="max-width: 420px;">
        
        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="<?= base_url('cdn/assets/img/' . esc($set->logo ?? 'logo.png')) ?>" alt="Logo" class="img-fluid" style="max-height: 80px;">
        </div>

        <!-- Card Login -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header text-white text-center py-3 fw-bold fs-5 rounded-top-3" style="background-color: #8B5A2B;">
                MASUK
            </div>
            
            <div class="card-body p-4">

                <form id="formLogin" action="<?= site_url('admin/login/process') ?>" method="post">
                    <!-- CSRF Token bawaan CI4 -->
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-medium">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autocomplete="username">
                    </div>

                    <div class="mb-3">
                        <label for="pass" class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" id="pass" name="pass" placeholder="Masukkan password" required autocomplete="current-password">
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn fw-medium text-white btn-login">
                            <i class="fa-solid fa-check me-1"></i> Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-muted small mt-4">
            Copyright &copy; <?= date('Y') ?> | <?= esc($set->nama ?? 'App Name') ?>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $("#formLogin").on("submit", function(e) {
                e.preventDefault();

                var $btn = $(this).find("button[type='submit']");
                var originalBtnText = $btn.html();

                // Ubah teks & ikon tombol saat proses loading
                $btn.prop("disabled", true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Tunggu Sebentar...');

                $.ajax({
                    url: "<?= site_url('admin/auth') ?>",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json", // Menggunakan JSON parsing bawaan jQuery (tidak perlu eval)
                    success: function(dt) {
                        // Update CSRF Hash CI4
                        if (dt.token) {
                            $("input[name='<?= csrf_token() ?>']").val(dt.token);
                        }

                        // Kembalikan status tombol
                        $btn.prop("disabled", false).html(originalBtnText);

                        if (dt.success) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Selamat datang kembali " + (dt.name || ''),
                                icon: "success",
                                confirmButtonColor: "#3E2723" // Menyelaraskan dengan warna tombol cokelat
                            }).then(function() {
                                window.location.href = "<?= site_url('admin') ?>";
                            });
                        } else {
                            Swal.fire({
                                title: "Gagal!",
                                text: dt.message || "Gagal masuk, cek kembali username & password anda",
                                icon: "warning",
                                confirmButtonColor: "#3E2723"
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.prop("disabled", false).html(originalBtnText);
                        Swal.fire({
                            title: "Error!",
                            text: "Terjadi kesalahan pada server. Silakan coba lagi.",
                            icon: "error",
                            confirmButtonColor: "#3E2723"
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>