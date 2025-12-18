<div class="row">
    <div class="col-12 mb-3">
        <div class="callout callout-info">
            <h5><i class="fas fa-user-tie"></i> Halo, <?= $this->session->userdata('full_name') ?>!</h5>
            <p>Semangat pagi! Kejar targetmu bulan ini.</p>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Penjualan Saya</span>
                <span class="info-box-number">Rp <?= number_format($sales_stats['omzet'], 0, ',', '.') ?></span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description">Bulan Ini</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Transaksi</span>
                <span class="info-box-number"><?= $sales_stats['count'] ?> Faktur</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description">Bulan Ini</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-wallet text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-white">Estimasi Insentif</span>
                <span class="info-box-number text-white">Rp <?= number_format($sales_stats['komisi'], 0, ',', '.') ?></span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-white text-sm">*Simulasi 2.5%</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Menu Cepat</h3>
            </div>
            <div class="card-body">
                <a href="<?= site_url('marketing/sales/create') ?>" class="btn btn-app bg-success btn-block text-white" style="height: auto; padding: 20px;">
                    <i class="fas fa-cart-plus fa-2x mb-2"></i><br> 
                    <span style="font-size: 1.2em; font-weight: bold;">BUAT ORDER BARU</span>
                </a>
                <div class="row mt-3">
                    <div class="col-6">
                        <a href="<?= site_url('marketing/customers/add') ?>" class="btn btn-default btn-block">
                            <i class="fas fa-user-plus"></i> Pelanggan Baru
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= site_url('marketing/customers/map') ?>" class="btn btn-default btn-block">
                            <i class="fas fa-map"></i> Cek Peta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">5 Transaksi Terakhir Saya</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($recent_orders)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach($recent_orders as $o): ?>
                                <tr>
                                    <td><a href="#"><?= $o->invoice_no ?></a></td>
                                    <td><?= date('d M', strtotime($o->order_date)) ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($o->total_amount/1000, 0) ?>k</td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $o->status == 'done' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($o->status) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="<?= site_url('marketing/sales') ?>">Lihat Semua Riwayat</a>
            </div>
        </div>
    </div>
</div>