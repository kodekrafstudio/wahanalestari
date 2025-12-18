<form action="<?= site_url('purchasing/purchases/create') ?>" method="post">
    <div class="row">
        
        <div class="col-md-4">
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-contract mr-1"></i> Data Pembelian</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tanggal PO</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div>
                            <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">- Pilih Supplier -</option>
                            <?php foreach($suppliers as $s): ?>
                                <option value="<?= $s->supplier_id ?>"><?= $s->supplier_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="callout callout-info mt-4">
                        <small class="text-muted text-uppercase font-weight-bold">Total Pembelian</small>
                        <h3 class="text-primary font-weight-bold" id="displayGrandTotal">Rp 0</h3>
                        <input type="hidden" name="grand_total" id="grand_total" value="0">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-3" onclick="return confirm('Simpan data pembelian ini? Stok akan bertambah.')">
                        <i class="fas fa-save mr-1"></i> SIMPAN PO
                    </button>
                    <a href="<?= site_url('purchasing/purchases') ?>" class="btn btn-default btn-block mt-2">Batal</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-outline card-success h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cubes mr-1"></i> Daftar Barang</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="btnAddRow">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40%">Nama Barang</th>
                                <th style="width: 15%" class="text-center">Qty</th>
                                <th style="width: 20%" class="text-right">Harga Beli</th>
                                <th style="width: 20%" class="text-right">Subtotal</th>
                                <th style="width: 5%" class="text-center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPO">
                            <tr class="row-item">
                                <td>
                                    <select name="product_id[]" class="form-control form-control-sm product-select" required style="width: 100%;">
                                        <option value="">- Pilih Barang -</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?= $p->product_id ?>" 
                                                    data-price="<?= ((float)$p->last_purchase_price > 0) ? (float)$p->last_purchase_price : $p->base_cost ?>">
                                                <?= $p->name ?> (<?= $p->unit ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="qty[]" class="form-control form-control-sm qty-input text-center" min="1" value="1" required>
                                </td>
                                <td>
                                    <input type="number" name="price[]" class="form-control form-control-sm price-input text-right" required placeholder="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm subtotal-input text-right bg-light" readonly value="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-danger btn-remove"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Harga Beli akan otomatis terisi berdasarkan pembelian terakhir atau HPP master data.</small>
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
        console.error('jQuery belum dimuat di Template Utama.');
        return;
    }

    // 2. Load Script Select2 Secara Dinamis (Agar aman dan urutannya benar)
    var script = document.createElement('script');
    script.src = "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js";
    
    script.onload = function() {
        // 3. Jalankan Logic Aplikasi HANYA setelah Select2 siap
        initPurchasePage();
    };
    
    document.head.appendChild(script);
});

// --- FUNGSI UTAMA HALAMAN PO ---
function initPurchasePage() {
    
    // --- 1. INISIALISASI ---
    // Init Select2 untuk Supplier
    $('select[name="supplier_id"]').select2({ theme: 'bootstrap4' });

    // Init Select2 untuk baris pertama
    initRowPlugins($('.row-item'));

    // --- 2. FUNGSI PENDUKUNG ---
    
    // Fungsi menghidupkan Select2 pada baris tertentu
    function initRowPlugins(rowElement) {
        rowElement.find('.product-select').select2({ 
            theme: 'bootstrap4', 
            placeholder: 'Cari barang...',
            width: '100%' 
        });
    }

    // Hitung per baris
    function calculateRow(row) {
        var qty   = parseFloat(row.find('.qty-input').val()) || 0;
        var price = parseFloat(row.find('.price-input').val()) || 0;
        var sub   = qty * price;
        
        row.find('.subtotal-input').val(sub.toLocaleString('id-ID'));
        
        // Simpan nilai mentah untuk kalkulasi total
        row.find('.subtotal-input').data('raw-val', sub);
        
        calculateTotal();
    }

    // Hitung Grand Total
    function calculateTotal() {
        var total = 0;
        $('.subtotal-input').each(function() {
            var val = $(this).data('raw-val');
            // Fallback jika data belum ada (manual input)
            if(val === undefined) {
                val = parseFloat($(this).val().replace(/\./g, '').replace(/,/g, '.')) || 0;
            }
            total += val;
        });
        
        $('#grand_total').val(total);
        $('#displayGrandTotal').text("Rp " + total.toLocaleString('id-ID'));
    }

    // --- 3. EVENT HANDLERS ---

    // A. Saat Produk Dipilih (Pakai Event Select2)
    $(document).on('select2:select', '.product-select', function(e) {
        var data  = e.params.data.element; 
        var price = $(data).data('price'); 
        var row   = $(this).closest('tr'); 
        
        // Isi harga otomatis jika ada
        if(price > 0) {
            row.find('.price-input').val(price);
        }
        calculateRow(row);
    });

    // B. Hitung saat ketik Qty / Harga
    $(document).on('input', '.qty-input, .price-input', function() {
        var row = $(this).closest('tr');
        calculateRow(row);
    });

    // C. Tambah Baris Baru
    $('#btnAddRow').on('click', function() {
        var html = `
            <tr class="row-item">
                <td>
                    <select name="product_id[]" class="form-control form-control-sm product-select" required>
                        <option value="">- Pilih Barang -</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p->product_id ?>" 
                                    data-price="<?= ((float)$p->last_purchase_price > 0) ? (float)$p->last_purchase_price : (float)$p->base_cost ?>">
                                
                                <?= $p->name ?> (<?= $p->unit ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="qty[]" class="form-control form-control-sm qty-input text-center" min="1" value="1" required></td>
                <td><input type="number" name="price[]" class="form-control form-control-sm price-input text-right" required></td>
                <td><input type="text" class="form-control form-control-sm subtotal-input text-right bg-light" readonly value="0"></td>
                <td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-remove"><i class="fas fa-times"></i></button></td>
            </tr>`;
        
        var newRow = $(html);
        $('#tbodyPO').append(newRow);
        
        // Aktifkan Select2 di baris baru
        initRowPlugins(newRow);
    });

    // D. Hapus Baris
    $(document).on('click', '.btn-remove', function() {
        if($('#tbodyPO tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotal();
        } else {
            alert("Minimal 1 barang.");
        }
    });
}
</script>