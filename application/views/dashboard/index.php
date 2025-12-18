<div class="row mb-2">
    <div class="col-sm-6">
        <h4 class="m-0 text-dark">Executive Dashboard</h4>
    </div>
    <div class="col-sm-6">
        <form class="form-inline float-right" method="get" action="<?= site_url('dashboard') ?>">
            <div class="input-group input-group-sm">
                <select name="month" class="form-control">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($m==$f_month)?'selected':'' ?>>
                            <?= date('F', mktime(0,0,0,$m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="year" class="form-control mx-1">
                    <?php for($y=date('Y'); $y>=2020; $y--): ?>
                        <option value="<?= $y ?>" <?= ($y==$f_year)?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Omzet (Revenue)</span>
                <span class="info-box-number">Rp <?= number_format($kpi['omzet']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm">Periode Terpilih</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Laba Bersih (Net)</span>
                <span class="info-box-number">Rp <?= number_format($kpi['net_profit']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm">Estimasi Profit</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3 bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-warehouse text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-white">Aset Stok</span>
                <span class="info-box-number text-white">Rp <?= number_format($kpi['asset_value']/1000000, 1) ?> Jt</span>
                <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                <span class="progress-description text-sm text-white">Nilai Barang Gudang</span>
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
                <span class="progress-description text-sm">Belum Dibayar Customer</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-chart-area mr-1"></i> Tren Bisnis <?= $f_year ?></h3>
            </div>
            <div class="card-body">
                <canvas id="mainChart" height="280" style="height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-teal">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Komposisi Produk</h3>
            </div>
            <div class="card-body">
                <canvas id="pieChart" height="280" style="height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-purple">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt mr-1"></i> Sebaran Pelanggan (WebGIS)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="dashboardMap" style="height: 400px; width: 100%;"></div>
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
                    <thead><tr><th>Pelanggan</th><th class="text-right">Beli</th></tr></thead>
                    <tbody>
                        <?php if(empty($top_customers)): ?>
                            <tr><td colspan="2" class="text-center text-muted py-3">Nihil</td></tr>
                        <?php else: ?>
                            <?php foreach($top_customers as $tc): ?>
                            <tr>
                                <td><?= $tc->name ?></td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($tc->total_beli/1000, 0) ?>k</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-orange">
            <div class="card-header">
                <h3 class="card-title text-orange">Follow Up Priority</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if(empty($follow_up)): ?>
                        <li class="item text-center p-3 text-success">Semua pelanggan aktif!</li>
                    <?php else: ?>
                        <?php foreach($follow_up as $f): ?>
                        <li class="item">
                            <div class="product-info ml-2">
                                <a href="javascript:void(0)" class="product-title"><?= $f->name ?>
                                    <span class="badge badge-danger float-right"><?= $f->days_since ?> Hari</span>
                                </a>
                                <span class="product-description text-xs">Last: <?= date('d/m/y', strtotime($f->last_order)) ?></span>
                                <a href="https://wa.me/<?= $f->phone ?>" target="_blank" class="text-success text-xs"><i class="fab fa-whatsapp"></i> Chat</a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title text-danger">Stok Menipis (<1000)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Produk</th><th class="text-right">Sisa</th></tr></thead>
                    <tbody>
                        <?php if(empty($low_stock)): ?>
                            <tr><td colspan="2" class="text-center text-success py-3">Stok Aman</td></tr>
                        <?php else: ?>
                            <?php foreach($low_stock as $ls): ?>
                            <tr>
                                <td><?= $ls->name ?></td>
                                <td class="text-right text-danger font-weight-bold">
                                    <?= number_format($ls->quantity) ?> <small class="text-muted"><?= $ls->unit ?></small>
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
    
    // --- 1. SETUP CHART ---
    if(document.getElementById('mainChart')) {
        var ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [
                    { label: 'Omzet', data: <?= $chart_omzet ?>, borderColor: '#3b8bba', fill: false },
                    { label: 'Profit', data: <?= $chart_profit ?>, borderColor: '#28a745', fill: false }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if(document.getElementById('pieChart')) {
        var pLabels = <?= $pie_labels ?>;
        var pData = <?= $pie_data ?>;
        if(pLabels.length == 0) { pLabels = ['No Data']; pData = [1]; }
        
        new Chart(document.getElementById('pieChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pLabels,
                datasets: [{ data: pData, backgroundColor: ['#f56954','#00a65a','#f39c12','#00c0ef','#3c8dbc'] }]
            },
            options: { responsive: true, maintainAspectRatio: false, legend: {position:'bottom'} }
        });
    }

    // --- 2. SETUP MAP (LEAFLET) ---
    if(document.getElementById('dashboardMap')) {
        var map = L.map('dashboardMap').setView([-6.9, 110.4], 9); // Koordinat Default (Jawa Tengah)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var markers = <?= $map_data ?>; // Data dari Controller
        
        // Loop marker
        markers.forEach(function(m) {
            if(m.latitude && m.longitude) {
                L.marker([m.latitude, m.longitude])
                 .addTo(map)
                 .bindPopup("<b>"+m.name+"</b><br>"+m.address);
            }
        });
    }
});
</script>