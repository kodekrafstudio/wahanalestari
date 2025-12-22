<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<style>
    #map { 
        width: 100%; 
        height: 75vh; 
        min-height: 600px; 
        z-index: 1; 
        background: #e6e6e6; /* Warna placeholder saat loading */
    }
    
    /* Custom Icon Cluster/Marker */
    .custom-div-icon { background: transparent; border: none; }
    .marker-pin {
        width: 16px; height: 16px; border-radius: 50%;
        border: 2px solid #fff; box-shadow: 0 0 5px rgba(0,0,0,0.5);
    }

    /* Style untuk Tabel di dalam Popup */
    .popup-table td { padding: 2px 5px !important; font-size: 11px; }
    .leaflet-popup-content { width: 260px !important; }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Sebaran Custemer</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="map"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
window.addEventListener('load', function() {
    
    // --- SAFETY CHECK ---
    if (typeof L === 'undefined') { alert("Leaflet Gagal Load"); return; }

    // --- CONFIG GUDANG (Ganti Koordinat Asli Disini) ---
    const GUDANG_LAT = -7.805273 // Contoh Jogja
    const GUDANG_LNG = 110.403241;

    // 1. Inisialisasi Peta
    var map = L.map('map').setView([GUDANG_LAT, GUDANG_LNG], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap', maxZoom: 19
    }).addTo(map);

    // Marker Gudang
    var gudangIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/2942/2942076.png', 
        iconSize: [15, 15], iconAnchor: [16, 16], popupAnchor: [0, -30]
    });
    L.marker([GUDANG_LAT, GUDANG_LNG], {icon: gudangIcon}).addTo(map).bindPopup("<b>Gudang Pusat</b>");

    // Variable Global
    var markers = L.markerClusterGroup();
    var heatLayer = null;
    var routingControl = null;

    // 2. Ambil Data Pelanggan via API
    $.getJSON("<?= site_url('api/map_data/get_all_customers') ?>", function(data) {
        
        var heatPoints = [];
        var maxSales = 0;

        $.each(data, function(key, val) {
            var lat = parseFloat(val.latitude);
            var lng = parseFloat(val.longitude);
            var sales = val.total_sales ? parseFloat(val.total_sales) : 0;

            if(!isNaN(lat) && !isNaN(lng) && lat != 0) {
                
                // --- A. MARKER SETUP ---
                var color = getColor(val.status);
                var customIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class='marker-pin' style='background-color:${color};'></div>`,
                    iconSize: [16, 16], iconAnchor: [8, 8]
                });

                var marker = L.marker([lat, lng], {icon: customIcon});
                var salesFmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sales);

                // --- B. POPUP CONTENT (AJAX HOLDER) ---
                var popupContent = `
                    <div style="width: 240px;">
                        <h6 class="mb-0 font-weight-bold text-primary">${val.name}</h6>
                        <small class="text-muted">${val.category_name}</small><br>
                        
                        <div class="d-flex justify-content-between mt-2 mb-1" style="font-size:11px;">
                            <span>Total Beli:</span>
                            <span class="font-weight-bold text-success">${salesFmt}</span>
                        </div>

                        <hr class="my-1">
                        <small class="text-secondary font-weight-bold">Riwayat Transaksi:</small>
                        
                        <div id="history-${val.customer_id}" class="mt-1 mb-2 bg-light p-1 rounded text-center">
                            <i class="fas fa-spinner fa-spin text-muted"></i> <small>Memuat data...</small>
                        </div>

                        <button class="btn btn-xs btn-outline-primary btn-block" onclick="hitungRute(${lat}, ${lng}, '${val.name}')">
                            <i class="fas fa-truck"></i> Rute dari Gudang
                        </button>
                        <a href="<?= site_url('marketing/customers/detail/') ?>${val.customer_id}" class="btn btn-xs btn-default btn-block mt-1">Lihat Detail Lengkap</a>
                    </div>
                `;

                marker.bindPopup(popupContent);

                // --- C. EVENT: SAAT POPUP DIBUKA (FETCH HISTORY) ---
                marker.on('popupopen', function() {
                    var container = document.getElementById(`history-${val.customer_id}`);
                    
                    // Panggil API History Baru
                    $.getJSON(`<?= site_url('api/map_data/get_customer_history/') ?>${val.customer_id}`, function(hist) {
                        
                        if(hist.length === 0) {
                            container.innerHTML = '<small class="text-muted font-italic">Belum ada transaksi.</small>';
                            return;
                        }

                        var html = '<table class="table table-borderless table-sm mb-0 popup-table">';
                        
                        hist.forEach(function(h) {
                            // Format Tanggal
                            var d = new Date(h.order_date);
                            var dateStr = d.toLocaleDateString('id-ID', {day:'numeric', month:'short'});
                            var amt = new Intl.NumberFormat('id-ID').format(h.total_amount);
                            var badge = h.status == 'done' ? 'text-success' : 'text-warning';

                            html += `
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td>${dateStr}</td>
                                    <td class="text-right">${amt}</td>
                                    <td class="text-right ${badge}">${h.status}</td>
                                </tr>
                            `;
                        });
                        html += '</table>';
                        container.innerHTML = html;

                    }).fail(function() {
                        container.innerHTML = '<small class="text-danger">Gagal memuat info.</small>';
                    });
                });

                markers.addLayer(marker);

                // --- D. HEATMAP DATA ---
                if(sales > maxSales) maxSales = sales;
                if(sales > 0) heatPoints.push([lat, lng, sales]);
            }
        });

        // Add Clusters
        map.addLayer(markers);

        // Add Heatmap
        if(heatPoints.length > 0 && maxSales > 0) {
            var finalHeat = heatPoints.map(p => {
                var i = p[2] / maxSales;
                if(i < 0.15) i = 0.25; 
                return [p[0], p[1], i];
            });

            heatLayer = L.heatLayer(finalHeat, {
                radius: 35, blur: 20, maxZoom: 12,
                gradient: {0.2: 'blue', 0.5: 'lime', 0.8: 'yellow', 1.0: 'red'}
            });
        }

        // Controls
        var overlays = { "Cluster Pelanggan": markers };
        if(heatLayer) overlays["Heatmap Penjualan 🔥"] = heatLayer;
        L.control.layers(null, overlays).addTo(map);

        if(markers.getBounds().isValid()) map.fitBounds(markers.getBounds());

    });

    // FUNGSI RUTE (Global)
    window.hitungRute = function(destLat, destLng, destName) {
        if (routingControl != null) {
            map.removeControl(routingControl);
            routingControl = null;
        }
        
        // Tutup popup biar lega
        map.closePopup();

        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(GUDANG_LAT, GUDANG_LNG),
                L.latLng(destLat, destLng)
            ],
            routeWhileDragging: false,
            lineOptions: { styles: [{color: '#007bff', opacity: 0.7, weight: 6}] },
            show: true, 
            addWaypoints: false,
            createMarker: function() { return null; } // Hilangkan marker default routing biar gak double
        }).addTo(map);
    };

    function getColor(status) {
        if(status == 'active') return '#28a745';
        if(status == 'prospect') return '#17a2b8';
        if(status == 'blacklist') return '#dc3545';
        return '#6c757d';
    }
    
    setTimeout(function(){ map.invalidateSize(); }, 800);
});
</script>