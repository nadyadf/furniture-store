<!DOCTYPE html>
<html lang="en">
<head>
	<title><?=$nama?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/png" href="<?=base_url("cdn/assets/img/".$set->favicon)?>"/>
	<meta name="google-site-verification" content="G35UyHn6lX6mRzyFws0NJYYxHQp_aejuAFbagRKCL7c" />
	<meta name="description" content="<?=$desc?>" />
	<!--  Social tags      -->
	<meta name="keywords" content="Aplikasi toko online <?=$nama?>">
	<meta name="description" content="<?=$desc?>">
	<!-- Schema.org markup for Google+ -->
	<meta itemprop="name" content="<?=$nama?>">
	<meta itemprop="description" content="<?=$desc?>">
	<meta itemprop="image" content="<?=$img?>">
	<!-- Twitter Card data -->
	<meta name="twitter:card" content="product">
	<meta name="twitter:site" content="@masbil_al">
	<meta name="twitter:title" content="<?=$nama?>">
	<meta name="twitter:description" content="<?=$desc?>">
	<meta name="twitter:creator" content="@masbil_al">
	<meta name="twitter:image" content="<?=$img?>">
	
	
	<meta property="og:title" content="<?=$nama?>" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="<?=$url?>" />
	<meta property="og:image" content="<?=$img?>" />
	<meta property="og:description" content="<?=$desc?>" />

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">



	<!--===============================================================================================-->

	

  <body>
    <?= $this->include('partials/head') ?>
    <?= $this->renderSection('content') ?>
		<?= $this->include('partials/foot') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  <script>
    function offsetHero() {
      const nav = document.getElementById('mainNavbar');
      const hero = document.getElementById('promoCarousel');

      if (nav && hero) {
        hero.style.marginTop = nav.offsetHeight + 'px';
      }
    }

    window.addEventListener('load', offsetHero);
    window.addEventListener('resize', offsetHero);
  </script>



    <script>
      document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Carousel(document.querySelector('#promoCarousel'), {
          interval: 5000,
          wrap: true,     // ← looping terus
          pause: false    // tidak berhenti saat hover
        });
      });
    </script>

  </body>

  
 