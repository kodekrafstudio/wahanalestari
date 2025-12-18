<form action="<?= site_url('marketing/sales/create') ?>" method="post" id="formSales">
    <div class="row">
        
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Info Faktur</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Pelanggan</label>
                        <select name="customer_id" class="form-control select2-cust" required style="width: 100%;">
                            <option value="">- Pilih -</option>
                            <?php foreach($customers as $c): ?>
                                <option value="<?= $c->customer_id ?>"><?= $c->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Salesman</label>
                        <select name="salesman_id" class="form-control select2-cust" required style="width: 100%;">
                            <option value="">- Pilih Salesman -</option>
                            <?php foreach($salesmen as $s): ?>
                                <option value="<?= $s->user_id ?>"><?= $s->full_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <hr>
                    <div class="form-group row mb-1">
                        <label class="col-6 col-form-label">Subtotal</label>
                        <div class="col-6">
                            <input type="text" id="disp_subtotal" class="form-control text-right form-control-sm bg-light" readonly value="0">
                            <input type="hidden" name="total_amount_raw" id="total_amount_raw" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group row mb-1">
                        <label class="col-6 col-form-label">Ongkir (+)</label>
                        <div class="col-6">
                            <input type="number" name="shipping_cost" id="shipping_cost" class="form-control text-right form-control-sm" value="0">
                        </div>
                    </div>

                    <div class="form-group row mb-1">
                        <label class="col-6 col-form-label">Disc Lain (-)</label>
                        <div class="col-6">
                            <input type="number" name="other_discount" id="other_discount" class="form-control text-right form-control-sm" value="0">
                        </div>
                    </div>

                    <div class="alert alert-info text-center mt-3">
                        <small>Total Akhir</small>
                        <h3 class="font-weight-bold m-0" id="displayGrandTotal">Rp 0</h3>
                        <input type="hidden" name="grand_total" id="grand_total" value="0">
                    </div>

                    <button type="submit" class="btn btn-success btn-block btn-lg mt-3" onclick="return confirm('Pastikan data benar. Simpan transaksi?')">
                        <i class="fas fa-save"></i> PROSES ORDER
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-default">
                <div class="card-header bg-light"><h3 class="card-title">Keranjang Belanja</h3></div>
                <div class="card-body">
                    
                    <div class="row bg-light p-2 mb-3 border rounded align-items-end">
                        <div class="col-md-4">
                            <label>Produk</label>
                            <select id="temp_product" class="form-control select2-prod" style="width: 100%;">
                                <option value="" data-price="0" data-stock="0">- Cari Produk -</option>
                                <?php foreach($products as $p): ?>
                                    <option value="<?= $p->product_id ?>" 
                                            data-name="<?= $p->name ?>" 
                                            data-price="<?= $p->sell_price ?>"
                                            data-stock="<?= $p->stock ? $p->stock : 0 ?>">
                                        <?= $p->name ?> (Stok: <?= $p->stock ? $p->stock : 0 ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-danger font-weight-bold" id="stockInfo"></small>
                        </div>
                        <div class="col-md-2">
                            <label>Qty</label>
                            <input type="number" id="temp_qty" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label>Harga</label>
                            <input type="number" id="temp_price" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label>Disc (Rp)</label>
                            <input type="number" id="temp_disc" class="form-control" value="0">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" id="btnAddItem"><b><i class="fas fa-plus"></i></b></button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Disc</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<script>
// Tunggu sampai seluruh halaman & jQuery Template selesai dimuat
window.addEventListener('load', function() {
    
    // 1. Cek apakah jQuery template sudah siap
    if (typeof $ === 'undefined') {
        alert('Error Fatal: jQuery tidak terdeteksi di Template AdminLTE Anda.');
        return;
    }

    // 2. Load Script Select2 Secara Dinamis (Agar urutannya benar setelah jQuery)
    var script = document.createElement('script');
    script.src = "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js";
    
    script.onload = function() {
        // 3. Setelah Select2 siap, baru jalankan Logic Aplikasi
        console.log("Select2 Loaded Successfully!");
        initSalesPage();
    };
    
    document.head.appendChild(script);
});

// --- FUNGSI UTAMA SALES (Dijalankan setelah semua siap) ---
function initSalesPage() {
    
    // Init Select2
    $('.select2-cust').select2({ theme: 'bootstrap4' });
    $('.select2-prod').select2({ theme: 'bootstrap4', placeholder: 'Cari Produk...' });

    // Definisi Variabel
    var inputProduct = $('#temp_product');
    var inputQty     = $('#temp_qty');
    var inputPrice   = $('#temp_price');
    var inputDisc    = $('#temp_disc');
    var stockInfo    = $('#stockInfo');
    var tableBody    = $('#cartTable');

    // EVENT 1: Saat Produk Dipilih -> Isi Harga & Stok Otomatis
    inputProduct.on('change', function() {
        var opt   = $(this).find('option:selected');
        var price = opt.data('price'); 
        var stock = opt.data('stock'); 

        if(price) inputPrice.val(price); else inputPrice.val('');
        
        if(stock !== undefined) {
            stockInfo.text("Sisa Stok: " + stock);
            // Visual feedback jika stok habis
            if(parseFloat(stock) <= 0) {
                stockInfo.removeClass('text-info').addClass('text-danger font-weight-bold');
            } else {
                stockInfo.removeClass('text-danger font-weight-bold').addClass('text-info');
            }
        } else {
            stockInfo.text("");
        }
    });

    // EVENT 2: Tombol Tambah Item Diklik
    $('#btnAddItem').on('click', function(e) {
        e.preventDefault(); 
        
        var pid   = inputProduct.val();
        var opt   = inputProduct.find('option:selected');
        var pName = opt.data('name');
        var stock = parseFloat(opt.data('stock')) || 0;
        
        var qty   = parseFloat(inputQty.val());
        var price = parseFloat(inputPrice.val());
        var disc  = parseFloat(inputDisc.val()) || 0;

        // Validasi
        if(!pid) { alert("Pilih produk dulu!"); return; }
        if(isNaN(qty) || qty <= 0) { alert("Qty wajib diisi!"); return; }
        if(isNaN(price)) { alert("Harga wajib diisi!"); return; }
        
        // Cek Stok (Validasi Client Side)
        if(qty > stock) { 
            alert("Stok tidak mencukupi! Hanya tersedia: " + stock); 
            return; 
        }

        var subtotal = (price * qty) - disc;

        var row = `
            <tr>
                <td>
                    <strong>${pName}</strong>
                    <input type="hidden" name="product_id[]" value="${pid}">
                </td>
                <td class="text-right">
                    ${price.toLocaleString()}
                    <input type="hidden" name="price[]" value="${price}">
                </td>
                <td class="text-center">
                    ${qty}
                    <input type="hidden" name="qty[]" value="${qty}">
                </td>
                <td class="text-right text-danger">
                    ${disc.toLocaleString()}
                    <input type="hidden" name="discount[]" value="${disc}">
                </td>
                <td class="text-right">
                    <strong>${subtotal.toLocaleString()}</strong>
                    <input type="hidden" name="subtotal[]" class="row-subtotal" value="${subtotal}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs btnRemove"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        
        tableBody.append(row);
        calculateTotal();

        // Reset Input
        inputProduct.val('').trigger('change');
        inputQty.val(1); 
        inputDisc.val(0); 
        inputPrice.val(''); 
        stockInfo.text('');
    });

    // EVENT 3: Hapus Baris
    $(document).on('click', '.btnRemove', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // EVENT 4: Update Total Realtime
    $('#shipping_cost, #other_discount').on('input', function() { 
        calculateTotal(); 
    });

    // FUNGSI HITUNG TOTAL
    function calculateTotal() {
        var totalBarang = 0;
        $('.row-subtotal').each(function() { 
            totalBarang += parseFloat($(this).val()) || 0; 
        });

        $('#disp_subtotal').val(totalBarang.toLocaleString());
        $('#total_amount_raw').val(totalBarang);

        var ongkir  = parseFloat($('#shipping_cost').val()) || 0;
        var potLain = parseFloat($('#other_discount').val()) || 0;
        var grand   = totalBarang + ongkir - potLain;

        $('#grand_total').val(grand);
        $('#displayGrandTotal').text("Rp " + grand.toLocaleString('id-ID'));
    }
}
</script>