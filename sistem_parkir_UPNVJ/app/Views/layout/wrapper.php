<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?> - Sistem Parkir UPNVJ</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* --- GLOBAL STYLE --- */
        body { 
            background-color: #f3f4f6; 
            font-family: 'Poppins', sans-serif; 
            overflow-x: hidden; 
        }
        
        /* --- SIDEBAR STYLE --- */
        .sidebar { 
            background: #1e293b; 
            min-height: 100vh; 
            color: white; 
            padding-top: 30px;
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar h4 { 
            font-weight: 600; 
            padding-left: 20px; 
            margin-bottom: 5px; 
            font-size: 1.2rem; 
        }
        
        .sidebar p { 
            padding-left: 20px; 
            color: #94a3b8; 
            font-size: 0.85em; 
            margin-bottom: 40px; 
        }
        
        /* --- MENU LINK STYLE --- */
        .nav-link { 
            color: #cbd5e1; 
            padding: 12px 25px; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover { 
            background-color: rgba(255,255,255,0.05); 
            color: white; 
            padding-left: 30px; 
        }
        
        .nav-link.active { 
            background-color: rgba(56, 189, 248, 0.1); 
            color: #38bdf8; 
            border-left: 4px solid #38bdf8; 
            font-weight: 500;
        }

        /* --- BUTTON STYLE --- */
        .logout-link { color: #ef4444 !important; margin-top: 20px; }
        .logout-link:hover { background: rgba(239, 68, 68, 0.1) !important; }

        .login-btn { 
            background-color: #3b82f6; 
            color: white !important; 
            margin: 20px; 
            border-radius: 8px; 
            justify-content: center;
            font-weight: 500;
        }
        .login-btn:hover { 
            background-color: #2563eb; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .content-area { padding: 40px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- BAGIAN 1: SIDEBAR -->
        <div class="col-md-2 sidebar d-flex flex-column px-0">
            <h4><i class="bi bi-p-square-fill text-primary me-2"></i>Parkir UPNVJ</h4>
            
            <!-- TAMPILAN STATUS USER -->
            <?php if(session()->get('logged_in')): ?>
                <p><i class="bi bi-person-badge"></i> Petugas: <?= session()->get('nama'); ?></p>
            <?php else: ?>
                <p><i class="bi bi-info-circle"></i> Mode: Informasi Publik</p>
            <?php endif; ?>

            <nav class="nav flex-column">
                
                <!-- MENU 1: KHUSUS PETUGAS (Hanya Tampil Jika Login) -->
                <?php if(session()->get('logged_in')): ?>
                    <div class="small text-muted px-4 mb-2 mt-2">TRANSAKSI</div>
                    
                    <a class="nav-link <?= ($title == 'Transaksi Masuk') ? 'active' : '' ?>" href="/dashboard">
                        <i class="bi bi-box-arrow-in-right"></i> Transaksi Masuk
                    </a>
                    <a class="nav-link <?= ($title == 'Kendaraan Keluar (Update)') ? 'active' : '' ?>" href="/dashboard/update">
                        <i class="bi bi-box-arrow-left"></i> Keluar & Bayar
                    </a>
                    <a class="nav-link <?= ($title == 'Transaksi Keluar') ? 'active' : '' ?>" href="/dashboard/transaksiKeluar">
                        <i class="bi bi-cart-dash-fill"></i> Transaksi Keluar
                    </a>
                <?php endif; ?>

                <!-- MENU 2: PUBLIK (Selalu Tampil) -->
                <div class="small text-muted px-4 mb-2 mt-3">INFORMASI</div>

                <a class="nav-link <?= ($title == 'Cek Status Area Parkir') ? 'active' : '' ?>" href="/dashboard/status">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Cek Status Area
                </a>
                
                <a class="nav-link <?= ($title == 'Laporan Pelanggaran' || $title == 'Input Laporan' || $title == 'Verifikasi Laporan') ? 'active' : '' ?>" href="/pelanggaran">
                    <i class="bi bi-exclamation-triangle-fill"></i> Laporan Pelanggaran
                </a>

                <!-- MENU 3: LAPORAN (Khusus Petugas) -->
                <?php if(session()->get('logged_in')): ?>
                    <div class="small text-muted px-4 mb-2 mt-3">DATA</div>
                    
                    <a class="nav-link <?= ($title == 'Laporan Harian' || $title == 'Laporan Bulanan') ? 'active' : '' ?>" href="/dashboard/laporan">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                    <a class="nav-link <?= ($title == 'History') ? 'active' : '' ?>" href="/dashboard/history">
                        <i class="bi bi-clock-history"></i> History
                    </a>
                    
                    <!-- TOMBOL LOGOUT -->
                    <a class="nav-link logout-link" href="/auth/logout">
                        <i class="bi bi-power"></i> Logout Sistem
                    </a>
                <?php else: ?>
                    <!-- TOMBOL LOGIN (POP UP) -->
                    <a class="nav-link login-btn mt-5" href="javascript:void(0)" onclick="tampilkanLogin()">
                        <i class="bi bi-box-arrow-in-right"></i> Login Petugas
                    </a>
                <?php endif; ?>

            </nav>
        </div>

        <!-- BAGIAN 2: KONTEN UTAMA -->
        <div class="col-md-10 content-area">
            <?= view($isi); ?>
        </div>
    </div>
</div>

<!-- Library JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SCRIPT LOGIKA JAVASCRIPT -->
<script>
    // 1. Fungsi Menampilkan Pop-up Login
    function tampilkanLogin() {
        Swal.fire({
            title: 'Login Petugas Parkir',
            html: `
                <form id="formLogin" action="/auth/loginProcess" method="POST">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username petugas" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </form>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Masuk Sistem',
            confirmButtonColor: '#3b82f6',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: () => {
                // Submit form secara otomatis saat tombol diklik
                const form = document.getElementById('formLogin');
                form.submit();
            }
        });
    }

    // 2. Notifikasi Login Sukses/Gagal (Dari Controller Auth)
    <?php if(session()->getFlashdata('login_sukses')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil!',
            text: '<?= session()->getFlashdata('login_sukses'); ?>',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if(session()->getFlashdata('login_gagal')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '<?= session()->getFlashdata('login_gagal'); ?>',
        });
    <?php endif; ?>

    // 3. Notifikasi Fitur Lain (Transaksi, Pelanggaran, dll)
    <?php if(session()->getFlashdata('berhasil') || session()->getFlashdata('sukses')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= session()->getFlashdata('berhasil') ?? session()->getFlashdata('sukses'); ?>',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if(session()->getFlashdata('gagal')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= session()->getFlashdata('gagal'); ?>',
        });
    <?php endif; ?>
</script>

</body>
</html>

