<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-header card-header-primary">
                  <h4 class="card-title"><b>Form Tambah Siswa</b></h4>
               </div>
               <div class="card-body mx-5 my-3">
                  <form action="<?= base_url('admin/siswa/create'); ?>" method="post" enctype="multipart/form-data">
                     <?= csrf_field() ?>
                     <?php $validation = \Config\Services::validation(); ?>

                     <?php if (session()->getFlashdata('msg')) : ?>
                        <div class="pb-2">
                           <div class="alert alert-danger">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                 <i class="material-icons">close</i>
                              </button>
                              <?= session()->getFlashdata('msg') ?>
                           </div>
                        </div>
                     <?php endif; ?>

                     <div class="form-group mt-4">
                        <label for="nis">NIS</label>
                        <input type="text" id="nis" class="form-control <?= $validation->getError('nis') ? 'is-invalid' : ''; ?>" name="nis" placeholder="1234" value="<?= old('nis') ?? '' ?>">
                        <div class="invalid-feedback">
                           <?= $validation->getError('nis'); ?>
                        </div>
                     </div>

                     <div class="form-group mt-4">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" class="form-control <?= $validation->getError('nama') ? 'is-invalid' : ''; ?>" name="nama" placeholder="Your Name" value="<?= old('nama') ?? '' ?>" required>
                        <div class="invalid-feedback">
                           <?= $validation->getError('nama'); ?>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6">
                           <label for="kelas">Kelas</label>
                           <select class="custom-select <?= $validation->getError('id_kelas') ? 'is-invalid' : ''; ?>" id="kelas" name="id_kelas">
                              <option value="">--Pilih kelas--</option>
                              <?php foreach ($kelas as $value) : ?>
                                 <option value="<?= $value['id_kelas']; ?>" <?= old('id_kelas') == $value['id_kelas'] ? 'selected' : ''; ?>>
                                    <?= $value['kelas'] . ' ' . $value['jurusan']; ?>
                                 </option>
                              <?php endforeach; ?>
                           </select>
                           <div class="invalid-feedback">
                              <?= $validation->getError('id_kelas'); ?>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <label for="jk">Jenis Kelamin</label>
                           <div class="form-check form-control pt-0 mb-1 <?= $validation->getError('jk') ? 'is-invalid' : ''; ?>" id="jk">
                              <div class="row">
                                 <div class="col-auto">
                                    <input class="form-check" type="radio" name="jk" id="laki" value="1" <?= old('jk') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="laki">Laki-laki</label>
                                 </div>
                                 <div class="col-auto">
                                    <input class="form-check" type="radio" name="jk" id="perempuan" value="2" <?= old('jk') == '2' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="perempuan">Perempuan</label>
                                 </div>
                              </div>
                           </div>
                           <div class="invalid-feedback">
                              <?= $validation->getError('jk'); ?>
                           </div>
                        </div>
                     </div>

                     <div class="form-group mt-5">
                        <label for="hp">No HP</label>
                        <input type="number" id="hp" name="no_hp" class="form-control <?= $validation->getError('no_hp') ? 'is-invalid' : ''; ?>" value="<?= old('no_hp') ?? '' ?>">
                        <div class="invalid-feedback">
                           <?= $validation->getError('no_hp'); ?>
                        </div>
                     </div>

                     <div>
                        <div class="form-title-image">
                           <p>Ambil gambar untuk sampel face recognition
                           <p>
                        </div>
                        <div id="open_camera" class="image-box" onclick="takeMultipleImages()">
                        <img class="img-fluid w-25 custom-img" src="<?= base_url('public/assets/img/dslr_photography_camera_flat_style.jpg'); ?>" alt="Default Image">
                        </div>
                        <div id="multiple-images">
                        </div>
                     </div>
                     <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   let video = document.getElementById('video');
   let canvas = document.createElement('canvas');
   let context = canvas.getContext('2d');
   let capturedImages = [];

   // Start video stream
   navigator.mediaDevices.getUserMedia({
         video: true
      })
      .then(stream => {
         video.srcObject = stream;
      })
      .catch(err => {
         console.error("Error accessing the camera: ", err);
      });

   // Capture 5 images
   function captureImages() {
      capturedImages = [];
      for (let i = 1; i <= 5; i++) {
         setTimeout(() => {
            context.drawImage(video, 0, 0, 320, 240);
            let imageData = canvas.toDataURL('image/png');
            capturedImages.push(imageData);
            document.getElementById(`capturedImage${i}`).value = imageData;
            document.getElementById('multiple-images').innerHTML += `<img src="${imageData}" width="100" height="100">`;
         }, i * 1000); // Ambil gambar setiap 1 detik
      }
   }
</script>
<?= $this->endSection() ?>