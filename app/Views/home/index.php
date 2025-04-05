<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Absensi - Face recognition </title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?= base_url('public/assets/images.jpeg') ?>" rel="icon">
  <link href="<?= base_url('public/assets/images.jpeg') ?>" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= base_url('public/assets2/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('public/assets2/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= base_url('public/assets2/vendor/aos/aos.css') ?>" rel="stylesheet">
  <link href="<?= base_url('public/assets2/vendor/glightbox/css/glightbox.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('public/assets2/vendor/swiper/swiper-bundle.min.css') ?>" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="public/assets2/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: FlexStart
  * Template URL: https://bootstrapmade.com/flexstart-bootstrap-startup-template/
  * Updated: Nov 01 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="#hero" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="public/assets/images.jpeg" alt="">
        <h5 class="sitename">Daruhsaadah</h5>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home<br></a></li>
          <li><a href="#tentang">Tentang kami</a></li>
          <li><a href="#guru">Data Guru</a></li>
          <li><a href="#siswa">Data siswa</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted flex-md-shrink-0" href="admin\dashboard">Dashboard</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h1 data-aos="fade-up">Solusi Modern untuk Sistem Absensi</h1>
            <p data-aos="fade-up" data-aos-delay="100">
              Kami menghadirkan teknologi pengenalan wajah untuk sistem absensi yang cepat, aman, dan akurat.
            </p>
            <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
              <a href="http://localhost/ABSENSI-SEKOLAH-facerecognition/masuk" class="btn-get-started">Mulai Sekarang <i class="bi bi-arrow-right"></i></a>
              <a href="https://youtu.be/r-4A9SnCDd0?si=Ca7dfGqicf1XozmE"
                class="glightbox btn-watch-video d-flex align-items-center justify-content-center ms-0 ms-md-4 mt-4 mt-md-0">
                <i class="bi bi-play-circle"></i><span>Lihat Video</span>
              </a>
            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
            <img src="public/assets2/img/face.png" class="img-fluid animated" alt="Absensi Face Recognition">
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="tentang" class="about section">

      <div class="container" data-aos="fade-up">
        <div class="row gx-0">

          <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
            <div class="content">
              <h3>Tentang Kami</h3>
              <h2>Sistem Absensi Berbasis Pengenalan Wajah yang Akurat dan Aman</h2>
              <p>
                Kami menghadirkan solusi absensi modern dengan teknologi pengenalan wajah yang cepat, aman, dan akurat.
                Sistem ini dirancang untuk meningkatkan efisiensi kehadiran di sekolah, kantor, dan institusi lainnya,
                serta meminimalkan risiko kecurangan dalam absensi.
              </p>
              <div class="text-center text-lg-start">
                <a href="#" class="btn-read-more d-inline-flex align-items-center justify-content-center align-self-center">
                  <span>Selengkapnya</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
            <img src="public/assets2/img/about.jpg" class="img-fluid" alt="Tentang Absensi Face Recognition">
          </div>

        </div>
      </div>

    </section><!-- /Tentang Kami -->
    <div class="container section-title" data-aos="fade-up" id="siswa">
      <h2>Data Guru</h2>
      <p>Our talented students</p>
    </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>NUPTK</th>
              <th>Nama</th>
              <th>Jenis Kelamin</th>
              <th>No HP</th>
              <th>Alamat</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; ?>
            <?php foreach ($guru as $g) : ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= $g['nuptk']; ?></td>
                <td><?= $g['nama_guru']; ?></td>
                <td><?= $g['jenis_kelamin']; ?></td>
                <td><?= $g['no_hp']; ?></td>
                <td><?= $g['alamat']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>


    <div class="container section-title mt-3" data-aos="fade-up" id="siswa">
      <h2>Data Siswa</h2>
      <p>Our talented students</p>
    </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>NIM</th>
              <th>Nama</th>
              <th>No HP</th>
              <th>Jenis Kelamin</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($siswa)) : ?>
              <?php $no = 1; ?>
              <?php foreach ($siswa as $s) : ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= $s['nis']; ?></td>
                  <td><?= $s['nama_siswa']; ?></td>
                  <td><?= $s['no_hp']; ?></td>
                  <td><?= $s['jenis_kelamin']; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="5" class="text-center">Tidak ada data siswa.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">FlexStart</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href=“https://themewagon.com>ThemeWagon
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- Vendor JS Files -->
  <script src="<?= base_url('public/assets2/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/php-email-form/validate.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/aos/aos.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/glightbox/js/glightbox.min.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/purecounter/purecounter_vanilla.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/imagesloaded/imagesloaded.pkgd.min.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/isotope-layout/isotope.pkgd.min.js') ?>"></script>
  <script src="<?= base_url('public/assets2/vendor/swiper/swiper-bundle.min.js') ?>"></script>

  <!-- Main JS File -->
  <script src="<?= base_url('public/assets2/js/main.js') ?>"></script>

</body>

</html>