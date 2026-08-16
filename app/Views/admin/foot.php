<script type="text/javascript">
  function logout() {
    Swal.fire({
      title: "Logout",
      text: "Anda yakin akan keluar dari panel admin?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Keluar",
      cancelButtonText: "Batal",
      customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "<?= site_url('admin/logout') ?>";
      }
    });
  }

  function updateToken(token){
      $("#tokens, .tokens").val(token);
  }

</script>