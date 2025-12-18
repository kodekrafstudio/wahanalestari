<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Produk Garam</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/products/add') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Produk</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Tipe</th>
                        <th>Grade</th>
                        <th>Satuan</th>
                        <th>HPP (Rp)</th>
                        <th>Harga Jual (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($products as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $p->name ?></td>
                        <td><span class="badge badge-info"><?= $p->type ?></span></td>
                        <td><?= $p->grade ?></td>
                        <td><?= $p->unit ?></td>
                        <td><?= number_format($p->base_cost, 0, ',', '.') ?></td>
                        <td><strong><?= number_format($p->sell_price, 0, ',', '.') ?></strong></td>
                        <td>
                            <a href="<?= site_url('master/products/edit/'.$p->product_id) ?>" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                            <a href="<?= site_url('master/products/delete/'.$p->product_id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>