<div class="card-body table-responsive">
   <?php if (!$empty) : ?>
      <table class="table table-hover">
         <thead class="text-primary">
            <th><b>No</b></th>
            <th><b>NIS</b></th>
            <th><b>Nama Siswa</b></th>
            <th><b>Jenis Kelamin</b></th>
            <th><b>Kelas</b></th>
            <th><b>Jurusan</b></th>
            <th><b>No HP</b></th>
            <th><b>Aksi</b></th>
         </thead>
         <tbody>
            <?php $i = 1;
            foreach ($data as $value) : ?>
               <tr>
                  <td><?= $i; ?></td>
                  <td><?= $value['nis']; ?></td>
                  <td><b><?= $value['nama_siswa']; ?></b></td>
                  <td><?= $value['jenis_kelamin']; ?></td>
                  <td><?= $value['kelas']; ?></td>
                  <td><?= $value['jurusan']; ?></td>
                  <td><?= $value['no_hp']; ?></td>
                  <td>
                     <a href="<?= base_url('admin/siswa/edit/' . $value['id_siswa']); ?>" class="btn btn-primary p-2">
                        <i class="material-icons">edit</i> Edit
                     </a>
                     <!-- Tombol untuk membuka modal -->
                     <button type="button" class="btn btn-danger  p-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="material-icons">delete_forever</i> Delete
                     </button>

                     <!-- Modal Konfirmasi Hapus -->
                     <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                           <div class="modal-content">
                              <div class="modal-header bg-danger text-white">
                                 <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                 <button type="button" class="close text-white border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="material-icons">close</i>
                                 </button>
                              </div>
                              <div class="modal-body">
                                 <p class="mb-0">Are you sure you want to delete this item? This action cannot be undone.</p>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                 <form id="deleteForm" action="<?= base_url('admin/siswa/delete/' . $value['id_siswa']); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>
                  </td>
               </tr>
            <?php $i++;
            endforeach; ?>
         </tbody>
      </table>
   <?php else : ?>
      <div class="row">
         <div class="col">
            <h4 class="text-center text-danger">Data tidak ditemukan</h4>
         </div>
      </div>
   <?php endif; ?>
</div>