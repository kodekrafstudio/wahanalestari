<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Kategori Bisnis</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/categories/add') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($categories as $cat): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $cat->category_name ?></td>
                        <td><?= $cat->description ?></td>
                        <td>
                            <a href="<?= site_url('master/categories/edit/'.$cat->category_id) ?>" class="btn btn-warning btn-xs">Edit</a>
                            <a href="<?= site_url('master/categories/delete/'.$cat->category_id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>