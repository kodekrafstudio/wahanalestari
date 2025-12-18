<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Supplier</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/suppliers/add') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Supplier</th>
                        <th>PIC</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($suppliers as $s): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $s->supplier_name ?></td>
                        <td><?= $s->pic_name ?></td>
                        <td><?= $s->phone ?></td>
                        <td><?= $s->address ?></td>
                        <td>
                            <a href="<?= site_url('master/suppliers/edit/'.$s->supplier_id) ?>" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                            <a href="<?= site_url('master/suppliers/delete/'.$s->supplier_id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>