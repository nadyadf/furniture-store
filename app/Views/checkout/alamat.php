<form id="alamat">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="section p-4 mb-3">
                <input type="hidden" id="tujuan" value="" name="tujuan" />
                <div class="mb-2 alamatform">
                <b>Alamat Pengiriman</b>
                </div>
                <div class="rs1-select2 rs2-select2 mb-2 alamatform">
                <select class="js-select2 form-control" name="alamat" id="idalamat" required>
                    <option value="">- Pilih Alamat Tujuan -</option>
                    <option value="0">+ Tambah Alamat Baru</option>
                    <?php
                    foreach($alamat as $al){
                        //RAJAONGKIR
                        // $kec = $this->func->getKec($al->idkec,"semua");
                        // $idkab = $kec->idkab;
                        $keckab = $al->kecamatan.", ".$al->kabupaten;
                        echo '<option value="'.$al->id.'" data-tujuan="'.$al->idkec.'">'.strtoupper(strtolower($al->judul.' - '.$al->nama)).' ('.$keckab.')</option>';
                    }
                    ?>
                </select>
                <div class="dropDownSelect2"></div>
                </div>
                <div class="mb-2">
                <?php
                    foreach($alamat as $al){
                        // $kec = $this->func->getKec($al->idkec,"semua");
                        // $idkab = $kec->idkab;
                        // $kec = $kec->nama;
                        // $kab = $this->func->getKab($idkab,"nama");
                        echo "
                            <div class='alamat section bg-soft py-3 px-4 mt-3' id='alamat_".$al->id."' data-tujuan='".$al->idkec."' style='display:none;'>
                                <b class='text-info'>Nama Penerima :</b><br/>".strtoupper(strtolower($al->nama))."<br/>
                                <b class='text-info'>No HP :</b><br/>".$al->no_hp."<br/>
                                <b class='text-info'>Alamat Lengkap :</b><br/>".strtoupper(strtolower($al->alamat."<br/>".$al->kecamatan.", ".$al->kabupaten))."<br/>KODEPOS ".$al->kodepos."
                            </div>
                        ";
                    }
                ?>
                </div>
                <div class="mb-3 tambahalamat" style="display:none;">
                <b>Tambah Alamat Pengiriman</b>
                </div>
                <div class="tambahalamat" style="display:none;">
                    <div class="mb-2 col-md-10 px-0">
                    <input class="form-control" type="text" name="judul" placeholder="Simpan Sebagai ? Ex : Alamat Rumah, Alamat Kantor, Dll">
                    </div>
                    <div class="mb-2 col-md-8 p-lr-0">
                    <input class="form-control" type="text" name="nama" placeholder="Nama Penerima">
                    </div>
                    <div class="mb-2 col-md-6 px-0">
                    <input class="form-control" type="text" name="nohp" placeholder="No Handphone Penerima">
                    </div>
                    <div class="mb-2">
                    <textarea class="form-control" name="alamatbaru" placeholder="Alamat lengkap"></textarea>
                    </div>
                    <div class="row mx-0">
                    <div class="rs1-select2 rs2-select2 col-md-5 mb-2 px-0">
                        <select class="js-select2 form-control" name="negara" readonly>
                        <option value="ID">Indonesia</option>
                        </select>
                        <div class="dropDownSelect2"></div>
                    </div>
                    <div class="col-md-6 pb-2"></div>
                    <div class="rs1-select2 rs2-select2 col-md-5 mb-2 px-0">
                        <select class="js-select2 form-control" id="prov">
                        <option value="">Provinsi</option>
                        <?php
                            foreach($provinsi as $pv){
                            echo '<option value="'.$pv->id.'">'.$pv->nama.'</option>';
                            }
                        ?>
                        </select>
                        <div class="dropDownSelect2"></div>
                    </div>
                    <div class="col-md-1 pb-2"></div>
                    <div class="rs1-select2 rs2-select2 col-md-5 mb-2 px-0">
                        <select class="js-select2 form-control" id="kab">
                        <option value="">Kabupaten</option>
                        </select>
                        <div class="dropDownSelect2"></div>
                    </div>
                    <div class="col-md-1 pb-2"></div>
                    <div class="rs1-select2 rs2-select2 col-md-5 mb-2 px-0">
                        <select class="js-select2 form-control" id="kec" name="idkec">
                        <option value="">Kecamatan</option>
                        </select>
                        
                    </div>
                    <div class="col-md-1 pb-2"></div>
                    <div class="mb-2 col-md-5 px-0">
                        <input class="form-control" type="number" name="kodepos" placeholder="Kode POS">
                    </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="text-center pt-4">
        <button type="submit" class="btn btn-lg btn-primary">SELANJUTNYA &nbsp;<i class="fas fa-chevron-right"></i></button>
    </div>
</form>

<script>
    $(function(){
        $("#idalamat").change(function(){
            var idalamat = $(this).val();
            var tujuan = $("#alamat_"+idalamat).data('tujuan');

            $(".alamat").hide();
            if($(this).val() == ""){
                $(".tambahalamat").hide();
                $(".tambahalamat input,.tambahalamat textarea").prop("required",false);
            }else if($(this).val() == 0){
                $(".tambahalamat").show();
                $(".tambahalamat input,.tambahalamat textarea").prop("required",true);
                if($("#kab").val() != ""){
                    $("#tujuan").val($("#kab").val());
                }
            }else if($(this).val() > 0){
                $("#alamat_"+idalamat).show();
                $(".tambahalamat").hide();
                $(".tambahalamat input,.tambahalamat textarea").prop("required",false);
            }
        });

        $("#prov").change(function () {

            $("#kab").html("<option>Loading...</option>");

            $.post(
                "<?= site_url('assync/getkab') ?>",
                {
                    id: $(this).val(),
                    [$("#names").val()]: $("#tokens").val()
                },
                function(response){

                    updateToken(response.token);

                    $("#kab").html(response.html);
                },
                "json"
            );

        });

        $("#kab").change(function () {

            $("#kec").html("<option>Loading...</option>");

            $.post(
                "<?= site_url('assync/getkec') ?>",
                {
                    id: $(this).val(),
                    [$("#names").val()]: $("#tokens").val()
                },
                function(response){

                    updateToken(response.token);

                    $("#kec").html(response.html);
                },
                "json"
            );

        });

        $("#alamat").on("submit", function(e){

            e.preventDefault();

            $.ajax({
                url: "<?= site_url('checkout/simpanalamat') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",

                success: function(response){

                    updateToken(response.token);

                    if(response.success){
                        loadKurir();
                        return;
                    }

                    Swal.fire(
                        "Gagal Menyimpan Alamat",
                        response.message,
                        "warning"
                    );
                },

                error: function(){
                    Swal.fire(
                        "Koneksi Bermasalah",
                        "Tidak dapat terhubung ke server.",
                        "error"
                    );
                }
            });
        });
    });
</script>