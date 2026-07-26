(function($) {

    $('.btn-num-product-down').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();
        var numProduct = Number($(this).next().val());
        if(numProduct > 1) $(this).next().val(numProduct - 1).trigger("change");
    });

    $('.btn-num-product-up').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();
        var numProduct = Number($(this).prev().val());
        $(this).prev().val(numProduct + 1).trigger("change");
    });

  $(document).ready(function() {
    // Event ketika ikon copy diklik
    $(document).on('click', '.clip', function() {
        // 1. Ambil teks dari atribut data-clipboard-text
        let textToCopy = $(this).data('clipboard-text'); 

        if (textToCopy) {
            // 2. Jalankan fungsi copy bawaan browser
            navigator.clipboard.writeText(textToCopy).then(() => {
                // 3. Tampilkan notifikasi sukses (bisa pakai SweetAlert yang sudah kamu punya)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disalin!',
                    text: `Nomor Invoice ${textToCopy} telah disalin ke clipboard.`,
                    showConfirmButton: false,
                    timer: 1500
                });
            }).catch(err => {
                console.error('Gagal menyalin teks: ', err);
            });
        }
    });
});

})(jQuery);