<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<input type="hidden" id="names" value="<?= csrf_token() ?>">
<input type="hidden" id="tokens" value="<?= csrf_hash() ?>">

<div class="modal fade" id="modalatc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title font-medium">
                    <i class="fa fa-shopping-basket text-warning"></i> 
                    &nbsp;Tambah ke keranjang
                </h5>

                <button 
                    type="button" 
                    class="btn-close" 
                    data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body">
                <i class="fas fa-spin fa-compact-disc text-success"></i> 
                &nbsp;Loading...
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
function signoutNow() {
  Swal.fire({
    title: "Logout",
    text: "Yakin akan logout dari Akun Anda?",
    icon: "warning",
    showDenyButton: true,
    confirmButtonText: "Oke",
    denyButtonText: "Batal"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('logoutForm').submit();
    }
  });
}

function addtocart(id){
    var modal = new bootstrap.Modal(document.getElementById('modalatc'));
    modal.show();
    $("#modalatc .modal-body").load("<?= site_url('home/formatc') ?>/" + id);
}

function updateToken(token){
    $("#tokens, .tokens").val(token);
}

function closeatc(){
    $("#modalatc").modal("hide");
}

function updateKeranjang(){
    let jum = parseInt($(".jmlkeranjang").text()) || 0;
    jum++;

    $(".jmlkeranjang").text(jum);
}
</script>

