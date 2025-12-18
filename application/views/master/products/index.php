<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Daftar Produk</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/products/add') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Produk
            </a>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table id="tableProducts" class="table table-hover text-nowrap">
            <thead class="bg-light">
                <tr>
                    <th style="width: 5%">#</th>
                    <th>Nama Produk</th>
                    <th>Kategori & Grade</th>
                    <th>Satuan</th>
                    <th class="text-right">Harga Beli</th>
                    <th class="text-right">HPP (Modal)</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-right">Est. Laba</th>
                    <th class="text-right" style="width: 100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($products as $p): 
                    $margin = $p->sell_price - $p->base_cost;
                    $persen = ($p->base_cost > 0) ? ($margin / $p->base_cost) * 100 : 0;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <span class="font-weight-bold text-dark"><?= $p->name ?></span>
                    </td>
                    <td>
                        <span class="badge badge-info"><?= strtoupper($p->type) ?></span>
                        <span class="text-muted ml-1"><?= $p->grade ?></span>
                    </td>
                    <td><?= $p->unit ?></td>
                    <td class="text-right text-muted">
                        Rp <?= number_format($p->last_purchase_price, 0, ',', '.') ?>
                    </td>
                    <td class="text-right text-muted">
                        Rp <?= number_format($p->base_cost, 0, ',', '.') ?>
                    </td>
                    <td class="text-right font-weight-bold text-success">
                        Rp <?= number_format($p->sell_price, 0, ',', '.') ?>
                    </td>
                    <td class="text-right">
                        <small class="text-success">
                            +<?= number_format($margin, 0, ',', '.') ?> (<?= round($persen) ?>%)
                        </small>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <a href="<?= site_url('master/products/edit/'.$p->product_id) ?>" class="btn btn-default btn-sm" title="Edit">
                                <i class="fas fa-pencil-alt text-warning"></i>
                            </a>
                            <a href="<?= site_url('master/products/delete/'.$p->product_id) ?>" class="btn btn-default btn-sm" onclick="return confirm('Hapus produk ini? Stok gudang akan ikut hilang.')" title="Hapus">
                                <i class="fas fa-trash text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
  $(function () {
    $("#tableProducts").DataTable({
      "responsive": true, 
      "autoWidth": false,
      "language": { "search": "Cari Produk:" }
    });
  });
</script>