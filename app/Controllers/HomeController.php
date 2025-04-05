<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\GuruModel;

class HomeController extends BaseController
{
    protected $siswaModel;
    protected $guruModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
    }

    /**
     * Menampilkan halaman beranda (homepage).
     */
    public function index()
    {
        // Ambil data siswa dan guru
        $dataSiswa = $this->siswaModel->getAllSiswa();
        $dataGuru = $this->guruModel->getAllGuru();

        // Data yang akan dikirim ke view
        $data = [
            'title' => 'Selamat Datang di Sistem Absensi Sekolah',
            'siswa' => $dataSiswa,
            'guru' => $dataGuru,
        ];

        // Tampilkan view home dengan data yang telah disiapkan
        return view('home/index', $data);
    }
}