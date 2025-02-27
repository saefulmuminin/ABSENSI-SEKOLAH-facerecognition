<?php

namespace App\Controllers;



class HomeController extends BaseController
{
    

    /**
     * Menampilkan halaman beranda (homepage).
     */
    public function index()
    {
        // Data yang akan dikirim ke view
        $data = [
            'title' => 'Selamat Datang di Sistem Absensi Sekolah',
        ];

        // Tampilkan view home dengan data yang telah disiapkan
        return view('home/index', $data);
    }

    /**
     * Menampilkan halaman tentang (about).
     */
    public function about()
    {
        $data = [
            'title' => 'Tentang Kami',
        ];

        return view('home/about', $data);
    }

    /**
     * Menampilkan halaman kontak (contact).
     */
    public function contact()
    {
        $data = [
            'title' => 'Hubungi Kami',
        ];

        return view('home/contact', $data);
    }
}