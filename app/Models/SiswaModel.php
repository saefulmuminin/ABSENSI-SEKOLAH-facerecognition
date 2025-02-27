<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'nis',
         'nama_siswa',
         'id_kelas',
         'jenis_kelamin',
         'no_hp',
         'image',
         'unique_code'
      ];
   }

   protected $table = 'tb_siswa';

   protected $primaryKey = 'id_siswa';

   public function cekSiswa(string $unique_code)
   {
      $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_jurusan.id = tb_kelas.id_jurusan',
         'LEFT'
      );
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getSiswaById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllSiswaWithKelas($kelas = null, $jurusan = null)
   {
      $query = $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_kelas.id_jurusan = tb_jurusan.id',
         'LEFT'
      );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas, 'jurusan' => $jurusan]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_siswa')->findAll();
   }

   public function getSiswaByKelas($id_kelas)
   {
      return $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )
         ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
         ->where(['tb_siswa.id_kelas' => $id_kelas])->findAll();
   }

   public function saveSiswa($idSiswa, $nis, $namaSiswa, $idKelas, $jenisKelamin, $noHp, $imagePath = null)
   {
      return $this->save([
         $this->primaryKey => $idSiswa,
         'nis' => $nis,
         'nama_siswa' => $namaSiswa,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'unique_code' => sha1($namaSiswa . md5($nis . $noHp . $namaSiswa)) . substr(sha1($nis . rand(0, 100)), 0, 24),
         'image' => json_encode($imagePath),
      ]);
   }
   public function getAllNIS()
   {
      $query = $this->db->table('tb_siswa')->select('nis')->get();
      $result = $query->getResultArray();
      $nisList = array_column($result, 'nis');
      return $nisList;
   }

   public function getAllSiswa()
   {
      // Query untuk mengambil NIS dan nama siswa
      $query = $this->db->table('tb_siswa')->select('nis, nama_siswa')->get();
      return $query->getResultArray();
   }

   public function getSiswaByNIS($nis)
   {
      // Debug: Tampilkan NIS yang dicari
      log_message('debug', 'Mencari siswa dengan NIS: ' . $nis);

      // Cari siswa berdasarkan NIS
      $result = $this->where('nis', $nis)->first();

      // Debug: Tampilkan hasil pencarian
      log_message('debug', 'Hasil pencarian: ' . print_r($result, true));

      if (!$result) {
         throw new \Exception('Siswa dengan NIS ' . $nis . ' tidak ditemukan');
      }

      return $result;
   }
   
}
