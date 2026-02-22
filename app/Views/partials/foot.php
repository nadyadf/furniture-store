<footer class="fixed-bottom bg-white border-top">
  <div class="container-fluid">
    <div class="row text-center">

      <div class="col py-2">
        <a href="<?= site_url() ?>" class="text-decoration-none text-dark small d-flex flex-column align-items-center">
          <span class="fs-4">🏠</span>
        </a>
      </div>

      <div class="col py-2">
        <a href="<?= site_url('akun') ?>" class="text-decoration-none text-dark small d-flex flex-column align-items-center">
          <span class="fs-4">👤</span>
        </a>
      </div>

      <div class="col py-2 position-relative">
        <a href="<?= site_url('keranjang') ?>" class="text-decoration-none text-dark small d-flex flex-column align-items-center">
          <span class="fs-4 position-relative">
            🛒
            <span class="badge-cart position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              0
            </span>
          </span>
        </a>
      </div>

      <div class="col py-2">
        <a href="<?= site_url('chat') ?>" class="text-decoration-none text-dark small d-flex flex-column align-items-center">
          <span class="fs-4">💬</span>
        </a>
      </div>

    </div>
  </div>
</footer>