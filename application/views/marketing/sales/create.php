<form action="<?= site_url('marketing/sales/create') ?>" method="post" id="formSales">
    <div class="row">
        
        <div class="col-lg-8 order-lg-1 order-2">
            
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-1"></i> Data Pelanggan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Transaksi</label>
                                <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Salesman</label>
                                <select name="salesman_id" class="form-control select2-cust" required style="width: 100%;">
                                    <option value="">- Pilih Sales -</option>
                                    <?php foreach($salesmen as $s): ?>
                                        <option value="<?= $s->user_id ?>" <?= ($s->user_id == $this->session->userdata('user_id')) ? 'selected' : '' ?>>
                                            <?= $s->full_name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Pelanggan</label>
                                <select name="customer_id" class="form-control select2-cust" required style="width: 100%;">
                                    <option value="">- Cari Pelanggan -</option>
                                    <?php foreach($customers as $c): ?>
                                        <option value="<?= $c->customer_id ?>"><?= $c->name ?> (<?= $c->address ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card card-outline card-primary shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h3 class="card-title"><i class="fas fa-cart-plus mr-1"></i> Input Pesanan</h3>
                </div>
                <div class="card-body">
                    
                    <div class="form-row align-items-end" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div class="col-md-5 col-12 mb-2">
                            <label class="small text-muted mb-1">Cari Produk</label>
                            <select id="temp_product" class="form-control select2-prod" style="width: 100%;">
                                <option value="" data-price="0" data-stock="0">- Ketik Nama Produk -</option>
                                <?php foreach($products as $p): ?>
                                    <option value="<?= $p->product_id ?>" 
                                            data-name="<?= $p->name ?>" 
                                            data-price="<?= $p->sell_price ?>"
                                            data-stock="<?= $p->stock ? $p->stock : 0 ?>">
                                        <?= $p->name ?> | Stok: <?= $p->stock ? $p->stock : 0 ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="d-block mt-1 font-weight-bold" id="stockInfo"></small>
                        </div>
                        
                        <div class="col-md-2 col-6 mb-2">
                            <label class="small text-muted mb-1">Harga Satuan</label>
                            <input type="number" id="temp_price" class="form-control" placeholder="0">
                        </div>
                        
                        <div class="col-md-2 col-6 mb-2">
                            <label class="small text-muted mb-1">Qty</label>
                            <input type="number" id="temp_qty" class="form-control" value="1" min="1">
                        </div>

                        <div class="col-md-2 col-6 mb-2">
                            <label class="small text-muted mb-1">Diskon Item</label>
                            <input type="number" id="temp_disc" class="form-control" value="0" placeholder="Rp">
                        </div>

                        <div class="col-md-1 col-6 mb-2">
                            <label class="d-none d-md-block small text-muted mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" id="btnAddItem" title="Tambah">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-right" width="15%">Harga</th>
                                    <th class="text-center" width="10%">Qty</th>
                                    <th class="text-right" width="15%">Disc</th>
                                    <th class="text-right" width="15%">Subtotal</th>
                                    <th class="text-center" width="5%"><i class="fas fa-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">
                                </tbody>
                        </table>
                        <div id="emptyCartMsg" class="text-center p-4 text-muted">
                            <i class="fas fa-shopping-basket fa-3x mb-3 text-gray-300"></i>
                            <p>Keranjang masih kosong.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-4 order-lg-2 order-1">
            <div class="card card-outline card-success shadow sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Ringkasan Bayar</h3>
                </div>
                <div class="card-body p-3">
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal Barang:</span>
                        <span class="font-weight-bold" id="lbl_subtotal">Rp 0</span>
                        <input type="hidden" id="disp_subtotal">
                        <input type="hidden" name="total_amount_raw" id="total_amount_raw" value="0">
                    </div>

                    <div class="form-group mb-2">
                        <label class="small mb-1">Biaya Kirim (Ongkir)</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="shipping_cost" id="shipping_cost" class="form-control text-right" value="0">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1">Potongan Lain</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="other_discount" id="other_discount" class="form-control text-right" value="0">
                        </div>
                    </div>

                    <div class="separator mb-3 border-bottom"></div>

                    <div class="text-center bg-light p-3 rounded mb-3">
                        <small class="text-muted text-uppercase font-weight-bold">Total Tagihan</small>
                        <h2 class="font-weight-bold text-success m-0" id="displayGrandTotal">Rp 0</h2>
                        <input type="hidden" name="grand_total" id="grand_total" value="0">
                    </div>

                    <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm" onclick="return confirm('Proses order ini sekarang?')">
                        <i class="fas fa-check-circle mr-2"></i> SELESAIKAN ORDER
                    </button>
                    
                    <a href="<?= site_url('marketing/sales') ?>" class="btn btn-default btn-block btn-sm mt-2 text-muted">Batal & Kembali</a>

                </div>
            </div>
        </div>

    </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<style>
    /* Agar tampilan Select2 menyatu dengan input bootstrap */
    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered { line-height: 36px !important; }
    /* Sticky footer effect di mobile jika mau */
    @media (max-width: 768px) {
        .card-body { padding: 1rem; }
    }
</style>

<script>
window.addEventListener('load', function() {
    // 1. Cek jQuery
    if (typeof $ === 'undefined') { alert('Error: jQuery belum dimuat.'); return; }

    // 2. Load Select2
    var script = document.createElement('script');
    script.src = "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js";
    script.onload = function() { initSalesPage(); };
    document.head.appendChild(script);
});

function initSalesPage() {
    // Init Plugins
    $('.select2-cust').select2({ theme: 'bootstrap4', width: '100%' });
    $('.select2-prod').select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Ketik nama produk...' });

    // Variables
    var inputProduct = $('#temp_product');
    var inputQty = $('#temp_qty');
    var inputPrice = $('#temp_price');
    var inputDisc = $('#temp_disc');
    var stockInfo = $('#stockInfo');
    var tableBody = $('#cartTable');
    var emptyMsg = $('#emptyCartMsg');

    // 1. Produk Dipilih
    inputProduct.on('change', function() {
        var opt = $(this).find('option:selected');
        var price = opt.data('price'); 
        var stock = opt.data('stock'); 

        if(price) inputPrice.val(price); else inputPrice.val('');
        
        if(stock !== undefined) {
            stockInfo.text("Stok: " + stock);
            stockInfo.removeClass().addClass(parseFloat(stock) <= 0 ? 'text-danger' : 'text-success');
        } else {
            stockInfo.text("");
        }
    });

    // 2. Tambah Item
    $('#btnAddItem').on('click', function(e) {
        e.preventDefault(); 
        
        var pid = inputProduct.val();
        var opt = inputProduct.find('option:selected');
        var pName = opt.data('name');
        var stock = parseFloat(opt.data('stock')) || 0;
        
        var qty = parseFloat(inputQty.val());
        var price = parseFloat(inputPrice.val());
        var disc = parseFloat(inputDisc.val()) || 0;

        if(!pid) { alert("Pilih produk dulu!"); return; }
        if(isNaN(qty) || qty <= 0) { alert("Qty minimal 1"); return; }
        if(isNaN(price)) { alert("Harga error"); return; }
        if(qty > stock) { alert("Stok kurang! Sisa: " + stock); return; }

        var subtotal = (price * qty) - disc;

        var row = `
            <tr>
                <td>
                    <span class="font-weight-bold text-primary">${pName}</span>
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
                    ${disc > 0 ? '-' + disc.toLocaleString() : '0'}
                    <input type="hidden" name="discount[]" value="${disc}">
                </td>
                <td class="text-right font-weight-bold">
                    ${subtotal.toLocaleString()}
                    <input type="hidden" name="subtotal[]" class="row-subtotal" value="${subtotal}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-xs btnRemove rounded-circle"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;
        
        tableBody.append(row);
        emptyMsg.hide(); // Sembunyikan pesan kosong
        calculateTotal();

        // Reset
        inputProduct.val('').trigger('change');
        inputQty.val(1); inputDisc.val(0); inputPrice.val(''); stockInfo.text('');
    });

    // 3. Hapus Item
    $(document).on('click', '.btnRemove', function() {
        $(this).closest('tr').remove();
        if($('#cartTable tr').length === 0) emptyMsg.show();
        calculateTotal();
    });

    // 4. Update Total
    $('#shipping_cost, #other_discount').on('input', function() { calculateTotal(); });

    function calculateTotal() {
        var totalBarang = 0;
        $('.row-subtotal').each(function() { totalBarang += parseFloat($(this).val()) || 0; });

        $('#lbl_subtotal').text("Rp " + totalBarang.toLocaleString());
        $('#total_amount_raw').val(totalBarang);

        var ongkir = parseFloat($('#shipping_cost').val()) || 0;
        var potLain = parseFloat($('#other_discount').val()) || 0;
        var grand = totalBarang + ongkir - potLain;

        $('#grand_total').val(grand);
        $('#displayGrandTotal').text("Rp " + grand.toLocaleString('id-ID'));
    }
}
</script>