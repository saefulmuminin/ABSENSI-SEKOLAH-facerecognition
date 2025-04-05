<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
   protected $allowedFields = [
      'nuptk',
      'nama_guru',
      'jenis_kelamin',
      'alamat',
      'no_hp',
      'unique_code',
      'image'
   ];

   protected $table = 'tb_guru';

   protected $primaryKey = 'id_guru';

   public function cekGuru(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getAllGuru()
   {
      return $this->orderBy('nama_guru')->findAll();
   }

   public function getGuruById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function saveGuru($idGuru, $nuptk, $namaGuru, $jenisKelamin, $alamat, $noHp, $imagePath = null)
   {
      return $this->save([
         $this->primaryKey => $idGuru,
         'nuptk' => $nuptk,
         'nama_guru' => $namaGuru,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'image' => json_encode($imagePath),
         'unique_code' => sha1($namaGuru . md5($nuptk . $namaGuru . $noHp)) . substr(sha1($nuptk . rand(0, 100)), 0, 24)
      ]);
   }
   public function getAllNUMPTK()
   {
      $query = $this->db->table('tb_guru')->select('numptk')->get();
      $result = $query->getResultArray();
      $numptkList = array_column($result, 'numptk');
      return $numptkList;
   }

   // public function getAllGuru()
   // {
   //    // Query untuk mengambil NIS dan nama siswa
   //    $query = $this->db->table('tb_guru')->select('numptk, nama_guru')->get();
   //    return $query->getResultArray();
   // }

   public function getGuruByNUMPTK($numptk)
   {
      // Debug: Tampilkan NIS yang dicari
      log_message('debug', 'Mencari Guru dengan NIS: ' . $numptk);

      // Cari Guru berdasarkan NIS
      $result = $this->where('nis', $numptk)->first();

      // Debug: Tampilkan hasil pencarian
      log_message('debug', 'Hasil pencarian: ' . print_r($result, true));

      if (!$result) {
         throw new \Exception('Guru dengan numptk ' . $numptk . ' tidak ditemukan');
      }

      return $result;
   }
}
