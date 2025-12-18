<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat Perubahan Stok (500 Transaksi Terakhir)</h3>
        <div class="card-tools">
            <a href="<?= site_url('inventory/stock') ?>" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Stok
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="tableHistory" style="width: 100%;">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 15%">Waktu</th>
                        <th style="width: 25%">Produk</th>
                        <th style="width: 10%" class="text-center">Tipe</th>
                        <th style="width: 10%" class="text-center">Jml</th>
                        <th>Keterangan</th>
                        <th style="width: 15%">User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
                    <tr>
                        <td>
                            <span style="display:none;"><?= $log->created_at ?></span> <?= date('d/m/Y H:i', strtotime($log->created_at)) ?>
                        </td>
                        <td>
                            <strong><?= $log->product_name ?></strong>
                        </td>
                        <td class="text-center">
                            <?php if($log->type == 'in'): ?>
                                <span class="badge badge-success">MASUK (IN)</span>
                            <?php else: ?>
                                <span class="badge badge-danger">KELUAR (OUT)</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center font-weight-bold">
                            <?= number_format($log->change_qty) ?>
                        </td>
                        <td>
                            <?= $log->description ?>
                        </td>
                        <td>
                            <i class="fas fa-user-circle text-muted"></i> <?= $log->full_name ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== 'undefined') {
        $('#tableHistory').DataTable({
            "responsive": true,
            "order": [[ 0, "desc" ]], // Urutkan dari yang terbaru
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    }
});
</script>