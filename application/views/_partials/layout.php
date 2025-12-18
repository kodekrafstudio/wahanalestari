<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view('_partials/header'); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        
        <div class="sidebar">
            <?php $this->load->view('_partials/sidebar'); ?>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                <?= $content_body ?>
            </div>
        </section>
        </div>
    <footer class="main-footer">
        <?php $this->load->view('_partials/footer'); ?>
    </footer>

</div>
<?php $this->load->view('_partials/js'); ?>

</body>
</html>