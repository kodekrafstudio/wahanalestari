<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Penjualan</h3>
        <div class="card-tools">
            <a href="<?= site_url('marketing/orders/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-cart-plus"></i> Buat Order Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga @</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Sales</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($o->order_date)) ?></td>
                    <td><?= $o->customer_name ?></td>
                    <td><?= $o->product_name ?></td>
                    <td><?= $o->quantity ?> <?= $o->unit ?></td>
                    <td>Rp <?= number_format($o->price, 0, ',', '.') ?></td>
                    <td class="font-weight-bold">Rp <?= number_format($o->total, 0, ',', '.') ?></td>
                    <td>
                        <?php 
                        $badge = 'secondary';
                        if($o->status == 'request') $badge = 'warning';
                        if($o->status == 'preparing') $badge = 'info';
                        if($o->status == 'delivering') $badge = 'primary';
                        if($o->status == 'done') $badge = 'success';
                        if($o->status == 'canceled') $badge = 'danger';
                        ?>
                        <span class="badge badge-<?= $badge ?>"><?= ucfirst($o->status) ?></span>
                    </td>
                    <td><small><?= $o->sales_name ?></small></td>
                    <td>
                        <a href="<?= site_url('marketing/orders/edit/'.$o->order_id) ?>" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <?php if($o->status == 'request'): ?>
                            <a href="<?= site_url('marketing/orders/delete/'.$o->order_id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Batalkan order ini?')"><i class="fas fa-trash"></i></a>
                         <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>