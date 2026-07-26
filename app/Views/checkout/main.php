<?=  $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="progress-wrap col-md-10 px-0 pt-4 mx-auto first-section" style="overflow:hidden;">
        <div class="row progress-checkout">
            <div class="line"></div>
            <div class="col-4 prog-alamat">
                <div class="wrap active">
                    <i class="fa-solid fa-map-marker-alt"></i>
                    <div class="titles">Alamat</div>
                </div>
            </div>
            <div class="col-4 prog-kurir">
                <div class="wrap">
                    <i class="fa-solid fa-shipping-fast"></i>
                    <div class="titles">Kurir</div>
                </div>
            </div>
            <div class="col-4 prog-bayar">
                <div class="wrap">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <div class="titles hidesmall">Pembayaran</div>
                    <div class="titles showsmall">Bayar</div>
                </div>
            </div>
        </div>
        <div class="p-4 mt-3 mb-6">
            <div class="load">
                <div class="py-4 text-center">
                    <i class="fa-solid fa-compact-disc fa-spin text-primary"></i> tunggu sebentar...
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function(){
            $(".load").load("<?=site_url("checkout/alamat")?>");
            
        });

        function loadKurir(){
            $(".progress-checkout .wrap").removeClass("active");
            $(".progress-checkout .prog-kurir .wrap").addClass("active");
            $(".load").html('<div class="p-tb-30 text-center"><i class="fas fa-compact-disc fa-spin text-primary fs-32 m-b-12"></i><br/>memuat pilihan kurir yang dapat mengirim pesanan ke alamat Anda</div>');
            $(".load").load("<?=site_url("checkout/kurir")?>");
        }

        function loadBayar(){
            $(".progress-checkout .wrap").removeClass("active");
            $(".progress-checkout .prog-bayar .wrap").addClass("active");
            $(".load").html('<div class="p-tb-30 text-center"><i class="fas fa-compact-disc fa-spin text-primary"></i> tunggu sebentar...</div>');
            $(".load").load("<?=site_url("checkout/bayar")?>");
        }

    </script>

<?= $this->endSection() ?>