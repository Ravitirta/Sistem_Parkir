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
            padding-top: 25px; /* Jarak atas sedikit dikurangi */
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar h4 { 
            font-weight: 600; 
            padding-left: 20px; 
            margin-bottom: 15px; 
            font-size: 1.2rem; 
            color: white;
        }
        
        /* BOX PROFIL (PERBAIKAN JARAK) */
        .profile-box {
            padding: 10px 20px;
            margin-bottom: 10px; /* Jarak ke menu diperkecil (sebelumnya 40px) */
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .profile-box p {
            color: #94a3b8; 
            font-size: 0.85em; 
            margin: 0; /* Hilangkan margin default paragraf */
        }
        
        /* --- MENU LINK STYLE --- */
        .nav-link { 
            color: #cbd5e1; 
            padding: 10px 25px; /* Padding sedikit diperkecil agar lebih padat */
            display: flex; 
            align-items: center; 
            gap: 12px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
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

        /* LABEL PEMISAH MENU (PERBAIKAN JARAK) */
        .menu-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-top: 15px;    /* Jarak antar kelompok menu diatur disini */
            margin-bottom: 5px;
            padding-left: 25px;
            font-weight: 600;
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
        }

        .content-area { padding: 40px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- BAGIAN 1: SIDEBAR -->
        <div class="col-md-2 sidebar d-flex flex-column px-0">
            <!-- JUDUL -->
            <h4><i class="bi bi-p-square-fill text-primary me-2"></i>Parkir UPNVJ</h4>
            
            <!-- PROFIL / INFO LOGIN (Desain Baru Lebih Rapat) -->
            <div class="profile-box">
                <?php if(session()->get('logged_in')): ?>
                    <p><i class="bi bi-person-badge text-success me-1"></i> <?= session()->get('nama'); ?> (Petugas)</p>
                <?php else: ?>
                    <p><i class="bi bi-globe text-info me-1"></i> Akses Publik</p>
                <?php endif; ?>
            </div>

            <nav class="nav flex-column">
                
                <!-- MENU 1: KHUSUS PETUGAS -->
                <?php if(session()->get('logged_in')): ?>
                    <div class="menu-label">TRANSAKSI</div>
                    
                    <a class="nav-link <?= ($title == 'Transaksi Masuk') ? 'active' : '' ?>" href="/dashboard">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </a>
                    <a class="nav-link <?= ($title == 'Kendaraan Keluar (Update)') ? 'active' : '' ?>" href="/dashboard/update">
                        <i class="bi bi-box-arrow-left"></i> Keluar & Bayar
                    </a>
                   
                <?php endif; ?>

                <!-- MENU 2: PUBLIK (Jarak diatur oleh .menu-label) -->
                <div class="menu-label">INFORMASI AREA</div>

                <a class="nav-link <?= ($title == 'Cek Status Area Parkir') ? 'active' : '' ?>" href="/dashboard/status">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Cek Status
                </a>
                
                <a class="nav-link <?= ($title == 'Laporan Pelanggaran' || $title == 'Input Laporan' || $title == 'Verifikasi Laporan') ? 'active' : '' ?>" href="/pelanggaran">
                    <i class="bi bi-exclamation-triangle-fill"></i> Pelanggaran
                </a>

                <!-- MENU 3: DATA (Khusus Petugas) -->
                <?php if(session()->get('logged_in')): ?>
                    <div class="menu-label">REKAP DATA</div>
                    
                    <a class="nav-link <?= ($title == 'Laporan Harian' || $title == 'Laporan Bulanan') ? 'active' : '' ?>" href="/dashboard/laporan">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                    <a class="nav-link <?= ($title == 'History') ? 'active' : '' ?>" href="/dashboard/history">
                        <i class="bi bi-clock-history"></i> Riwayat
                    </a>
                    
                    <!-- TOMBOL LOGOUT -->
                    <a class="nav-link logout-link" href="/auth/logout">
                        <i class="bi bi-power"></i> Logout
                    </a>
                <?php else: ?>
                    <!-- TOMBOL LOGIN -->
                    <a class="nav-link login-btn mt-4" href="javascript:void(0)" onclick="tampilkanLogin()">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login Petugas
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
    // 1. Fungsi Pop-up Login
    function tampilkanLogin() {
        Swal.fire({
            title: 'Login Petugas',
            html: `
                <form id="formLogin" action="/auth/loginProcess" method="POST">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Masuk',
            confirmButtonColor: '#3b82f6',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                document.getElementById('formLogin').submit();
            }
        });
    }

    // 2. Notifikasi
    <?php if(session()->getFlashdata('login_sukses')): ?>
        Swal.fire({ icon: 'success', title: 'Login Berhasil!', text: '<?= session()->getFlashdata('login_sukses'); ?>', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    <?php if(session()->getFlashdata('login_gagal')): ?>
        Swal.fire({ icon: 'error', title: 'Login Gagal', text: '<?= session()->getFlashdata('login_gagal'); ?>' });
    <?php endif; ?>

    <?php if(session()->getFlashdata('berhasil') || session()->getFlashdata('sukses')): ?>
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= session()->getFlashdata('berhasil') ?? session()->getFlashdata('sukses'); ?>', timer: 3000, showConfirmButton: false });
    <?php endif; ?>

    <?php if(session()->getFlashdata('gagal')): ?>
        Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= session()->getFlashdata('gagal'); ?>' });
    <?php endif; ?>
</script>

</body>
</html>
