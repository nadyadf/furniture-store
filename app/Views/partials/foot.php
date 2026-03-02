<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
</script>

