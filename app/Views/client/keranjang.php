<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<section class="pt-5 pb-5 first-section">
    <div class="container cart">
        <h2 class="text-center mb-4 fw-bold">Keranjang Belanja</h2>

        <?php if(count($dataKeranjang) > 0): ?>

        <form action="<?= site_url('checkout') ?>" method="post">
            <div class="mx-auto mb-5">
            <?php 
            $lastGudang = null;
            $gudangAwal = $dataKeranjang[0]->gudang ?? null;
            $total = 0;
            foreach ($dataKeranjang as $car): 
            ?>

                <?php if($lastGudang != $car->gudang): ?>
                    <div class="cart-city p-3 mb-4 rounded border">
                        Dikirim dari &nbsp;<b><i class="fas fa-map-marker-alt"></i> Kota <?= $car->gudang ?></b>
                    </div>
                    <?php $lastGudang = $car->gudang; ?>
                <?php endif; ?>

                <?php
                $checked = ($car->gudang == $gudangAwal);
                ?>
                <div class="row cart-item-wrapper mb-3 border rounded p-3" id="produk_<?= $car->id ?>">

                    <div class="col-md-1">
                        <input type="checkbox" name="idproduk[]" class="checkbox pointer" value="<?= $car->id ?>" <?= $checked ? 'checked' : '' ?> data-subtotal="<?= $car->harga * $car->jumlah ?>">
                    </div>

                    <div class="col-md-2 col-4 pointer">
                        <div class="img"
                            style="<?= !empty($car->gambar) 
                                ? "background-image:url('".base_url('cdn/uploads/'.$car->gambar->nama)."')" 
                                : '' ?>">
                        </div>
                    </div>

                    <div class="col-md-9 col-12 row mx-0">
                        <div class="col-md-5 mb-2 d-flex flex-column justify-content-center pointer" onclick="window.location.href='<?= site_url('produk/'.$car->url); ?>'">
                            <span class="fw-medium w-100">
                                <?= esc($car->nama) ?>
                            </span>
                            <?php 
                                if($car->variasi > 0){
                                    echo "
                                    <span class='text-variasi w-100 small fw-bold'>
                                        ".$car->nama_warna."
                                    </span>";
                                }

                                if($car->keterangan != ""){
                                    echo "<span class='text-warning w-100' style='font-size:80%;'><b>Note : </b> <i>".$car->keterangan."</i></span>";
                                }
                            ?>
                        </div>

                        
                        <div class="col-md-3 d-flex justify-content-center align-items-center">
                            <div class="input-group wrap-num-product">

                                <button type="button"
                                        class="btn btn-number-input num-product-down">
                                    <i class="fas fa-minus text-light"></i>
                                </button>

                                <input 
                                    class="form-control text-center num-product produk-jumlah"
                                    type="number"
                                    min="<?= $car->min_order; ?>"
                                    id="jumlah_<?= $car->id; ?>"
                                    name="jumlah[]"
                                    value="<?= $car->jumlah; ?>"
                                    data-proid="<?= $car->id; ?>"
                                >

                                <button type="button"
                                        class="btn btn-number-input num-product-up">
                                    <i class="fas fa-plus text-light"></i>
                                </button>

                            </div>
                        </div>

                        <div class="col-md-3 centered">
                            Rp <span id="totalperproduk_<?= $car->id ?>">
                                <?= number_format($car->harga * $car->jumlah,0,',','.') ?>
                            </span>
                        </div>

                        <div class="col-md-1 centered">
                            <button type="button"
                                    onclick="hapus(<?= $car->id ?>)"
                                    class="btn btn-danger btn-sm">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div>

                    </div>
                </div>

                <input type="hidden" id="harga_<?= $car->id ?>" value="<?= $car->harga ?>">
                <input type="hidden" class="totalhargaproduk" id="totalharga_<?= $car->id; ?>" value="<?= $car->harga*$car->jumlah; ?>" />

            <?php endforeach; ?>

            <div class="alert alert-warning text-center">
                Pilih produk yang akan Anda bayar terlebih dahulu, pastikan asal pengirimannya sesuai.
                Apabila yang Anda pilih tercampur, maka hanya akan ada satu asal pengiriman saja yang diproses untuk checkout ke pembayaran dan produk lainnya akan dikembalikan ke keranjang belanja Anda.
            </div>

            <div class="pt-4 pb-3 px-4 mb-5">
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <h5 class="mb-3 fw-bold text-success text-end">
                            Total : Rp 
                            <span id="totalbayar"></span>
                        </h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6"></div>

                    <div class="col-md-3 mb-2 px-1">
                        <a href="<?php echo site_url("katalog"); ?>" class="btn btn-back-to-shop btn-lg w-100">
                            Kembali Berbelanja
                        </a>
                    </div>

                    <div class="col-md-3 mb-2 px-1">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            Selesaikan Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php else: ?>

    <div class="alert alert-info text-center">
        Keranjang kosong
    </div>

    <?php endif; ?>
  </div>
