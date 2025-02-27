<?php

namespace App\Controllers\Admin;

use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataSiswa extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;
   protected JurusanModel $jurusanModel;
   protected string $imagePath;

   protected $siswaValidationRules = [
      'nis' => [
         'rules' => 'required|max_length[20]|min_length[4]',
         'errors' => [
            'required' => 'NIS harus diisi.',
            'min_length' => 'Panjang NIS minimal 4 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_kelas' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Kelas harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]',
   ];

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();
      $this->jurusanModel = new JurusanModel();
      $this->imagePath = FCPATH . 'uploads/siswa_images/';

      // Membuat folder jika belum ada
      if (!is_dir($this->imagePath)) {
         mkdir($this->imagePath, 0777, true);
      }
   }

   public function index()
   {
      $data = [
         'title' => 'Data Siswa',
         'ctx' => 'siswa',
         'kelas' => $this->kelasModel->getAllKelas(),
         'jurusan' => $this->jurusanModel->findAll()
      ];

      return view('admin/data/data-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      $kelas = $this->request->getVar('kelas') ?? null;
      $jurusan = $this->request->getVar('jurusan') ?? null;

      $result = $this->siswaModel->getAllSiswaWithKelas($kelas, $jurusan);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-siswa', $data);
   }

   public function formTambahSiswa()
   {
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'ctx' => 'siswa',
         'kelas' => $kelas,
         'title' => 'Tambah Data Siswa',
         'validation' => \Config\Services::validation(),
      ];
      return view('admin/data/create/create-data-siswa', $data);
   }

   public function saveSiswa()
   {
      // Validasi input
      if (!$this->validate($this->siswaValidationRules)) {
         return redirect()->to('admin/data/create/create-data-siswa')->withInput()->with('errors', $this->validator->getErrors());
      }

      $nis = $this->request->getPost('nis');
      $namaSiswa = $this->request->getPost('nama');
      $idKelas = intval($this->request->getPost('id_kelas'));
      $jenisKelamin = $this->request->getPost('jk');
      $noHp = $this->request->getPost('no_hp');

      // Buat folder khusus untuk siswa berdasarkan NIS
      $studentFolder = $this->imagePath . $nis . '/';
      if (!is_dir($studentFolder)) {
         mkdir($studentFolder, 0777, true); // Buat folder jika belum ada
      }

      // Proses penyimpanan gambar
      $imageFileNames = [];
      for ($i = 1; $i <= 5; $i++) {
         if ($this->request->getPost("capturedImage$i")) {
            $base64Data = explode(',', $this->request->getPost("capturedImage$i"))[1];
            $imageData = base64_decode($base64Data);
            $fileName = "{$i}.png"; // Nama file: 1.png, 2.png, dst.
            file_put_contents($studentFolder . $fileName, $imageData); // Simpan gambar ke folder siswa
            $imageFileNames[] = $fileName;
         }
      }

      // Simpan data ke database
      $result = $this->siswaModel->save([
         'nis' => $nis,
         'nama_siswa' => $namaSiswa,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'image' => json_encode($imageFileNames),
      ]);

      if ($result) {
         session()->setFlashdata('msg', 'Tambah data berhasil');
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata('msg', 'Gagal menambah data');
      return redirect()->to('admin/data/create/create-data-siswa');
   }

   public function formEditSiswa($id)
   {
      $siswa = $this->siswaModel->getSiswaById($id);
      if (!$siswa) {
         throw new PageNotFoundException("Siswa dengan ID $id tidak ditemukan.");
      }

      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'ctx' => 'siswa',
         'kelas' => $kelas,
         'siswa' => $siswa,
         'title' => 'Edit Data Siswa',
         'validation' => \Config\Services::validation(),
      ];
      return view('admin/data/edit/edit-data-siswa', $data);
   }

   public function updateSiswa()
   {
      $id = $this->request->getPost('id_siswa');

      // Validasi input
      $this->siswaValidationRules['nis']['rules'] = "required|max_length[20]|min_length[4]|is_unique[tb_siswa.nis,id_siswa,{$id}]";
      if (!$this->validate($this->siswaValidationRules)) {
         return redirect()->to("/admin/siswa/edit/$id")->withInput()->with('errors', $this->validator->getErrors());
      }

      $nis = $this->request->getPost('nis');
      $namaSiswa = $this->request->getPost('nama');
      $idKelas = intval($this->request->getPost('id_kelas'));
      $jenisKelamin = $this->request->getPost('jk');
      $noHp = $this->request->getPost('no_hp');

      // Buat folder khusus untuk siswa berdasarkan NIS
      $studentFolder = $this->imagePath . $nis . '/';
      if (!is_dir($studentFolder)) {
         mkdir($studentFolder, 0777, true); // Buat folder jika belum ada
      }

      // Proses penyimpanan gambar (jika ada gambar baru)
      $imageFileNames = [];
      for ($i = 1; $i <= 5; $i++) {
         if ($this->request->getPost("capturedImage$i")) {
            $base64Data = explode(',', $this->request->getPost("capturedImage$i"))[1];
            $imageData = base64_decode($base64Data);
            $fileName = "{$i}.png"; // Nama file: 1.png, 2.png, dst.
            file_put_contents($studentFolder . $fileName, $imageData); // Simpan gambar ke folder siswa
            $imageFileNames[] = $fileName;
         }
      }

      // Jika tidak ada gambar baru, gunakan gambar lama
      if (empty($imageFileNames)) {
         $siswa = $this->siswaModel->getSiswaById($id);
         $imageFileNames = json_decode($siswa['image'], true);
      }

      // Update data ke database
      $result = $this->siswaModel->save([
         'id_siswa' => $id,
         'nis' => $nis,
         'nama_siswa' => $namaSiswa,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'image' => json_encode($imageFileNames), // Simpan nama file gambar sebagai JSON
      ]);

      if ($result) {
         session()->setFlashdata('msg', 'Data siswa berhasil diperbarui');
      } else {
         session()->setFlashdata('msg', 'Gagal memperbarui data');
      }

      return redirect()->to('/admin/siswa');
   }

   public function delete($id)
   {
      $siswa = $this->siswaModel->getSiswaById($id);
      if (!$siswa) {
         throw new PageNotFoundException("Siswa dengan ID $id tidak ditemukan.");
      }

      // Hapus folder dan gambar terkait
      $studentFolder = $this->imagePath . $siswa['nis'] . '/';
      if (is_dir($studentFolder)) {
         $images = json_decode($siswa['image'], true);
         foreach ($images as $image) {
            if (file_exists($studentFolder . $image)) {
               unlink($studentFolder . $image); // Hapus gambar
            }
         }
         rmdir($studentFolder); // Hapus folder
      }

      // Hapus data dari database
      $this->siswaModel->delete($id);

      session()->setFlashdata('msg', 'Data siswa berhasil dihapus');
      return redirect()->to('/admin/siswa');
   }
}
