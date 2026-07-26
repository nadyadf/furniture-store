<?=  $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container text-center py-5">
    <div style="min-height: 60vh;" class="d-flex flex-column justify-content-center align-items-center">

        <h1 class="text-danger fw-bold mb-3" style="font-size: 8rem;">
            404
        </h1>

        <p class="fs-5 mb-4">
            Maaf, halaman yang Anda cari tidak ditemukan atau sedang dalam proses pengembangan.
            Silakan laporkan kepada admin apabila kendala ini terjadi berulang kali.
        </p>

        <div>
            <a href="javascript:history.back()" class="btn btn-primary me-2">
                <i class="fa fa-chevron-left"></i>
                Halaman Sebelumnya
            </a>

            <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                Beranda
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>