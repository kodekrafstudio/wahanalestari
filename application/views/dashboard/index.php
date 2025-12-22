<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

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
        <div class="card card-outline card-info">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-calendar-day mr-1"></i> 
                    Tren Penjualan Harian: <strong><?= $month_name ?></strong>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="dailyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
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

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-purple">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt mr-1"></i> Sebaran Pelanggan (Live Map)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="dashboardMap" style="height: 450px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('load', function() {
    
    // --- 1. SETUP CHART (OMZET & PROFIT) ---
    if(document.getElementById('mainChart')) {
        var ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [
                    { 
                        label: 'Omzet', 
                        data: <?= $chart_omzet ?>, 
                        borderColor: '#17a2b8', // Info Blue
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        fill: true,
                        pointRadius: 3 // Titik chart diperkecil juga biar rapi
                    },
                    { 
                        label: 'Profit', 
                        data: <?= $chart_profit ?>, 
                        borderColor: '#28a745', // Success Green
                        fill: false,
                        pointRadius: 3
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return 'Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                },
                scales: {
                    yAxes: [{ ticks: { callback: function(value) { return 'Rp ' + value/1000000 + ' Jt'; } } }]
                }
            }
        });
    }

    // --- 2. PIE CHART (PRODUK) ---
    if(document.getElementById('pieChart')) {
        var pLabels = <?= $pie_labels ?>;
        var pData = <?= $pie_data ?>;
        if(pLabels.length == 0) { pLabels = ['No Data']; pData = [1]; }
        
        new Chart(document.getElementById('pieChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pLabels,
                datasets: [{ 
                    data: pData, 
                    backgroundColor: ['#f56954','#00a65a','#f39c12','#00c0ef','#3c8dbc', '#d2d6de'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, legend: {position:'bottom'} }
        });
    }

    // --- 3. MAPS (WEBGIS) - TITIK KECIL (DOTS) ---
    if(document.getElementById('dashboardMap')) {
        var map = L.map('dashboardMap'); 

        // Gunakan peta yang lebih bersih/terang (CartoDB Positron) agar titik terlihat jelas
        // Atau tetap pakai OSM standar
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var markersData = <?= $map_data ?>; 
        var markerArray = [];

        markersData.forEach(function(m) {
            if(m.latitude && m.longitude) {
                var lat = parseFloat(m.latitude);
                var lng = parseFloat(m.longitude);

                // GANTI DARI L.marker KE L.circleMarker
                var marker = L.circleMarker([lat, lng], {
                    radius: 6,          // Ukuran titik (semakin kecil angkanya, semakin kecil titiknya)
                    fillColor: "#ff0000", // Warna Merah (Bisa diganti hex code lain, misal #007bff biru)
                    color: "#ffffff",     // Warna Garis Tepi (Putih biar kontras)
                    weight: 1,            // Ketebalan garis tepi
                    opacity: 1,
                    fillOpacity: 0.8      // Transparansi (0.8 = agak solid)
                })
                .addTo(map)
                .bindPopup(`
                    <div style="text-align:center; min-width: 150px;">
                        <b style="color:#333;">${m.name}</b><br>
                        <span class="badge badge-info" style="font-size:10px;">${m.category_name || 'Umum'}</span><br>
                        <div style="font-size:11px; margin-top:4px; color:#666;">${m.address}</div>
                    </div>
                `);
                
                markerArray.push(marker); 
            }
        });

        // Auto Zoom agar pas semua titik
        if (markerArray.length > 0) {
            var group = new L.featureGroup(markerArray);
            map.fitBounds(group.getBounds().pad(0.1));
        } else {
            map.setView([-7.7956, 110.3695], 10); 
        }
        
        setTimeout(function(){ map.invalidateSize(); }, 800);
    }

    // --- 4. SETUP CHART HARIAN (BAR CHART) ---
    if(document.getElementById('dailyChart')) {
        var ctxDaily = document.getElementById('dailyChart').getContext('2d');
        new Chart(ctxDaily, {
            type: 'bar', // Pakai Bar Chart biar beda dengan tahunan
            data: {
                labels: <?= $daily_labels ?>, // [1, 2, 3... 31]
                datasets: [{
                    label: 'Omzet Harian',
                    data: <?= $chart_daily ?>,
                    backgroundColor: '#17a2b8', // Warna Biru Info
                    borderColor: '#117a8b',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            // Format Rupiah Singkat (1jt, 500k)
                            callback: function(value) {
                                if(value >= 1000000) return 'Rp ' + value/1000000 + ' Jt';
                                if(value >= 1000) return 'Rp ' + value/1000 + ' k';
                                return value;
                            }
                        }
                    }],
                    xAxes: [{
                        gridLines: { display: false } // Biar bersih
                    }]
                },
                tooltips: {
                    callbacks: {
                        title: function(tooltipItem) {
                            return 'Tanggal ' + tooltipItem[0].xLabel + ' <?= $month_name ?>';
                        },
                        label: function(tooltipItem) {
                            return 'Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            }
        });
    }
});
</script>