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
        /* CSS Khusus Tampilan Modern */
        body { 
            background-color: #f3f4f6; /* Abu muda yang bersih */
            font-family: 'Poppins', sans-serif; 
            overflow-x: hidden; 
        }
        
        /* Sidebar Styling */
        .sidebar {
            background: #1e293b; /* Biru gelap elegan */
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
        }
        
        .sidebar h4 { 
            font-weight: 600; 
            padding-left: 20px; 
            margin-bottom: 5px; 
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }
        
        .sidebar p { 
            padding-left: 20px; 
            color: #94a3b8; 
            font-size: 0.85em; 
            margin-bottom: 40px; 
        }
        
        /* Menu Link Styling */
        .nav-link {
            color: #cbd5e1;
            padding: 12px 25px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .nav-link:hover { 
            background-color: rgba(255,255,255,0.05); 
            color: white; 
            padding-left: 30px;
        }
        
        .nav-link.active { 
            background-color: rgba(56, 189, 248, 0.1); 
            color: #38bdf8; /* Biru muda */
            border-left: 4px solid #38bdf8; 
            font-weight: 500;
        }
        
        .nav-link i { font-size: 1.1rem; }
        
        /* Tombol Logout */
        .logout-link { 
            color: #ef4444 !important; /* Merah */
            margin-top: 40px; 
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }
        .logout-link:hover { background: rgba(239, 68, 68, 0.1) !important; }

        .content-area { padding: 40px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar d-flex flex-column px-0">
            <h4><i class="bi bi-p-square-fill text-primary me-2"></i>Parkir UPNVJ</h4>
            <p>Petugas: <?= session()->get('nama'); ?></p>

            <nav class="nav flex-column">
                <a class="nav-link <?= ($title == 'Transaksi Masuk') ? 'active' : '' ?>" href="/dashboard">
                    <i class="bi bi-box-arrow-in-right"></i> Transaksi Masuk
                </a>
                <a class="nav-link <?= ($title == 'Kendaraan Keluar (Update)') ? 'active' : '' ?>" href="/dashboard/update">
                    <i class="bi bi-box-arrow-left"></i> Keluar & Bayar
                </a>
                
                <a class="nav-link <?= ($title == 'Transaksi Keluar') ? 'active' : '' ?>" href="/dashboard/transaksiKeluar">
                    <i class="bi bi-cart-dash-fill"></i> Transaksi Keluar
                </a>
                <a class="nav-link <?= ($title == 'Cek Status Area Parkir') ? 'active' : '' ?>" href="/dashboard/status">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Cek Status
                </a>

                <a class="nav-link <?= ($title == 'Laporan Pelanggaran' || $title == 'Input Laporan' || $title == 'Verifikasi Laporan') ? 'active' : '' ?>" href="/pelanggaran">
                    <i class="bi bi-exclamation-triangle-fill"></i> Pelanggaran
                </a>
                
                <a class="nav-link <?= ($title == 'Laporan Harian' || $title == 'Laporan Bulanan') ? 'active' : '' ?>" href="/dashboard/laporan">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
                <a class="nav-link <?= ($title == 'History') ? 'active' : '' ?>" href="/dashboard/history">
                    <i class="bi bi-clock-history"></i> History
                </a>
                
                <a class="nav-link logout-link" href="/auth/logout">
                    <i class="bi bi-power"></i> Logout
                </a>
            </nav>
        </div>

        <!-- AREA KONTEN UTAMA -->
        <div class="col-md-10 content-area">
            <!-- Menampilkan Konten Sesuai Variabel $isi -->
            <?= view($isi); ?>
        </div>

    </div>
</div>

<!-- Script JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Logika Notifikasi -->
<script>
    <?php if(session()->getFlashdata('berhasil')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= session()->getFlashdata('berhasil'); ?>',
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

