<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Omzet Bulan Ini</span>
                <span class="info-box-number">Rp <?= number_format($kpi['omzet']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm">
                    <a href="<?= site_url('reports/profit_loss') ?>" class="text-white" style="text-decoration: underline;">Lihat Detail</a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Est. Laba Bersih</span>
                <span class="info-box-number">Rp <?= number_format($kpi['net_profit']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm">
                    <a href="<?= site_url('reports/profit_loss') ?>" class="text-white" style="text-decoration: underline;">Lihat Detail</a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-warehouse text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-white">Valuasi Stok</span>
                <span class="info-box-number text-white">Rp <?= number_format($kpi['asset_value']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm">
                    <a href="<?= site_url('inventory/stock') ?>" class="text-white" style="text-decoration: underline;">Lihat Detail</a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Piutang</span>
                <span class="info-box-number">Rp <?= number_format($kpi['piutang']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm">
                    <a href="<?= site_url('marketing/sales?status=all') ?>" class="text-white" style="text-decoration: underline;">Tagih Sekarang</a>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-chart-area mr-1"></i> Performa Bisnis <?= date('Y') ?></h3>
            </div>
            <div class="card-body">
                <canvas id="mainChart" height="280" style="height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-orange">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn mr-1"></i> Smart Follow-Up</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if(empty($follow_up)): ?>
                        <li class="item text-center p-4 text-muted">
                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i><br>
                            Semua pelanggan aktif belanja!
                        </li>
                    <?php else: ?>
                        <?php foreach($follow_up as $f): ?>
                        <li class="item">
                            <div class="product-img">
                                <span class="badge badge-danger float-right">Absen <?= $f->days_since ?> Hari</span>
                            </div>
                            <div class="product-info ml-2">
                                <a href="javascript:void(0)" class="product-title"><?= $f->name ?></a>
                                <span class="product-description">
                                    Terakhir: <?= date('d M Y', strtotime($f->last_order)) ?>
                                </span>
                                <a href="https://wa.me/<?= $f->phone ?>" target="_blank" class="btn btn-xs btn-success mt-1">
                                    <i class="fab fa-whatsapp"></i> Sapa
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="<?= site_url('marketing/customers') ?>" class="uppercase">Lihat Semua Pelanggan</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    
    <div class="col-md-4">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title text-warning">Top 5 Pelanggan</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th class="text-right">Total Beli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($top_customers)): ?>
                            <tr><td colspan="2" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                        <?php else: ?>
                            <?php 
                                $max_beli = $top_customers[0]->total_beli; 
                            ?>
                            <?php foreach($top_customers as $index => $tc): 
                                $percent = ($tc->total_beli / $max_beli) * 100;
                            ?>
                            <tr>
                                <td>
                                    <span class="badge badge-secondary mr-1"><?= $index + 1 ?></span> 
                                    <?= $tc->name ?>
                                    <div class="progress progress-xs mt-1">
                                        <div class="progress-bar bg-warning" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-right font-weight-bold">
                                    Rp <?= number_format($tc->total_beli/1000, 0) ?>k
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title text-info">Produk Terlaris</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th class="text-right">Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($top_products)): ?>
                            <tr><td colspan="2" class="text-center text-muted py-4">Belum ada penjualan.</td></tr>
                        <?php else: ?>
                            <?php 
                                // Cari nilai tertinggi untuk skala bar biru
                                $max_sold = $top_products[0]->total_sold; 
                            ?>
                            <?php foreach($top_products as $index => $tp): 
                                $percent = ($tp->total_sold / $max_sold) * 100;
                            ?>
                            <tr>
                                <td>
                                    <span class="badge badge-secondary mr-1"><?= $index + 1 ?></span>
                                    <?= $tp->name ?>
                                    <div class="progress progress-xs mt-1">
                                        <div class="progress-bar bg-info" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <span class="badge bg-info" style="font-size: 0.9em;">
                                        <?= number_format($tp->total_sold) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title text-danger">
                    Stok Update (< 1000)
                </h3>
                <div class="card-tools">
                    <a href="<?= site_url('inventory/stock') ?>" class="btn btn-tool btn-sm">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th class="text-right">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($low_stock)): ?>
                            <tr>
                                <td colspan="2" class="text-center text-success py-4">
                                    Stok Aman
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($low_stock as $ls): ?>
                            <tr>
                                <td><?= $ls->name ?></td>
                                <td class="text-right">
                                    <span class="text-danger font-weight-bold" style="font-size: 1.2em;">
                                        <?= number_format($ls->quantity) ?>
                                    </span> 
                                    <small class="text-muted"><?= $ls->unit ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.addEventListener('load', function() {
    
    // Safety check library
    if (typeof Chart === 'undefined') { console.error("ChartJS gagal load"); return; }

    // --- 1. DUAL LINE CHART (OMZET VS PROFIT) ---
    var ctxMain = document.getElementById('mainChart');
    if(ctxMain) {
        var ctx = ctxMain.getContext('2d');
        
        // Gradient Colors
        var gradientBlue = ctx.createLinearGradient(0,0,0,300);
        gradientBlue.addColorStop(0, 'rgba(60, 141, 188, 0.5)');
        gradientBlue.addColorStop(1, 'rgba(60, 141, 188, 0.0)');

        var gradientGreen = ctx.createLinearGradient(0,0,0,300);
        gradientGreen.addColorStop(0, 'rgba(40, 167, 69, 0.5)');
        gradientGreen.addColorStop(1, 'rgba(40, 167, 69, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        label: 'Omzet (Revenue)',
                        data: <?= $chart_omzet ?>,
                        borderColor: '#3b8bba',
                        backgroundColor: gradientBlue,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3
                    },
                    {
                        label: 'Profit (Laba)',
                        data: <?= $chart_profit ?>,
                        borderColor: '#28a745',
                        backgroundColor: gradientGreen,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                tooltips: {
                    mode: 'index', intersect: false,
                    callbacks: { label: function(t) { return 'Rp ' + Number(t.yLabel).toLocaleString('id-ID'); } }
                },
                scales: {
                    yAxes: [{ ticks: { callback: function(val) { return val/1000000 + ' Jt'; } } }]
                }
            }
        });
    }

    // --- 2. PIE CHART ---
    var ctxPie = document.getElementById('pieChart');
    if(ctxPie) {
        var pNames = <?= $pie_labels ?>;
        var pQty   = <?= $pie_data ?>;
        
        if(pNames.length == 0) { pNames = ['Belum Ada Data']; pQty=[1]; }

        new Chart(ctxPie.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pNames,
                datasets: [{
                    data: pQty,
                    backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc'],
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: { position: 'right', labels: { boxWidth: 10, fontSize: 10 } }
            }
        });
    }
});
</script>