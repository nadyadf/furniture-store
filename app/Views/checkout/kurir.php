<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Pilih Kurir Pengiriman</h5>

        <div class="row g-3">
            <?php foreach ($kurir as $id => $nama): ?>

                <div class="col-6 col-md-4 col-lg-3 col-xl-2 kurir-pilih-atas">
                    <div class="kurir-wrap kurir-select h-100 text-center p-3 rounded"
                         data-kurir="<?= esc($id) ?>">

                        <i class="fas fa-check-circle"></i>

                        <?php
                        $logo = FCPATH . 'cdn/assets/img/kurir/' . strtolower($nama) . '.png';
                        ?>

                        <?php if (is_file($logo)): ?>

                            <img
                                src="<?= base_url('cdn/assets/img/kurir/' . strtolower($nama) . '.png') ?>"
                                class="img-fluid"
                                alt="<?= esc($nama) ?>">

                        <?php else: ?>

                            <span class="fw-semibold">
                                <?= strtoupper($nama) ?>
                            </span>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section p-4 mb-4">
    <h5 class="mb-4">Pilih Paket Pengiriman</h5>

    <div class="text-danger mb-3 pilihkurir">
        Pilih Kurir Dulu
    </div>

    <?php foreach ($paket as $idKurir => $paketList): ?>

        <div class="row g-3 paket-list" id="kur_<?= $idKurir ?>" style="display:none">

            <?php foreach ($paketList as $idPaket => $item): ?>

                <?php
                    $etd  = (!empty($item['etd']) && intval($item['etd']) > 0)
                        ? intval($item['etd'])
                        : 1;

                    $etdSelesai = $etd + 3;
                ?>

                <div class="col-12 col-md-6 col-lg-4">

                    <div class="kurir-wrap paket-select h-100"
                         id="paket_<?= $idPaket ?>"
                         data-paket="<?= $idPaket ?>">

                        <i class="fas fa-check-circle"></i>

                        <div class="font-medium">
                            <?= $item['nama'] ?>
                        </div>

                        <div class="text-success fw-semibold">
                            Ongkir Rp <?= number_format($item["harga"],0,',','.') ?>
                        </div>

                        <?php if (!empty($item["etd"])): ?>
                            <div class="small text-muted mb-2">
                                Perkiraan sampai
                                <b><?= date('d-m', strtotime("+{$etd} days")) ?></b>
                                s/d
                                <b><?= date('d-m', strtotime("+{$etdSelesai} days")) ?></b>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($item["cod"])): ?>
                            <span class="badge text-bg-warning">
                                Bisa bayar ditempat (COD)
                            </span>
                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endforeach; ?>

</div>

<form id="lanjut" style="display:none">
    <input type="hidden" id="kurir" name="kurir" />
    <input type="hidden" id="paket" name="paket" />
    <div class="text-center">
        <button type="submit" class="btn btn-lg btn-primary">SELANJUTNYA &nbsp;<i class="fas fa-chevron-right"></i></button>
    </div>
</form>

<script type="text/javascript">
    $(function(){
        $(".kurir-select").click(function(){
            $(".kurir-select").removeClass("active");
            $(".paket-select").removeClass("active");
            $(this).addClass("active");
            var kurir = $(this).data("kurir");
            $("#kurir").val(kurir);
            $("#paket").val("0");
            $("#lanjut").hide();
            $(".paket-list").hide();
            $(".pilihkurir").hide();
            $("#kur_"+kurir).show();
        });
        $(".paket-select").click(function(){
            $(".paket-select").removeClass("active");
            $(this).addClass("active");
            var paket = $(this).data("paket");
            $("#paket").val(paket);
            $("#lanjut").show();
        });
        
        $("#lanjut").on("submit", function (e) {
            e.preventDefault();

            $.ajax({
                url: "<?= site_url('checkout/simpankurir') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {

                    if (response.success) {
                        loadBayar();
                    } else {
                        Swal.fire(
                            "Gagal Menyimpan",
                            "Terjadi kesalahan saat menyimpan data kurir pilihan Anda. Silakan ulangi beberapa saat lagi.",
                            "warning"
                        );
                    }

                },
                error: function () {

                    Swal.fire(
                        "Error",
                        "Terjadi kesalahan pada server.",
                        "error"
                    );

                }
            });
        });
    });
</script>