<div class="row">
    <div class="col-md-6">
        <div class="card bg-gradient-primary">
            <div class="card-body">
                <h5 class="card-title">Performa Bulan Ini</h5>
                <h2 class="mt-3">Rp <?= number_format($sales_stats['omzet'], 0, ',', '.') ?></h2>
                <p>Target: Rp <?= number_format($sales_stats['target'], 0, ',', '.') ?></p>
                
                <div class="progress progress-xs" style="height: 10px;">
                    <div class="progress-bar bg-warning" style="width: <?= $sales_stats['persen'] ?>%"></div>
                </div>
                <small class="text-white"><?= number_format($sales_stats['persen'], 1) ?>% dari Target</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $sales_stats['count'] ?></h3>
                <p>Transaksi</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h4 class="mb-0">Rp <?= number_format($sales_stats['komisi'], 0, ',', '.') ?></h4>
                <p>Est. Komisi</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Order Terbaru Saya</h3>
        <div class="card-tools">
            <a href="<?= site_url('marketing/sales/create') ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Order Baru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($recent_orders)): ?>
                    <tr><td colspan="4" class="text-center">Belum ada order.</td></tr>
                <?php else: ?>
                    <?php foreach($recent_orders as $ro): ?>
                    <tr>
                        <td><?= $ro->invoice_no ?></td>
                        <td><?= date('d/m/Y', strtotime($ro->order_date)) ?></td>
                        <td>
                            <span class="badge badge-<?= ($ro->status=='done')?'success':(($ro->status=='canceled')?'danger':'warning') ?>">
                                <?= ucfirst($ro->status) ?>
                            </span>
                        </td>
                        <td class="text-right">Rp <?= number_format($ro->total_amount, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer text-center">
        <a href="<?= site_url('marketing/sales') ?>">Lihat Semua Riwayat</a>
    </div>
</div>