<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;

class FaceRecognition extends BaseController
{
    protected SiswaModel $siswaModel;
    protected PresensiSiswaModel $presensiSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
    }

    public function index($t = 'Masuk')
    {
        $data = [
            'waktu' => $t,
            'title' => 'Absensi Siswa Berbasis Pengenalan Wajah',
            'siswaList' => $this->siswaModel->getAllSiswa() // Ambil daftar siswa dari model
        ];
        return view('face_recognition/index', $data);
    }

    public function cekWajah()
    {
        try {
            // Ambil hasil pengenalan wajah dari POST (NIS siswa)
            $recognizedUser = $this->request->getPost('nis'); // NIS siswa yang dikenali
            $waktuAbsen = $this->request->getPost('waktu'); // Waktu absen (masuk/pulang)

            // Debug: Tampilkan data yang diterima
            log_message('debug', 'NIS yang diterima: ' . $recognizedUser);
            log_message('debug', 'Waktu absen: ' . $waktuAbsen);

            if (!$recognizedUser) {
                return $this->response->setJSON(['error' => 'Wajah tidak dikenali']);
            }

            // Cek data siswa berdasarkan NIS
            $result = $this->siswaModel->getSiswaByNIS($recognizedUser);

            // Debug: Tampilkan hasil pencarian siswa
            log_message('debug', 'Hasil pencarian siswa: ' . print_r($result, true));

            if (empty($result)) {
                return $this->response->setJSON(['error' => 'Data siswa tidak ditemukan']);
            }

            // Validasi nilai waktu absen
            if (!in_array($waktuAbsen, ['masuk', 'pulang'])) {
                return $this->response->setJSON(['error' => 'Waktu absen tidak valid. Harap gunakan "masuk" atau "pulang".']);
            }

            // Proses absensi
            switch ($waktuAbsen) {
                case 'masuk':
                    return $this->absenMasuk($result);
                case 'pulang':
                    return $this->absenPulang($result);
                default:
                    return $this->response->setJSON(['error' => 'Data tidak valid']);
            }
        } catch (\Exception $e) {
            // Tangkap error dan kirim respons error ke client
            log_message('error', 'Error in cekWajah: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
        }
    }

    public function absenMasuk($result)
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        // Gunakan id_siswa dari hasil pencarian siswa
        $idSiswa = $result['id_siswa'];
        $idKelas = $result['id_kelas'];

        // Cek apakah siswa sudah absen hari ini
        $sudahAbsen = $this->presensiSiswaModel->cekAbsenHariIni($idSiswa, $date);
        if ($sudahAbsen) {
            return $this->response->setJSON(['error' => 'Anda sudah absen hari ini']);
        }

        // Simpan data absensi masuk
        $this->presensiSiswaModel->absenMasuk($idSiswa, $date, $time, $idKelas);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Absensi masuk berhasil',
            'data' => $result
        ]);
    }

    public function absenPulang($result)
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        // Gunakan id_siswa dari hasil pencarian siswa
        $idSiswa = $result['id_siswa'];

        // Cek apakah siswa sudah absen masuk hari ini
        $sudahAbsen = $this->presensiSiswaModel->cekAbsenHariIni($idSiswa, $date);
        if (!$sudahAbsen) {
            return $this->response->setJSON(['error' => 'Anda belum absen masuk hari ini']);
        }

        // Cek apakah siswa sudah absen pulang hari ini
        if (!empty($sudahAbsen['waktu_pulang'])) {
            return $this->response->setJSON(['error' => 'Anda sudah absen pulang hari ini']);
        }

        // Validasi waktu absen pulang (contoh: hanya boleh setelah pukul 12:00 siang)
        $waktuPulangMinimum = '12:00:00'; // Batas waktu minimal untuk absen pulang
        if ($time < $waktuPulangMinimum) {
            return $this->response->setJSON(['error' => 'Absen pulang hanya bisa dilakukan setelah pukul ' . $waktuPulangMinimum]);
        }

        // Update data absensi pulang
        $this->presensiSiswaModel->absenKeluar($sudahAbsen['id_presensi'], $time);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Absensi pulang berhasil',
            'data' => $result
        ]);
    }
}
