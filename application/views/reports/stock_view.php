<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search"></i> Filter Stok</h3>
    </div>
    <div class="card-body">
        <form action="" method="get">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start" class="form-control" value="<?= $filter['start'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end" class="form-control" value="<?= $filter['end'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Produk</label>
                        <select name="product_id" class="form-control">
                            <option value="all">Semua Produk</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p->product_id ?>" <?= $filter['product_id'] == $p->product_id ? 'selected' : '' ?>><?= $p->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group btn-block">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Tampilkan</button>
                            <button type="submit" name="export" value="excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">Riwayat Keluar Masuk Barang</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Nama Produk</th>
                        <th>Jenis Mutasi</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($report as $row): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($row->created_at)) ?></td>
                        <td><?= $row->product_name ?></td>
                        <td class="text-center">
                            <?php if($row->change_qty > 0): ?>
                                <span class="badge badge-success">MASUK (IN)</span>
                            <?php else: ?>
                                <span class="badge badge-danger">KELUAR (OUT)</span>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-bold text-right">
                            <?= abs($row->change_qty) ?> <?= $row->unit ?>
                        </td>
                        <td><?= $row->description ?></td>
                        <td><?= $row->user_name ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>