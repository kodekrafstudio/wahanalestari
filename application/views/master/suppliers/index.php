<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-truck mr-1"></i> Data Supplier</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/suppliers/add') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Supplier
            </a>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table id="tableSuppliers" class="table table-hover text-nowrap">
            <thead class="bg-light">
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 40%">Informasi Supplier</th>
                    <th style="width: 30%">Kontak Person (PIC)</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($suppliers as $s): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <span class="font-weight-bold d-block text-primary"><?= $s->supplier_name ?></span>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i> <?= $s->address ? $s->address : '-' ?>
                        </small>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-dark"><i class="fas fa-user-tie mr-1 text-secondary"></i> <?= $s->pic_name ?></span>
                            <small class="text-muted"><i class="fas fa-phone mr-1"></i> <?= $s->phone ?></small>
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <a href="<?= site_url('master/suppliers/edit/'.$s->supplier_id) ?>" class="btn btn-default btn-sm" title="Edit Data">
                                <i class="fas fa-pencil-alt text-warning"></i>
                            </a>
                            <a href="<?= site_url('master/suppliers/delete/'.$s->supplier_id) ?>" class="btn btn-default btn-sm" onclick="return confirm('Hapus supplier ini? Data pembelian terkait mungkin akan error.')" title="Hapus">
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
    $("#tableSuppliers").DataTable({
      "responsive": true, 
      "autoWidth": false,
      "language": {
          "search": "Cari Supplier:",
          "zeroRecords": "Tidak ada data supplier"
      }
    });
  });
</script>