<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Login | Insan Wahana Lestari</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9; /* Warna background abu muda agar Card menonjol */
            margin: 0;
            padding: 0;
        }

        /* --- LAYOUT DESKTOP --- */
        .login-container {
            min-height: 100vh;
            display: flex;
        }

        /* Bagian Kiri (Gambar) */
        .bg-image {
            background-image: url('<?php echo base_url('assets/img/bg2.jpeg'); ?>'); /* Gambar Laut/Garam */
            background-size: cover;
            background-position: center;
            width: 60%;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 50px;
        }
        
        /* Overlay Gradient pada Gambar */
        .bg-image::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.2));
        }

        .hero-text {
            position: relative;
            color: white;
            z-index: 2;
        }

        /* Bagian Kanan (Form) */
        .login-section {
            width: 40%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        /* --- STYLING ELEMEN FORM --- */
        .brand-logo {
            color: #007bff;
            font-weight: 800;
            font-size: 32px;
            letter-spacing: -1px;
            margin-bottom: 5px;
            display: inline-block;
        }

        .login-title {
            font-weight: 700;
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .login-subtitle {
            color: #888;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-control {
            height: 55px; /* Input lebih tinggi enak di touch screen */
            border-radius: 12px;
            border: 1px solid #eee;
            background-color: #f8f9fa;
            font-size: 15px;
            padding-left: 20px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.1);
        }

        .label-input {
            font-weight: 600;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .btn-login {
            height: 55px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        /* --- ALERT --- */
        .alert-custom {
            border-radius: 10px;
            border: none;
            background-color: #ffe5e5;
            color: #d63384;
            font-size: 13px;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        /* --- RESPONSIVE MOBILE (Layaknya Aplikasi Native) --- */
        @media (max-width: 991px) {
            .bg-image { display: none; } /* Sembunyikan gambar di tablet/HP */
            
            .login-container {
                align-items: center;
                justify-content: center;
                background-color: #f4f6f9; /* Background abu */
                padding: 20px;
            }

            .login-section {
                width: 100%;
                background: transparent;
                padding: 0;
                align-items: flex-start; /* Agar form agak ke atas dikit */
                padding-top: 10vh;
            }

            .login-card {
                background: #ffffff;
                padding: 30px;
                border-radius: 20px; /* Sudut membulat modern */
                box-shadow: 0 15px 35px rgba(0,0,0,0.05); /* Bayangan halus */
            }

            .brand-logo { font-size: 28px; }
            .login-title { font-size: 22px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    
    <div class="bg-image d-none d-lg-flex">
        <div class="hero-text">
            <h1 style="font-weight: 700; font-size: 2rem;">Web App Insan Wahana Lestari</h1>
            <p style="font-size: 1rem; opacity: 0.9;">Sistem Manajemen Distribusi & Pemetaan Terintegrasi.</p>
            <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 20px;">
                <small>&copy; <?= date('Y') ?> PT Insan Wahana Lestari. All Rights Reserved.</small>
            </div>
        </div>
    </div>

    <div class="login-section">
        <div class="login-card">
            
            <div class="text-center mb-4">
                <div style="font-size: 40px; color: #007bff; margin-bottom: 10px;">
                    <i class="fas fa-globe-asia"></i>
                </div>
                <!-- <div class="brand-logo">IWL SYSTEM</div> -->
                <h4 class="login-title">Selamat Datang!</h4>
                <p class="login-subtitle">Masuk untuk mengelola bisnis Anda.</p>
            </div>

            <?php if($this->session->flashdata('message')): ?>
                <div class="alert alert-custom mb-4 fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i> 
                    <?= $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/login') ?>" method="post">
                
                <div class="form-group mb-4">
                    <label class="label-input">Email Address</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="contoh@iwl.com" required value="<?= set_value('email') ?>">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="label-input">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-login">
                    Masuk Sekarang <i class="fas fa-arrow-right ml-2"></i>
                </button>

            </form>

            <div class="mt-4 text-center">
                <a href="#" class="text-muted small" style="text-decoration: none;">Lupa Password? <span class="text-primary">Hubungi Admin</span></a>
            </div>

        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>