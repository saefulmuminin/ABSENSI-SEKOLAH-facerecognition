<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Absensi</title>
</head>
<body>
    <h1>Hasil Absensi</h1>
    <?php if (isset($data)): ?>
        <p>Nama: <?= $data['nama_siswa'] ?></p>
        <p>NIS: <?= $data['nis'] ?></p>
        <p>Waktu: <?= $data['waktu'] ?></p>
        <p>Status: Berhasil</p>
    <?php else: ?>
        <p>Tidak ada data absensi.</p>
    <?php endif; ?>
    <a href="/face-recognition/masuk">Kembali</a>
</body>
</html>