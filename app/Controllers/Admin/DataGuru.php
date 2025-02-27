<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataGuru extends BaseController
{
   protected GuruModel $guruModel;
   protected string $imagePath;

   protected $guruValidationRules = [
      'nuptk' => [
         'rules' => 'required|max_length[20]|min_length[16]|is_unique[tb_guru.nuptk]',
         'errors' => [
            'required' => 'NUPTK harus diisi.',
            'is_unique' => 'NUPTK ini telah terdaftar.',
            'min_length' => 'Panjang NUPTK minimal 16 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => ['required' => 'Nama harus diisi']
      ],
      'jk' => [
         'rules' => 'required',
         'errors' => ['required' => 'Jenis kelamin wajib diisi']
      ],
      'no_hp' => [
         'rules' => 'required|numeric|max_length[20]|min_length[5]',
         'errors' => ['required' => 'Nomor HP harus diisi']
      ],
      'capturedImage1' => ['rules' => 'required', 'errors' => ['required' => 'Gambar 1 harus diunggah']],
      'capturedImage2' => ['rules' => 'required', 'errors' => ['required' => 'Gambar 2 harus diunggah']],
      'capturedImage3' => ['rules' => 'required', 'errors' => ['required' => 'Gambar 3 harus diunggah']],
      'capturedImage4' => ['rules' => 'required', 'errors' => ['required' => 'Gambar 4 harus diunggah']],
      'capturedImage5' => ['rules' => 'required', 'errors' => ['required' => 'Gambar 5 harus diunggah']],
   ];

   public function __construct()
   {
      helper(['filesystem']); // Helper untuk manipulasi file
      $this->guruModel = new GuruModel();
      $this->imagePath = FCPATH . 'uploads/guru_images/';

      if (!is_dir($this->imagePath)) {
         mkdir($this->imagePath, 0777, true);
      }
   }

   public function index()
   {
      return view('admin/data/data-guru', [
         'title' => 'Data Guru',
         'ctx' => 'guru',
      ]);
   }

   public function ambilDataGuru()
   {
      return view('admin/data/list-data-guru', [
         'data' => $this->guruModel->getAllGuru(),
         'empty' => empty($this->guruModel->getAllGuru())
      ]);
   }

   public function formTambahGuru()
   {
      return view('admin/data/create/create-data-guru', [
         'ctx' => 'guru',
         'title' => 'Tambah Data Guru'
      ]);
   }

   public function saveGuru()
   {
      $validation = \Config\Services::validation();

      // Validasi input
      if (!$this->validate($this->guruValidationRules)) {
         return redirect()->to('/admin/guru/create')->withInput()->with('errors', $validation->getErrors());
      }

      $nuptk = $this->request->getPost('nuptk');
      $namaGuru = $this->request->getPost('nama');
      $alamat = $this->request->getPost('alamat');
      $jenisKelamin = $this->request->getPost('jk');
      $noHp = $this->request->getPost('no_hp');

      // Buat folder khusus untuk guru berdasarkan NUPTK
      $teacherFolder = $this->imagePath . $nuptk . '/';
      if (!is_dir($teacherFolder)) {
         mkdir($teacherFolder, 0777, true); // Buat folder jika belum ada
      }

      // Proses penyimpanan gambar
      $imageFileNames = [];
      for ($i = 1; $i <= 5; $i++) {
         if ($this->request->getPost("capturedImage$i")) {
            $base64Data = explode(',', $this->request->getPost("capturedImage$i"))[1];
            $imageData = base64_decode($base64Data);
            $fileName = "{$i}.png"; // Nama file: 1.png, 2.png, dst.
            file_put_contents($teacherFolder . $fileName, $imageData); // Simpan gambar ke folder guru
            $imageFileNames[] = $fileName;
         }
      }

      // Simpan data ke database menggunakan method saveGuru dari model
      $result = $this->guruModel->saveGuru(
         null, // ID baru, bisa diisi null
         $nuptk,
         $namaGuru,
         $jenisKelamin,
         $alamat,
         $noHp,
         $imageFileNames
      );

      if ($result) {
         session()->setFlashdata('msg', 'Tambah data berhasil');
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata('msg', 'Gagal menambah data');
      return redirect()->to('/admin/guru/create')->withInput();
   }



   public function formEditGuru($id)
   {
      $guru = $this->guruModel->getGuruById($id);
      if (!$guru) {
         throw new PageNotFoundException("Data guru dengan ID $id tidak ditemukan.");
      }

      return view('admin/data/edit/edit-data-guru', [
         'data' => $guru,
         'ctx' => 'guru',
         'title' => 'Edit Data Guru',
      ]);
   }

   public function updateGuru()
   {
      $idGuru = $this->request->getPost('id');
      $guruLama = $this->guruModel->getGuruById($idGuru);
      $nuptk = $this->request->getPost('nuptk');
      $namaGuru = $this->request->getPost('nama');
      $alamat = $this->request->getPost('alamat');
      $jenisKelamin = $this->request->getPost('jk');
      $noHp = $this->request->getPost('no_hp');

      $teacherFolder = $this->imagePath . $nuptk . '/';
      if (!is_dir($teacherFolder)) {
         mkdir($teacherFolder, 0777, true);
      }

      // Proses penyimpanan gambar
      $imageFileNames = [];
      for ($i = 1; $i <= 5; $i++) {
         if ($this->request->getPost("capturedImage$i")) {
            $base64Data = explode(',', $this->request->getPost("capturedImage$i"))[1];
            $imageData = base64_decode($base64Data);
            $fileName = "{$i}.png"; // Nama file: 1.png, 2.png, dst.
            file_put_contents($teacherFolder . $fileName, $imageData); // Simpan gambar ke folder guru
            $imageFileNames[] = $fileName;
         }
      }

      // Jika tidak ada gambar baru, gunakan gambar lama
      if (empty($imageFileNames)) {
         $imageFileNames = json_decode($guruLama['image'], true);
      }

      // Simpan data ke database menggunakan method saveGuru dari model
      $result = $this->guruModel->saveGuru(
         $idGuru, // ID yang akan diupdate
         $nuptk,
         $namaGuru,
         $jenisKelamin,
         $alamat,
         $noHp,
         $imageFileNames
      );

      if ($result) {
         session()->setFlashdata('msg', 'Edit data berhasil');
      } else {
         session()->setFlashdata('msg', 'Gagal mengubah data');
      }

      return redirect()->to('/admin/guru');
   }
   public function delete($id)
   {
      $guru = $this->guruModel->getGuruById($id);
      if (!$guru) {
         throw new PageNotFoundException("Guru dengan ID $id tidak ditemukan.");
      }

      // Hapus folder dan gambar terkait
      $teacherFolder = $this->imagePath . $guru['nuptk'] . '/';
      if (is_dir($teacherFolder)) {
         $images = json_decode($guru['image'], true) ?? [];
         if (!empty($images)) {
            foreach ($images as $image) {
               if (file_exists($teacherFolder . $image)) {
                  unlink($teacherFolder . $image); // Hapus gambar
               }
            }
         }
         rmdir($teacherFolder); // Hapus folder
      }

      // Hapus data dari database
      $this->guruModel->delete($id);

      session()->setFlashdata('msg', 'Data guru berhasil dihapus');
      return redirect()->to('/admin/guru');
   }
}