</section>

<script>
    $(document).on('click', '.num-product-down', function (e) {
        e.preventDefault();

        let input = $(this).siblings('input');
        let min = parseInt(input.attr('min')) || 1;
        let numProduct = parseInt(input.val()) || min;

        if (numProduct > min) {
            input.val(numProduct - 1);

            input[0].dispatchEvent(new Event('change', { bubbles: true }));
        }
    });

    $(document).on('click', '.num-product-up', function (e) {
        e.preventDefault();

        let input = $(this).siblings('input');
        let numProduct = parseInt(input.val()) || 1;

        input.val(numProduct + 1);

        input[0].dispatchEvent(new Event('change', { bubbles: true }));
    });


    $(document).on('change', '.produk-jumlah', function () {

        let input = $(this);

        let jumlah = parseInt(input.val()) || 0;
        let prodid = input.data('proid');

        let harga = parseFloat($("#harga_" + prodid).val()) || 0;
        let hargatotal = jumlah * harga;

        let min = parseInt(input.attr("min")) || 1;

        if (jumlah > 0) {

            if (jumlah < min) {
                input.val(min).trigger("change");
                return;
            }

            $.post(
                "<?= site_url('assync/updatekeranjang'); ?>",
                {
                    update: prodid,
                    jumlah: jumlah,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                function (data) {

                    // updateToken(data.token);

                    if (data.success == false) {
                        Swal.fire("Gagal", data.msg, "error")
                        .then(() => {
                            location.reload();
                        });
                    }

                },
                'json'
            );

            $("#totalperproduk_" + prodid).html(
                Number(hargatotal).toLocaleString('id-ID')
            );
            $("#totalharga_" + prodid).val(hargatotal);

            hitungTotal();

        } else {

            Swal.fire({
                title: "Anda yakin?",
                text: "Menghapus produk dari keranjang belanja",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: "Oke",
                denyButtonText: "Batal",
            })
            .then((result) => {

                if (result.isConfirmed) {

                    $.post(
                        "<?= site_url('assync/hapuskeranjang'); ?>",
                        {
                            hapus: prodid,
                            [$("#names").val()]: $("#tokens").val()
                        },
                        function (data) {

                            updateToken(data.token);

                            if (data.success == true) {
                                location.reload();
                            } else {
                                Swal.fire(
                                    "Gagal",
                                    "Gagal menghapus pesanan",
                                    "error"
                                );
                            }

                        },
                        'json'
                    );

                } else {
                    input.val(min);
                }

            });
        }

    });

    function hapus(id){
		const input = document.getElementById("jumlah_" + id);

        input.value = 0;

        input.dispatchEvent(
            new Event("change", { bubbles: true })
        );
	}

    function hitungTotal(){

        let sum = 0;

        $(".checkbox:checked").each(function(){

            const prodid = $(this).val();

            sum += parseFloat(
                $("#totalharga_" + prodid).val()
            ) || 0;
        });

        $("#totalbayar").html(
            Number(sum).toLocaleString('id-ID')
        );
    }

    $(document).ready(function(){
        hitungTotal();
    });

    $(document).on("change", ".checkbox", function(){
        hitungTotal();
    });

</script>

<?= $this->endSection() ?>