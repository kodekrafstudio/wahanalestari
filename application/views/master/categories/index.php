<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tags mr-1"></i> Data Kategori</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/categories/add') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table id="tableCategories" class="table table-hover text-nowrap">
            <thead class="bg-light">
                <tr>
                    <th style="width: 10px">#</th>
                    <th style="width: 30%">Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-right" style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($categories as $cat): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <span class="font-weight-bold text-primary">
                            <i class="fas fa-tag mr-1 text-muted text-xs"></i> <?= $cat->category_name ?>
                        </span>
                    </td>
                    <td>
                        <?php if($cat->description): ?>
                            <?= $cat->description ?>
                        <?php else: ?>
                            <span class="text-muted font-italic">- Tidak ada deskripsi -</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <a href="<?= site_url('master/categories/edit/'.$cat->category_id) ?>" class="btn btn-default btn-sm" title="Edit">
                                <i class="fas fa-pencil-alt text-warning"></i>
                            </a>
                            <a href="<?= site_url('master/categories/delete/'.$cat->category_id) ?>" class="btn btn-default btn-sm" onclick="return confirm('Yakin hapus kategori ini? Produk yang menggunakan kategori ini mungkin akan terpengaruh.')" title="Hapus">
                                <i class="fas fa-trash text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
  $(function () {
    $("#tableCategories").DataTable({
      "responsive": true,
      "autoWidth": false,
      "columnDefs": [
        { "orderable": false, "targets": 3 } // Matikan sorting di kolom Aksi
      ],
      "language": {
          "search": "Cari Kategori:",
          "zeroRecords": "Belum ada data kategori"
      }
    });
  });
</script>