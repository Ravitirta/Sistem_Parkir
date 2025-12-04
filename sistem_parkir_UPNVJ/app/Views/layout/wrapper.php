<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?> - Sistem Parkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Khusus untuk Tampilan Seperti Gambar */
        body { background-color: #f8f9fa; overflow-x: hidden; }
        
        .sidebar {
            background-color: #212529; /* Warna Gelap Sidebar */
            min-height: 100vh;
            color: white;
            padding-top: 20px;
        }
        
        .sidebar h4 { font-weight: bold; padding-left: 20px; margin-bottom: 5px; }
        .sidebar p { padding-left: 20px; color: #adb5bd; font-size: 0.9em; margin-bottom: 30px; }
        
        .nav-link {
            color: #ced4da;
            padding: 12px 20px;
            font-size: 1rem;
            display: block;
            text-decoration: none;
        }
        
        .nav-link:hover { background-color: #343a40; color: white; }
        .nav-link.active { background-color: #343a40; color: white; border-left: 4px solid #0d6efd; }
        
        .logout-link { color: #dc3545; margin-top: 40px; }
        .logout-link:hover { color: #ff6b6b; background: none; }

        .content-area { padding: 30px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-2 sidebar d-flex flex-column">
            <h4>Parkir UPNVJ</h4>
            <p>Halo, <?= session()->get('nama'); ?></p>

            <nav class="nav flex-column">
                <a class="nav-link <?= ($title == 'Transaksi Masuk') ? 'active' : '' ?>" href="/dashboard">Transaksi Masuk</a>
                <a class="nav-link" href="/dashboard/keluar">Keluar & Bayar</a>
                <a class="nav-link" href="/dashboard/status">Cek Status</a>
                <a class="nav-link" href="/dashboard/laporan">Laporan</a>
                <a class="nav-link" href="/dashboard/history">History</a>
                
                <a class="nav-link logout-link" href="/auth/logout">Logout</a>
            </nav>
        </div>

        <div class="col-md-10 content-area">
            <?= view($isi); ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Cek apakah ada session 'berhasil' dari Controller
        <?php if(session()->getFlashdata('berhasil')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('berhasil'); ?>',
                timer: 3000, // Otomatis tutup dalam 3 detik
                showConfirmButton: false
            });
        <?php endif; ?>

        // Cek apakah ada session 'gagal' dari Controller
        <?php if(session()->getFlashdata('gagal')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk!',
                text: '<?= session()->getFlashdata('gagal'); ?>',
            });
        <?php endif; ?>
    </script>


</body>
</html>
