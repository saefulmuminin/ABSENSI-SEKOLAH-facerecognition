<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\GuruModel;
use App\Models\PresensiGuruModel;

class FaceRecognitionGuru extends BaseController
{
    protected $guruModel;
    protected $presensiGuruModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->presensiGuruModel = new PresensiGuruModel();
    }

    public function index($t = 'Masuk')
    {
        $data = [
            'waktu' => $t,
            'title' => 'Absensi Guru Berbasis Pengenalan Wajah',
            'guruList' => $this->guruModel->getAllGuru() // Ambil daftar guru dari model
        ];
        return view('face_recognition_guru/index', $data);
    }

    public function cekWajahGuru()
    {
        try {
            // Ambil hasil pengenalan wajah dari POST (NUPTK guru)
            $recognizedUser = $this->request->getPost('nuptk'); // NUPTK guru yang dikenali
            $waktuAbsen = $this->request->getPost('waktu'); // Waktu absen (masuk/pulang)

            // Debug: Tampilkan data yang diterima
            log_message('debug', 'NUPTK yang diterima: ' . $recognizedUser);
            log_message('debug', 'Waktu absen: ' . $waktuAbsen);

            if (!$recognizedUser) {
                return $this->response->setJSON(['error' => 'Wajah tidak dikenali']);
            }

            // Cek data guru berdasarkan NUPTK
            $result = $this->guruModel->where('nuptk', $recognizedUser)->first();

            // Debug: Tampilkan hasil pencarian guru
            log_message('debug', 'Hasil pencarian guru: ' . print_r($result, true));

            if (empty($result)) {
                return $this->response->setJSON(['error' => 'Data guru tidak ditemukan']);
            }

            // Validasi nilai waktu absen
            if (!in_array($waktuAbsen, ['masuk', 'pulang'])) {
                return $this->response->setJSON(['error' => 'Waktu absen tidak valid. Harap gunakan "masuk" atau "pulang".']);
            }

            // Proses absensi
            switch ($waktuAbsen) {
                case 'masuk':
                    return $this->absenMasukGuru($result);
                case 'pulang':
                    return $this->absenPulangGuru($result);
                default:
                    return $this->response->setJSON(['error' => 'Data tidak valid']);
            }
        } catch (\Exception $e) {
            // Tangkap error dan kirim respons error ke client
            log_message('error', 'Error in cekWajahGuru: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
        }
    }

    private function absenMasukGuru($result)
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        // Gunakan id_guru dari hasil pencarian guru
        $idGuru = $result['id_guru'];

        // Cek apakah guru sudah absen hari ini
        $sudahAbsen = $this->presensiGuruModel->getPresensiByIdGuruTanggal($idGuru, $date);
        if ($sudahAbsen) {
            return $this->response->setJSON(['error' => 'Anda sudah absen hari ini']);
        }

        // Simpan data absensi masuk
        $this->presensiGuruModel->absenMasuk($idGuru, $date, $time);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Absensi masuk berhasil',
            'data' => $result
        ]);
    }

    private function absenPulangGuru($result)
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        // Gunakan id_guru dari hasil pencarian guru
        $idGuru = $result['id_guru'];

        // Cek apakah guru sudah absen masuk hari ini
        $sudahAbsen = $this->presensiGuruModel->getPresensiByIdGuruTanggal($idGuru, $date);
        if (!$sudahAbsen) {
            return $this->response->setJSON(['error' => 'Anda belum absen masuk hari ini']);
        }

        // Cek apakah guru sudah absen pulang hari ini
        if (!empty($sudahAbsen['jam_keluar'])) {
            return $this->response->setJSON(['error' => 'Anda sudah absen pulang hari ini']);
        }

        // Validasi waktu absen pulang (contoh: hanya boleh setelah pukul 12:00 siang)
        $waktuPulangMinimum = '12:00:00'; // Batas waktu minimal untuk absen pulang
        if ($time < $waktuPulangMinimum) {
            return $this->response->setJSON(['error' => 'Absen pulang hanya bisa dilakukan setelah pukul ' . $waktuPulangMinimum]);
        }

        // Update data absensi pulang
        $this->presensiGuruModel->absenKeluar($sudahAbsen['id_presensi'], $time);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Absensi pulang berhasil',
            'data' => $result
        ]);
    }
}