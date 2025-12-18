<div class="row">
    <div class="col-md-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rp <?= number_format($total, 0, ',', '.') ?></h3>
                <p>Total Pengeluaran (Periode Ini)</p>
            </div>
            <div class="icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pengeluaran</h3>
        <div class="card-tools">
            <a href="<?= site_url('finance/expenses/create') ?>" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Catat Biaya
            </a>
        </div>
    </div>
    <div class="card-body">
        
        <form action="" method="get" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="date" name="start" class="form-control" value="<?= $filter['start'] ?>">
                </div>
                <div class="col-md-4">
                    <input type="date" name="end" class="form-control" value="<?= $filter['end'] ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filter Data</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Jumlah (Rp)</th>
                        <th>Diinput Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($expenses)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data pengeluaran.</td></tr>
                    <?php else: ?>
                        <?php foreach($expenses as $row): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row->expense_date)) ?></td>
                            <td><span class="badge badge-secondary"><?= $row->category ?></span></td>
                            <td><?= $row->description ?></td>
                            <td class="text-right font-weight-bold text-danger">Rp <?= number_format($row->amount, 0, ',', '.') ?></td>
                            <td><small><?= $row->user_name ?></small></td>
                            <td class="text-center">
                                <a href="<?= site_url('finance/expenses/delete/'.$row->expense_id) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>