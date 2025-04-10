<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Scan
// $routes->group('absensi', function ($routes) {
//    // Route untuk menampilkan halaman absensi
//    $routes->get('/', 'AbsensiController::index');

//    // Route untuk mengambil data siswa (digunakan untuk face recognition)
//    $routes->get('getSiswaData', 'AbsensiController::getSiswaData');

//    // Route untuk menangani pengiriman data absensi
//    $routes->post('handleAttendance', 'AbsensiController::handleAttendance');
// });


// Default route for the homepage
$routes->get('/', 'HomeController::index');

// Route for the about page
$routes->get('/about', 'HomeController::about');

// Route for the contact page
$routes->get('/contact', 'HomeController::contact');

$routes->get('masukguru', 'FaceRecognitionGuru::index/masuk'); // http://localhost/ABSENSI-SEKOLAH-facerecognition/masuk
$routes->get('pulangguru', 'FaceRecognitionGuru::index/pulang'); 
$routes->post('/face-recognition/cekWajahGuru', 'FaceRecognitionGuru::cekWajahGuru');


$routes->get('masuk', 'FaceRecognition::index/masuk'); // http://localhost/ABSENSI-SEKOLAH-facerecognition/masuk
$routes->get('pulang', 'FaceRecognition::index/pulang'); 
$routes->post('/face-recognition/cekWajah', 'FaceRecognition::cekWajah'); // Untuk menangani POST request dari JavaScript

$routes->group('admin', function (RouteCollection $routes) {
   // Admin dashboard
   $routes->get('', 'Admin\Dashboard::index');
   $routes->get('dashboard', 'Admin\Dashboard::index');

   // data kelas & jurusan
   $routes->resource('kelas', ['controller' => 'Admin\KelasController']);
   $routes->resource('jurusan', ['controller' => 'Admin\JurusanController']);

   // admin lihat data siswa
   $routes->get('siswa', 'Admin\DataSiswa::index');
   $routes->post('siswa', 'Admin\DataSiswa::ambilDataSiswa');
   // admin tambah data siswa
   $routes->get('siswa/create', 'Admin\DataSiswa::formTambahSiswa');
   $routes->post('siswa/create', 'Admin\DataSiswa::saveSiswa');
   // admin edit data siswa
   $routes->get('siswa/edit/(:any)', 'Admin\DataSiswa::formEditSiswa/$1');
   $routes->post('siswa/edit', 'Admin\DataSiswa::updateSiswa');
   // admin hapus data siswa
   $routes->delete('siswa/delete/(:any)', 'Admin\DataSiswa::delete/$1');


   // admin lihat data guru
   $routes->get('guru', 'Admin\DataGuru::index');
   $routes->post('guru', 'Admin\DataGuru::ambilDataGuru');
   // admin tambah data guru
   $routes->get('guru/create', 'Admin\DataGuru::formTambahGuru');
   $routes->post('guru/create', 'Admin\DataGuru::saveGuru');
   // admin edit data guru
   $routes->get('guru/edit/(:any)', 'Admin\DataGuru::formEditGuru/$1');
   $routes->post('guru/edit', 'Admin\DataGuru::updateGuru');
   // admin hapus data guru
   $routes->delete('guru/delete/(:any)', 'Admin\DataGuru::delete/$1');


   // admin lihat data absen siswa
   $routes->get('absen-siswa', 'Admin\DataAbsenSiswa::index');
   $routes->post('absen-siswa', 'Admin\DataAbsenSiswa::ambilDataSiswa'); // ambil siswa berdasarkan kelas dan tanggal
   $routes->post('absen-siswa/kehadiran', 'Admin\DataAbsenSiswa::ambilKehadiran'); // ambil kehadiran siswa
   $routes->post('absen-siswa/edit', 'Admin\DataAbsenSiswa::ubahKehadiran'); // ubah kehadiran siswa

   $routes->post('tambah-kelas', 'Admin\DataAbsenSiswa::tambahKelas'); // tambah data kelas

   // admin lihat data absen guru
   $routes->get('absen-guru', 'Admin\DataAbsenGuru::index');
   $routes->post('absen-guru', 'Admin\DataAbsenGuru::ambilDataGuru'); // ambil guru berdasarkan tanggal
   $routes->post('absen-guru/kehadiran', 'Admin\DataAbsenGuru::ambilKehadiran'); // ambil kehadiran guru
   $routes->post('absen-guru/edit', 'Admin\DataAbsenGuru::ubahKehadiran'); // ubah kehadiran guru

   // admin generate QR
   $routes->get('generate', 'Admin\GenerateQR::index');
   $routes->post('generate/siswa-by-kelas', 'Admin\GenerateQR::getSiswaByKelas');

   $routes->post('generate/siswa', 'Admin\QRGenerator::generateQrSiswa');
   $routes->post('generate/guru', 'Admin\QRGenerator::generateQrGuru');

   // admin buat laporan
   $routes->get('laporan', 'Admin\GenerateLaporan::index');
   $routes->post('laporan/siswa', 'Admin\GenerateLaporan::generateLaporanSiswa');
   $routes->post('laporan/guru', 'Admin\GenerateLaporan::generateLaporanGuru');

   // superadmin lihat data petugas
   $routes->get('petugas', 'Admin\DataPetugas::index');
   $routes->post('petugas', 'Admin\DataPetugas::ambilDataPetugas');
   // superadmin tambah data petugas
   $routes->get('petugas/register', 'Admin\DataPetugas::registerPetugas');
   // superadmin edit data petugas
   $routes->get('petugas/edit/(:any)', 'Admin\DataPetugas::formEditPetugas/$1');
   $routes->post('petugas/edit', 'Admin\DataPetugas::updatePetugas');
   // superadmin hapus data petugas
   $routes->delete('petugas/delete/(:any)', 'Admin\DataPetugas::delete/$1');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
   require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
