<div class="row">
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Info Rute</h3>
            </div>
            <div class="card-body">
                <strong>Driver:</strong><br> <?= $route->driver_name ?><br><br>
                <strong>Kendaraan:</strong><br> <?= $route->vehicle ?><br><br>
                <strong>Tanggal:</strong><br> <?= date('d M Y', strtotime($route->route_date)) ?><br><br>
                <strong>Status:</strong><br> <?= ucfirst($route->status) ?>
            </div>
            <div class="card-footer">
                <a href="<?= site_url('logistics/routes/print_surat_jalan/'.$route->route_id) ?>" target="_blank" class="btn btn-default btn-block">
                    <i class="fas fa-print"></i> Cetak Surat Jalan
                </a>
            </div>
        </div>

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Tambah Tujuan</h3>
            </div>
            <form action="<?= site_url('logistics/routes/add_point/'.$route->route_id) ?>" method="post">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pilih Toko / Pelanggan</label>
                        <select name="customer_id" class="form-control select2" required>
                            <option value="">-- Cari Pelanggan --</option>
                            <?php foreach($customers as $c): ?>
                                <option value="<?= $c->customer_id ?>"><?= $c->name ?> (<?= $c->city ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Daftar Kunjungan (Urutan)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Pelanggan</th>
                            <th>Alamat / Kota</th>
                            <th>Status Kirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($route->points) && count($route->points) > 0): ?>
                            <?php foreach($route->points as $p): ?>
                            <tr>
                                <td><span class="badge badge-info" style="font-size:1.2em"><?= $p->sequence_number ?></span></td>
                                <td>
                                    <strong><?= $p->customer_name ?></strong><br>
                                    <small><i class="fas fa-phone"></i> <?= $p->phone ?></small>
                                </td>
                                <td><?= $p->address ?>, <?= $p->city ?></td>
                                <td><?= ucfirst($p->status) ?></td>
                                <td>
                                    <a href="<?= site_url('logistics/routes/delete_point/'.$route->route_id.'/'.$p->point_id) ?>" class="text-danger" onclick="return confirm('Hapus dari rute?')">
                                        <i class="fas fa-times"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada tujuan ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>