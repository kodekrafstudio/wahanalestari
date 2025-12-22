<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

<style>
    /* Peta Responsif */
    #map-picker {
        height: 500px;
        width: 100%;
        border-radius: 0 0 4px 4px; /* Sudut bawah melengkung */
        z-index: 1;
    }

    @media (max-width: 768px) {
        #map-picker { height: 350px; }
        .sticky-top { position: static !important; }
    }

    /* Overlay Loading */
    .loading-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8);
        z-index: 1005; display: none;
        align-items: center; justify-content: center;
        font-weight: bold; color: #007bff;
        backdrop-filter: blur(2px);
    }
</style>

<?php
    $is_edit = isset($row); 
    $title_card = $is_edit ? 'Edit Data Pelanggan' : 'Input Pelanggan Baru';
?>

<form action="" method="post" id="formCustomer">
    <div class="row">
        
        <div class="col-12 col-lg-4 order-2 order-lg-1">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-file-alt mr-1"></i> Data Pelanggan</h3>
                </div>
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Nama Toko / Pelanggan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light"><i class="fas fa-store text-primary"></i></span>
                            </div>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Toko Berkah Jaya"
                                   value="<?= $is_edit ? $row->name : set_value('name') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    <?php foreach($categories as $cat): ?>
                                        <?php $selected = ($is_edit && $row->category_id == $cat->category_id) ? 'selected' : ''; ?>
                                        <option value="<?= $cat->category_id ?>" <?= $selected ?>><?= $cat->category_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pemilik / CP</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="Nama Pemilik"
                                       value="<?= $is_edit ? $row->contact_person : set_value('contact_person') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>WhatsApp / HP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white"><i class="fab fa-whatsapp"></i></span>
                            </div>
                            <input type="number" name="phone" class="form-control" placeholder="08xxxx"
                                   value="<?= $is_edit ? $row->phone : set_value('phone') ?>" required>
                        </div>
                    </div>

                    <div class="dropdown-divider my-4"></div>
                    
                    <h6 class="text-muted text-uppercase font-weight-bold mb-3"><i class="fas fa-map-marker-alt mr-1"></i> Detail Lokasi</h6>

                    <div class="form-group relative">
                        <label>Alamat Lengkap</label>
                        <div class="loading-overlay" id="addr-loading"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil alamat...</div>
                        <textarea name="address" id="address" class="form-control" rows="3" placeholder="Alamat akan terisi otomatis saat pin digeser..."><?= $is_edit ? $row->address : set_value('address') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <input type="text" name="district" id="district" class="form-control bg-light" readonly
                                       value="<?= $is_edit ? $row->district : set_value('district') ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kota/Kab</label>
                                <input type="text" name="city" id="city" class="form-control bg-light" readonly
                                       value="<?= $is_edit ? $row->city : set_value('city') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row bg-light p-2 rounded mx-0 border">
                        <div class="col-6 px-1">
                            <small class="text-muted d-block">Latitude</small>
                            <input type="text" name="latitude" id="lat" class="form-control form-control-sm border-0 bg-transparent font-weight-bold pl-0" readonly required value="<?= $is_edit ? $row->latitude : '' ?>">
                        </div>
                        <div class="col-6 px-1 border-left">
                            <small class="text-muted d-block">Longitude</small>
                            <input type="text" name="longitude" id="lng" class="form-control form-control-sm border-0 bg-transparent font-weight-bold pl-0" readonly required value="<?= $is_edit ? $row->longitude : '' ?>">
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> SIMPAN DATA
                    </button>
                    <a href="<?= site_url('marketing/customers') ?>" class="btn btn-default btn-block mt-2 text-muted">Batal</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8 order-1 order-lg-2 mb-3">
            <div class="sticky-top" style="top: 20px; z-index: 100;">
                <div class="card card-outline card-info shadow-sm">
                    
                    <div class="card-header p-3 bg-light">
                        <div class="row">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <h3 class="card-title mt-1"><i class="fas fa-map-marked-alt mr-1"></i> Tentukan Lokasi</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="search-input" class="form-control" placeholder="Cari kecamatan, kota, atau jalan..." onkeypress="handleEnter(event)">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" onclick="searchLocation()" id="btn-search">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="getLocation()" title="Lokasi Saya">
                                            <i class="fas fa-crosshairs text-primary"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0 position-relative">
                        <div id="map-picker"></div>
                        
                        <div style="position: absolute; top: 10px; left: 50px; z-index: 1000; background: rgba(255,255,255,0.9); padding: 5px 12px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); font-size: 12px; font-weight: 500;">
                            <i class="fas fa-hand-pointer text-primary mr-1"></i> Geser <b>Pin Biru</b> ke lokasi toko pelanggan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

