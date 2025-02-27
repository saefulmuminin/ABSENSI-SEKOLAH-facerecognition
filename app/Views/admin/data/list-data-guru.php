<div class="card-body table-responsive">  
   <?php if (!$empty) : ?>  
      <table class="table table-hover">  
         <thead class="text-success">  
            <th><b>No</b></th> 
            <th><b>NUPTK</b></th>  
            <th><b>Nama Guru</b></th>  
            <th><b>Jenis Kelamin</b></th>  
            <th><b>No HP</b></th>  
            <th><b>Alamat</b></th>  
            <th><b>Aksi</b></th>  
         </thead>  
         <tbody>  
            <?php $i = 1; foreach ($data as $value) : ?>  
               <tr>  
                  <td><?= $i; ?></td>  
                  <td><?= $value['nuptk']; ?></td>  
                  <td><b><?= $value['nama_guru']; ?></b></td>  
                  <td><?= $value['jenis_kelamin']; ?></td>  
                  <td><?= $value['no_hp']; ?></td>  
                  <td><?= $value['alamat']; ?></td>  
                  <td>  
                     <a href="<?= base_url('admin/guru/edit/' . $value['id_guru']); ?>" type="button" class="btn btn-success p-2">  
                        <i class="material-icons">edit</i>  
                        Edit  
                     </a>  
                     <button type="button" class="btn btn-danger p-2" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $value['id_guru']; ?>">
                        <i class="material-icons">delete_forever</i> Delete
                     </button>

                     <!-- Modal Konfirmasi Hapus -->
                     <div class="modal fade" id="deleteModal<?= $value['id_guru']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $value['id_guru']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                           <div class="modal-content">
                              <div class="modal-header bg-danger text-white">
                                 <h5 class="modal-title" id="deleteModalLabel<?= $value['id_guru']; ?>">Confirm Deletion</h5>
                                 <button type="button" class="close text-white border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="material-icons">close</i>
                                 </button>
                              </div>
                              <div class="modal-body">
                                 <p class="mb-0">Are you sure you want to delete this item? This action cannot be undone.</p>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                 <form id="deleteForm<?= $value['id_guru']; ?>" action="<?= base_url('admin/guru/delete/' . $value['id_guru']); ?>" method="post">
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
            <?php $i++; endforeach; ?>  
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