<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Libraries\enums\Kehadiran;

class PresensiSiswaModel extends Model
{
   protected $table = 'tb_presensi_siswa';
   protected $primaryKey = 'id_presensi';

   protected $allowedFields = [
      'id_siswa',
      'id_kelas',
      'tanggal',
      'jam_masuk',
      'jam_keluar',
      'id_kehadiran',
      'keterangan'
   ];

   public function cekAbsen($nis, $date)
   {
      // Cari id_siswa berdasarkan nis
      $siswa = $this->db->table('tb_siswa')
         ->where('nis', $nis)
         ->get()
         ->getRowArray();

      if (!$siswa) {
         return false; // Siswa tidak ditemukan
      }

      $idSiswa = $siswa['id_siswa'];

      // Cek apakah siswa sudah absen hari ini
      return $this->where(['id_siswa' => $idSiswa, 'tanggal' => $date])->first();
   }
   public function absenMasuk($idSiswa, $date, $time, $idKelas = '')
   {
      // Pastikan id_siswa valid
      if (empty($idSiswa)) {
         throw new \Exception('ID siswa tidak valid.');
      }

      // Cek apakah siswa sudah absen hari ini
      $sudahAbsen = $this->cekAbsen($idSiswa, $date);
      if ($sudahAbsen) {
         throw new \Exception('Siswa sudah absen hari ini.');
      }

      // Simpan data presensi
      return $this->save([
         'id_siswa' => $idSiswa,
         'id_kelas' => $idKelas,
         'tanggal' => $date,
         'jam_masuk' => $time,
         'id_kehadiran' => Kehadiran::Hadir->value,
         'keterangan' => ''
      ]);
   }

   public function absenKeluar($idPresensi, $time)
   {
      // Pastikan id_presensi valid
      if (empty($idPresensi)) {
         throw new \Exception('ID presensi tidak valid.');
      }

      // Update data absensi pulang
      return $this->update($idPresensi, [
         'jam_keluar' => $time,
         'keterangan' => ''
      ]);
   }
   public function getPresensiByIdSiswaTanggal($idSiswa, $date)
   {
      return $this->where(['id_siswa' => $idSiswa, 'tanggal' => $date])->first();
   }

   public function getPresensiByKelasTanggal($idKelas, $tanggal)
   {
      return $this->setTable('tb_siswa')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_siswa AS id_siswa_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_siswa)tb_presensi_siswa",
            "{$this->table}.id_siswa = tb_presensi_siswa.id_siswa_presensi AND tb_presensi_siswa.tanggal = '$tanggal'",
            'left'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
            'left'
         )
         ->where("{$this->table}.id_kelas = $idKelas")
         ->orderBy("nama_siswa")
         ->findAll();
   }

   public function getPresensiByKehadiran(string $idKehadiran, $tanggal)
   {
      $this->join(
         'tb_siswa',
         "tb_presensi_siswa.id_siswa = tb_siswa.id_siswa AND tb_presensi_siswa.tanggal = '$tanggal'",
         'right'
      );

      if ($idKehadiran == '4') {
         $result = $this->findAll();

         $filteredResult = [];

         foreach ($result as $value) {
            if ($value['id_kehadiran'] != ('1' || '2' || '3')) {
               array_push($filteredResult, $value);
            }
         }

         return $filteredResult;
      } else {
         $this->where(['tb_presensi_siswa.id_kehadiran' => $idKehadiran]);
         return $this->findAll();
      }
   }

   public function updatePresensi(
      $idPresensi,
      $idSiswa,
      $idKelas,
      $tanggal,
      $idKehadiran,
      $jamMasuk,
      $jamKeluar,
      $keterangan
   ) {
      $presensi = $this->getPresensiByIdSiswaTanggal($idSiswa, $tanggal);

      $data = [
         'id_siswa' => $idSiswa,
         'id_kelas' => $idKelas,
         'tanggal' => $tanggal,
         'id_kehadiran' => $idKehadiran,
         'keterangan' => $keterangan ?? $presensi['keterangan'] ?? ''
      ];

      if ($idPresensi != null) {
         $data[$this->primaryKey] = $idPresensi;
      }

      if ($jamMasuk != null) {
         $data['jam_masuk'] = $jamMasuk;
      }

      if ($jamKeluar != null) {
         $data['jam_keluar'] = $jamKeluar;
      }

      return $this->save($data);
   }
   public function getPresensiById(string $idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getAbsenByNIS($nis)
   {
      return $this->select('tb_presensi_siswa.*')
         ->join('tb_siswa', 'tb_siswa.id_siswa = tb_presensi_siswa.id_siswa')
         ->where(['tb_siswa.nis' => $nis])
         ->findAll();
   }
   public function cekAbsenHariIni($idSiswa, $tanggal)
   {
      return $this->where('id_siswa', $idSiswa)
         ->where('tanggal', $tanggal)
         ->first();
   }
}
