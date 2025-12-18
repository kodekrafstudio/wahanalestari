<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Buat Rute Pengiriman Baru</h3>
    </div>
    <form action="" method="post">
        <div class="card-body">
            <div class="form-group">
                <label>Tanggal Pengiriman</label>
                <input type="date" name="route_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Pilih Driver</label>
                <select name="driver_id" class="form-control" required>
                    <option value="">-- Pilih Driver --</option>
                    <?php foreach($drivers as $d): ?>
                        <option value="<?= $d->user_id ?>"><?= $d->full_name ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Hanya user dengan role 'driver' yang muncul.</small>
            </div>
            <div class="form-group">
                <label>Kendaraan (Nopol / Jenis)</label>
                <input type="text" name="vehicle" class="form-control" placeholder="Contoh: L300 - AB 1234 XY" required>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan & Lanjut Atur Tujuan</button>
        </div>
    </form>
</div>