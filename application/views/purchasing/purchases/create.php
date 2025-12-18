<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Input Pembelian Baru</h3></div>
    <form action="<?= site_url('purchasing/purchases/create') ?>" method="post">
        <div class="card-body">
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal PO</label>
                        <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control select2" required>
                            <option value="">- Pilih Supplier -</option>
                            <?php foreach($suppliers as $s): ?>
                                <option value="<?= $s->supplier_id ?>"><?= $s->supplier_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Grand Total</label>
                        <input type="text" name="grand_total" id="grand_total" class="form-control text-right bg-light font-weight-bold" readonly value="0">
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-sm mt-3">
                <thead class="bg-light">
                    <tr>
                        <th width="35%">Barang</th>
                        <th width="15%">Qty</th>
                        <th width="20%">Harga Beli (@)</th>
                        <th width="20%">Subtotal</th>
                        <th class="text-center"><button type="button" class="btn btn-xs btn-success" id="btnAdd"><i class="fas fa-plus"></i></button></th>
                    </tr>
                </thead>
                <tbody id="tbodyPO">
                    <tr class="row-item">
                        <td>
                            <select name="product_id[]" class="form-control product-select" required>
                                <option value="">- Cari Barang -</option>
                                <?php foreach($products as $p): ?>
                                    <option value="<?= $p->product_id ?>" 
                                            data-price="<?= ($p->last_purchase_price > 0) ? $p->last_purchase_price : $p->base_cost ?>">
                                        <?= $p->name ?> (<?= $p->unit ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="qty[]" class="form-control qty-input" min="1" value="1" required></td>
                        <td><input type="text" name="price[]" class="form-control price-input text-right" required></td>
                        <td><input type="text" name="subtotal[]" class="form-control subtotal-input text-right bg-light" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-remove"><i class="fas fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan PO</button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    function calculateRow(row) {
        var qty   = parseFloat(row.find('.qty-input').val()) || 0;
        var price = parseFloat(row.find('.price-input').val().replace(/[^0-9]/g, '')) || 0;
        row.find('.subtotal-input').val(qty * price);
        calculateTotal();
    }

    function calculateTotal() {
        var total = 0;
        $('.subtotal-input').each(function() { total += parseFloat($(this).val()) || 0; });
        $('#grand_total').val(total);
    }

    // Saat Produk Dipilih
    $(document).on('change', '.product-select', function() {
        var price = $(this).find(':selected').data('price');
        var row   = $(this).closest('tr');
        row.find('.price-input').val(price);
        calculateRow(row);
    });

    $(document).on('input', '.qty-input, .price-input', function() {
        calculateRow($(this).closest('tr'));
    });

    // Tambah Baris
    $('#btnAdd').click(function() {
        var html = `
            <tr class="row-item">
                <td>
                    <select name="product_id[]" class="form-control product-select" required>
                        <option value="">- Cari Barang -</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p->product_id ?>" data-price="<?= ($p->last_purchase_price > 0) ? $p->last_purchase_price : $p->base_cost ?>">
                                <?= $p->name ?> (<?= $p->unit ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="qty[]" class="form-control qty-input" min="1" value="1" required></td>
                <td><input type="text" name="price[]" class="form-control price-input text-right" required></td>
                <td><input type="text" name="subtotal[]" class="form-control subtotal-input text-right bg-light" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-remove"><i class="fas fa-trash"></i></button></td>
            </tr>`;
        $('#tbodyPO').append(html);
    });

    $(document).on('click', '.btn-remove', function() {
        if($('#tbodyPO tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotal();
        }
    });
});
</script>