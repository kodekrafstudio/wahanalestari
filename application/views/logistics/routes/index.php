<div class="card">
    <div class="card-header">
        <h3 class="card-title">Jadwal Pengiriman</h3>
        <div class="card-tools">
            <a href="<?= site_url('logistics/routes/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-truck-moving"></i> Buat Rute Baru
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Driver</th>
                        <th>Kendaraan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($routes as $r): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($r->route_date)) ?></td>
                        <td><?= $r->driver_name ?></td>
                        <td><?= $r->vehicle ?></td>
                        <td>
                            <span class="badge badge-<?= $r->status == 'completed' ? 'success' : ($r->status == 'ongoing' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst($r->status) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= site_url('logistics/routes/view/'.$r->route_id) ?>" class="btn btn-info btn-xs">
                                <i class="fas fa-eye"></i> Detail / Atur Tujuan
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>