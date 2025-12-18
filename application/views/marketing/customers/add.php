<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

<?php
    // Logika Cek Mode: Edit atau Tambah?
    $is_edit = isset($row); 
    $title_card = $is_edit ? 'Edit Data Pelanggan' : 'Input Pelanggan Baru';
?>

<div class="row">
    <div class="col-md-5">
        <form action="" method="post">
            <div class="card card-primary card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-edit"></i> <?= $title_card ?></h3>
                </div>
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Nama Toko / Pelanggan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-store"></i></span>
                            </div>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= $is_edit ? $row->name : set_value('name') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kategori Bisnis <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    <?php foreach($categories as $cat): ?>
                                        <?php 
                                            // Auto Select jika Edit
                                            $selected = ($is_edit && $row->category_id == $cat->category_id) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $cat->category_id ?>" <?= $selected ?>>
                                            <?= $cat->category_name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Pemilik / CP</label>
                                <input type="text" name="contact_person" class="form-control" 
                                       value="<?= $is_edit ? $row->contact_person : set_value('contact_person') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>No HP / WhatsApp <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                            </div>
                            <input type="number" name="phone" class="form-control" 
                                   value="<?= $is_edit ? $row->phone : set_value('phone') ?>" required>
                        </div>
                    </div>

                    <hr>
                    <div class="text-muted small mb-2"><i class="fas fa-info-circle"></i> Geser pin di peta untuk isi alamat otomatis.</div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="address" id="address" class="form-control" rows="2"><?= $is_edit ? $row->address : set_value('address') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <input type="text" name="district" id="district" class="form-control" 
                                       value="<?= $is_edit ? $row->district : set_value('district') ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kota/Kabupaten</label>
                                <input type="text" name="city" id="city" class="form-control" 
                                       value="<?= $is_edit ? $row->city : set_value('city') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group bg-light p-2 rounded border mt-2">
                        <label class="mb-0 text-muted small font-weight-bold">Titik Koordinat</label>
                        <div class="row mt-1">
                            <div class="col-6">
                                <input type="text" name="latitude" id="lat" class="form-control form-control-sm" readonly required 
                                       value="<?= $is_edit ? $row->latitude : '' ?>">
                            </div>
                            <div class="col-6">
                                <input type="text" name="longitude" id="lng" class="form-control form-control-sm" readonly required 
                                       value="<?= $is_edit ? $row->longitude : '' ?>">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= site_url('marketing/customers') ?>" class="btn btn-default">Batal</a>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-7">
        <div class="card card-outline card-info h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Tentukan Lokasi</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool btn-sm bg-info text-white" onclick="getLocation()">
                        <i class="fas fa-crosshairs"></i> Lokasi Saya
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="map-picker" style="height: 100%; min-height: 600px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

<script>
    // --- KONFIGURASI MAP (Cerdas) ---
    
    // 1. Tentukan Titik Awal
    // Jika Mode Edit dan ada koordinat, pakai itu. Jika tidak, pakai default (Jogja).
    <?php if($is_edit && $row->latitude && $row->longitude): ?>
        var startLat = <?= $row->latitude ?>;
        var startLng = <?= $row->longitude ?>;
        var startZoom = 17; // Zoom dekat kalau edit
    <?php else: ?>
        var startLat = -7.7956;
        var startLng = 110.3695;
        var startZoom = 13;
    <?php endif; ?>
    
    // 2. Render Peta
    var map = L.map('map-picker').setView([startLat, startLng], startZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

    // --- FUNGSI UPDATE ---
    function updateCoordinate(lat, lng) {
        document.getElementById('lat').value = lat.toFixed(7);
        document.getElementById('lng').value = lng.toFixed(7);
    }

    async function getAddress(lat, lng) {
        document.getElementById('address').value = "Mengambil data...";
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            if (data && data.address) {
                const addr = data.address;
                
                // Logic parsing alamat (Sama seperti sebelumnya)
                let street = addr.road || addr.street || addr.village || "";
                let number = addr.house_number ? " No. " + addr.house_number : "";
                document.getElementById('address').value = street + number + (addr.postcode ? ", " + addr.postcode : "");

                let district = addr.village || addr.suburb || addr.town || addr.city_district || "";
                document.getElementById('district').value = district;

                let city = addr.city || addr.municipality || addr.county || addr.regency || "";
                city = city.replace("Kota ", "").replace("Kabupaten ", "");
                document.getElementById('city').value = city;
            }
        } catch (error) {
            console.error(error);
        }
    }

    // --- EVENT LISTENERS ---
    marker.on('dragend', function (e) {
        var pos = marker.getLatLng();
        updateCoordinate(pos.lat, pos.lng);
        getAddress(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoordinate(e.latlng.lat, e.latlng.lng);
        getAddress(e.latlng.lat, e.latlng.lng);
    });

    // GPS Lokasi Saya
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, 17);
                updateCoordinate(lat, lng);
                getAddress(lat, lng);
                alert("Lokasi GPS ditemukan!");
            });
        } else { alert("Browser tidak support GPS."); }
    }
    
    setTimeout(function(){ map.invalidateSize(); }, 800);
</script>