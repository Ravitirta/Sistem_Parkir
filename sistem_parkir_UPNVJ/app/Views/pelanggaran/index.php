<h2 class="mb-4">Daftar Pelanggaran Parkir</h2>

<?php if(session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4">
    <!-- Tombol untuk Umum -->
    <a href="/pelanggaran/lapor" class="btn btn-primary btn-lg">
        <i class="bi bi-camera-fill"></i> Masukkan Laporan
    </a>

    <!-- Tombol Khusus Petugas (Cek Session) -->
    <?php if(session()->get('logged_in')): ?>
        <a href="/pelanggaran/manage" class="btn btn-warning btn-lg text-white">
            <i class="bi bi-pencil-square"></i> Kelola Laporan Masuk
        </a>
    <?php endif; ?>
</div>

<!-- Tabel Laporan yang VALID -->
<div class="row">
    <?php foreach($data_valid as $row): ?>
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
            <!-- Menampilkan Foto -->
            <img src="/uploads/<?= $row['foto']; ?>" class="card-img-top" alt="Pelanggaran" style="height: 200px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title text-danger">Pelanggaran Area <?= $row['nama_area']; ?></h5>
                <p class="card-text text-muted small">
                    <i class="bi bi-calendar"></i> <?= $row['tanggal']; ?>
                </p>
                <p class="card-text"><?= $row['keterangan']; ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>