<script>
    // --- 1. SETUP PETA ---
    <?php if($is_edit && $row->latitude && $row->longitude): ?>
        var startLat = <?= $row->latitude ?>;
        var startLng = <?= $row->longitude ?>;
        var startZoom = 17;
    <?php else: ?>
        // Default: Indonesia Tengah (agar netral) atau set ke Kota Anda
        var startLat = -7.7956; 
        var startLng = 110.3695;
        var startZoom = 10;
    <?php endif; ?>

    var map = L.map('map-picker').setView([startLat, startLng], startZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var marker = L.marker([startLat, startLng], { 
        draggable: true,
        autoPan: true 
    }).addTo(map);

    // --- 2. LOGIKA UPDATE DATA ---
    function updateInputs(lat, lng) {
        document.getElementById('lat').value = lat.toFixed(7);
        document.getElementById('lng').value = lng.toFixed(7);
    }

    async function getAddress(lat, lng) {
        // Efek Loading
        document.getElementById('addr-loading').style.display = 'flex';
        document.getElementById('address').setAttribute('disabled', true);

        try {
            // Reverse Geocoding (Koordinat -> Alamat)
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
            const response = await fetch(url, { headers: { 'User-Agent': 'AplikasiSales/1.0' } });
            const data = await response.json();

            if (data && data.address) {
                const addr = data.address;
                document.getElementById('address').value = data.display_name;

                let district = addr.village || addr.suburb || addr.town || addr.city_district || "";
                let city = addr.city || addr.municipality || addr.county || addr.regency || "";
                city = city.replace("Kota ", "").replace("Kabupaten ", "");

                document.getElementById('district').value = district;
                document.getElementById('city').value = city;
            }
        } catch (error) {
            console.error("Gagal ambil alamat:", error);
            document.getElementById('address').value = "Gagal mengambil detail alamat (Koneksi error).";
        } finally {
            document.getElementById('addr-loading').style.display = 'none';
            document.getElementById('address').removeAttribute('disabled');
        }
    }

    // --- 3. FITUR PENCARIAN (BARU) ---
    function handleEnter(e) {
        if(e.keyCode === 13){ // Jika tekan Enter
            e.preventDefault(); // Jangan submit form
            searchLocation();
        }
    }

    async function searchLocation() {
        var query = document.getElementById('search-input').value;
        if(!query) { alert("Masukkan nama lokasi yang dicari!"); return; }

        // Ubah tombol jadi loading
        var btn = document.getElementById('btn-search');
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            // Forward Geocoding (Nama -> Koordinat)
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;
            const response = await fetch(url, { headers: { 'User-Agent': 'AplikasiSales/1.0' } });
            const data = await response.json();

            if (data && data.length > 0) {
                var lat = parseFloat(data[0].lat);
                var lon = parseFloat(data[0].lon);
                var newLatLng = new L.LatLng(lat, lon);

                // Pindahkan Peta & Marker
                map.setView(newLatLng, 16); // Zoom mendekat
                marker.setLatLng(newLatLng);
                
                // Update input & ambil detail alamat
                updateInputs(lat, lon);
                getAddress(lat, lon);
            } else {
                alert("Lokasi tidak ditemukan. Coba kata kunci lain (misal: nama jalan + kota).");
            }
        } catch (error) {
            alert("Gagal mencari lokasi. Periksa koneksi internet.");
        } finally {
            // Kembalikan tombol
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // --- 4. EVENT LISTENER ---
    marker.on('dragend', function (e) {
        var pos = marker.getLatLng();
        updateInputs(pos.lat, pos.lng);
        getAddress(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
        getAddress(e.latlng.lat, e.latlng.lng);
    });

    function getLocation() {
        if (navigator.geolocation) {
            // Tombol loading state
            const btn = document.querySelector('button[title="Lokasi Saya"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-primary"></i>';
            
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var newLatLng = new L.LatLng(lat, lng);
                
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, 18);
                updateInputs(lat, lng);
                getAddress(lat, lng);
                btn.innerHTML = '<i class="fas fa-crosshairs text-primary"></i>';
            }, function(error) {
                alert("Gagal ambil lokasi GPS: " + error.message);
                btn.innerHTML = '<i class="fas fa-crosshairs text-primary"></i>';
            });
        } else { alert("Browser tidak support GPS."); }
    }

    setTimeout(function(){ map.invalidateSize(); }, 500);
</script>