<style>
    .user-panel-modern {
        display: flex;
        align-items: center;
        padding: 20px 5px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 10px;
        transition: background 0.3s;
    }
    .user-panel-modern:hover {
        background: rgba(255,255,255,0.03);
    }
    .modern-img {
        width: 48px;
        height: 48px;
        border-radius: 12px; /* Squircle shape */
        object-fit: cover;
        margin-right: 15px;
        /*background: #fff;*/
        padding: 2px;
    }
    .modern-info {
        line-height: 1.3;
    }
    .modern-name {
        display: block;
        color: #fff;
        font-weight: 600;
        font-size: 15px;
    }
    .modern-role {
        color: #adb5bd;
        font-size: 12px;
        display: flex;
        align-items: center;
    }
    .modern-dot {
        height: 8px; width: 8px; 
        background-color: #28a745; 
        border-radius: 50%; 
        display: inline-block; 
        margin-right: 6px;
        box-shadow: 0 0 5px #28a745; /* Glowing effect */
    }
</style>

<div class="user-panel-modern">
    <img src="<?php echo base_url('assets/img/logo1.png'); ?>" class="modern-img" alt="User">
    <div class="modern-info">
        <span class="modern-name"><?= $this->session->userdata('full_name') ?? 'User'; ?></span>
        <span class="modern-role">
            <span class="modern-dot"></span> <?= ucfirst($this->session->userdata('role')); ?>
        </span>
    </div>
</div>

<?php 
    // Ambil Role User untuk Logika Tampilan
    $role = $this->session->userdata('role'); 
?>

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <li class="nav-item">
            <a href="<?= site_url('dashboard') ?>" class="nav-link active">
                <i class="nav-icon fas fa-home"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <?php if($role == 'admin' || $role == 'sales' || $role == 'owner'): ?>
        <li class="nav-header">PENJUALAN (SALES)</li>
        
        <li class="nav-item">
            <a href="<?= site_url('marketing/sales/create') ?>" class="nav-link">
                <i class="nav-icon fas fa-cash-register"></i>
                <p>Kasir / Transaksi Baru</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>Manajemen Sales <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= site_url('marketing/sales') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Riwayat Penjualan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('marketing/customers') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Database Pelanggan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('marketing/customers/map') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Peta Sebaran (GIS)</p>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'gudang' || $role == 'driver' || $role == 'owner'): ?>
        <li class="nav-header">GUDANG & LOGISTIK</li>
        
        <?php if($role != 'driver'): ?>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>Inventory <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= site_url('inventory/stock') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Stok Barang</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('purchasing/purchases') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Pembelian (PO)</p>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="<?= site_url('logistics/routes') ?>" class="nav-link">
                <i class="nav-icon fas fa-shipping-fast"></i>
                <p>Jadwal Pengiriman</p>
            </a>
        </li>
        <?php endif; ?>

        <?php if($role == 'admin'|| $role == 'owner'): ?>
        <li class="nav-header">KEUANGAN</li>
        <li class="nav-item">
            <a href="<?= site_url('finance/expenses') ?>" class="nav-link">
                <i class="fas fa-money-bill-wave nav-icon"></i>
                <p>Biaya Operasional</p>
            </a>
        </li>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'owner'): ?>
        <li class="nav-header">LAPORAN (REPORT)</li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Pusat Laporan <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= site_url('reports/sales') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Laporan Penjualan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('reports/stock') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Laporan Mutasi Stok</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('report/finance') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Laporan Keuangan</p>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'owner'): ?>
        <li class="nav-header">PENGATURAN SISTEM</li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Master Data <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= site_url('finance/settings') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Setup Keuangan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('master/products') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Produk & Harga</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('master/suppliers') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Data Supplier</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('master/categories') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Kategori Bisnis</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('master/users') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i> <p>Manajemen User</p>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

    </ul>
</nav>