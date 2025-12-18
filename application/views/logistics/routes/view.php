<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Info Pengiriman</h3>
            </div>
            <div class="card-body">
                <strong>Driver:</strong> <br> <?= $route->driver_name ?> <br><br>
                <strong>Kendaraan:</strong> <br> <?= $route->vehicle ?> <br><br>
                <strong>Tanggal:</strong> <br> <?= date('d M Y', strtotime($route->route_date)) ?> <br><br>
                <a href="<?= site_url('logistics/routes/print_surat_jalan/'.$route->route_id) ?>" target="_blank" class="btn btn-default btn-block"><i class="fas fa-print"></i> Cetak Surat Jalan</a>
            </div>
        </div>
        
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Tambah Pengiriman</h3>
            </div>
            <form action="<?= site_url('logistics/routes/add_point/'.$route->route_id) ?>" method="post">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pilih Order Siap Kirim</label>
                        <select name="sales_order_id" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Pilih Invoice --</option>
                            <?php if(!empty($pending_orders)): ?>
                                <?php foreach($pending_orders as $po): ?>
                                    <option value="<?= $po->id ?>">
                                        <?= $po->invoice_no ?> - <?= $po->customer_name ?> (<?= $po->address ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled>Tidak ada order siap kirim</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Hanya menampilkan status Request/Preparing</small>
                    </div>
                    <div class="form-group">
                        <label>Urutan Antar</label>
                        <input type="number" name="sequence_number" class="form-control" value="1">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block">Tambahkan ke Rute</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Tujuan (Waypoints)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>No Invoice</th>
                            <th>Pelanggan & Alamat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($route->points)): ?>
                            <?php foreach($route->points as $p): ?>
                            <tr>
                                <td><?= $p->sequence_number ?></td>
                                <td>
                                    <strong><?= $p->invoice_no ? $p->invoice_no : '-' ?></strong>
                                </td>
                                <td>
                                    <strong><?= $p->customer_name ?></strong><br>
                                    <small><?= $p->address ?></small>
                                </td>
                                <td>
                                    <?php if($p->status == 'pending'): ?>
                                        <span class="badge badge-warning">OTW</span>
                                    <?php elseif($p->status == 'delivered'): ?>
                                        <span class="badge badge-success">Sampai</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Gagal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('logistics/routes/delete_point/'.$p->point_id.'/'.$route->route_id) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hapus dari rute? Status order akan kembali ke Preparing.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Belum ada tujuan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Peta Rute</h3>
            </div>
            <div class="card-body">
                <div id="map" style="height: 300px; background: #eee; text-align: center; line-height: 300px;">
                    (Peta akan muncul jika koordinat tersedia)
                </div>
            </div>
        </div>
    </div>
</div>