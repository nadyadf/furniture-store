<?php

$adasub = false;
$variasi = [];
$stok = [];
$harga = [];
$subvariasi = [];
$stokvariasi = [];

$totalstok = count($variasiData) > 0 ? 0 : $prod->stok;

foreach($variasiData as $r){

    $variasi[$r->idwarna] = $r->id;

    $stokvariasi[$r->idwarna] =
        isset($stokvariasi[$r->idwarna])
        ? $stokvariasi[$r->idwarna] + $r->stok
        : $r->stok;

    $subvariasi[$r->idwarna] = $r->id;

    $totalstok += $r->stok;

    $stok[$r->id] = $r->stok;
    $harga[$r->id] = $r->harga;

}

if($totalstok == 0){
    echo "<div class='text-center alert alert-danger'>
            Mohon maaf, stok produk telah habis.
          </div>";
    return;
}
?>

<form id="atcart">
<div class="p-3">

    <div class="mb-3">
        <label class="form-label">Nama Produk</label>
        <div class="fw-bold text-primary">
            <?= ucwords(strtolower($prod->nama)) ?>
        </div>
    </div>

    <?php if(count($warna) > 0){ ?>

    <div class="mb-3">
        <label class="form-label">
            Varian <?= ucwords(strtolower($prod->variasi)) ?>
        </label>

        <select id="varian" class="form-select" required>

            <option value="">
                Pilih Varian <?= ucwords(strtolower($prod->variasi)) ?>
            </option>

            <?php foreach($warna as $w): ?>

            <?php
              $hg = $w->harga;
            ?>

            <?php if($w->stok > 0): ?>

            <option
                value="<?=$w->idwarna?>"
                data-stok="<?=$w->stok?>"
                data-harga="<?=$hg?>"
                data-variasi="<?=$w->id?>">

                <?=$w->warna?>

            </option>

            <?php endif ?>

            <?php endforeach ?>

        </select>
    </div>

    <?php } ?>

    <div class="mb-3">
        <label class="form-label">Jumlah Pembelian</label>

        <div class="row align-items-center">

            <div class="col-6">

                <div class="input-group">

                    <button
                        class="btn btn-primary"
                        type="button"
                        onclick="$('#jumlah').val(parseFloat($('#jumlah').val())-1).trigger('change')">

                        <i class="fas fa-minus"></i>

                    </button>

                    <input
                        type="number"
                        class="form-control text-center"
                        id="jumlah"
                        name="jumlah"
                        value="1"
                        max="<?=$totalstok?>"
                        required>

                    <button
                        class="btn btn-primary"
                        type="button"
                        onclick="$('#jumlah').val(parseFloat($('#jumlah').val())+1).trigger('change')">

                        <i class="fas fa-plus"></i>

                    </button>

                </div>

            </div>

            <div class="col-6 text-primary">
                <i class="fas fa-box"></i>
                <b id="stok"><?=$totalstok?></b> pcs
            </div>

        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Catatan Tambahan</label>
        <input type="text" name="keterangan" class="form-control">
    </div>

    <input type="hidden" name="variasi" id="variasi" value="0">
    <input type="hidden" name="harga" id="harga" value="<?=$prod->harga?>">
    <input type="hidden" name="idproduk" value="<?=$prod->id?>">

    <div class="d-grid">
        <button type="submit" class="btn btn-submit">
            <i class="fas fa-check"></i> Tambahkan ke Keranjang
        </button>
    </div>

</div>
</form>


<script>
$(function(){

    // cek apakah produk punya variasi
    var variasi = <?= count($warna) > 0 ? 'true' : 'false' ?>;

    // kontrol jumlah pembelian
    $("#jumlah").on("change", function(){

        let val = parseInt($(this).val());
        let max = parseInt($(this).attr("max"));

        if(val < 1){
            $(this).val(1);
        }

        if(val > max){
            $(this).val(max);
        }

    });


    // ketika varian dipilih
    $("#varian").on("change", function(){

        $("#jumlah").val(1);

        let selected = $(this).find(":selected");

        <?php if($adasub){ ?>

            $("#subvarian").html($("#sub_"+$(this).val()).html());
            $("#variasi").val(0);
            $("#stok").html("<?=$totalstok?>");

        <?php }else{ ?>

            $("#variasi").val(selected.data("variasi"));

            $("#jumlah").attr("max", selected.data("stok"));

            $("#stok").html(selected.data("stok"));

            $("#harga").val(selected.data("harga"));

        <?php } ?>

    });


    // ketika sub varian dipilih
    $("#subvarian").on("change", function(){

        $("#jumlah").val(1);

        let selected = $(this).find(":selected");

        $("#variasi").val(selected.data("variasi"));

        $("#jumlah").attr("max", selected.data("stok"));

        $("#harga").val(selected.data("harga"));

        $("#stok").html(selected.data("stok"));

    });

});

$("#atcart").on("submit", function(e){

    e.preventDefault();

    if(variasi === true && $("#variasi").val() == 0){

        Swal.fire(
            "Pilih Varian",
            "Pilih varian produk terlebih dahulu sebelum menambahkan ke keranjang",
            "warning"
        );

        return;
    }

    let btn = $("#submit");
    let original = btn.html();

    btn.html("<i class='fas fa-compact-disc fa-spin'></i> Memproses...");

    let datar = $(this).serialize();

    // tambah csrf token
    datar += "&" + $("#names").val() + "=" + $("#tokens").val();

    $.ajax({

        url: "<?= site_url('assync/prosesbeli') ?>",
        type: "POST",
        data: datar,
        dataType: "json",

        success: function(data){

            updateToken(data.token);

            closeatc();

            btn.html(original);

            if(data.success){

                // fbq('track','AddToCart',{
                //     content_ids: "<?=$prod->id?>",
                //     content_type: "<?=$kategoriNama?>",
                //     content_name: "<?=$prod->nama?>",
                //     currency: "IDR",
                //     value: data.total
                // });

                updateKeranjang();

                Swal.fire(
                    "<?=$prod->nama?>",
                    "Berhasil ditambahkan ke keranjang",
                    "success"
                );

            }else{

                Swal.fire(
                    "Gagal",
                    "Tidak dapat memproses pesanan\n" + data.msg,
                    "error"
                );

            }

        },

        error: function(){

            btn.html(original);

            Swal.fire(
                "Server Error",
                "Terjadi kesalahan pada server",
                "error"
            );

        }

    });

});
</script>