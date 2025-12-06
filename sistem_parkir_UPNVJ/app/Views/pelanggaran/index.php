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

<div class="row">
    <?php if(empty($data_valid)): ?>
        <div class="col-12 text-center py-5">
            <h4 class="text-muted">Belum ada data pelanggaran.</h4>
        </div>
    <?php else: ?>
        <?php foreach($data_valid as $row): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 overflow-hidden">
                
                <div class="position-relative">
                    <!-- FOTO -->
                    <img src="/uploads/<?= $row['foto']; ?>" class="card-img-top" alt="Pelanggaran" 
                         style="height: 250px; object-fit: cover; cursor: pointer;"
                         onclick="lihatFoto('/uploads/<?= $row['foto']; ?>')">
                    
                    <!-- TOMBOL HAPUS (HANYA MUNCUL JIKA LOGIN) -->
                    <?php if(session()->get('logged_in')): ?>
                        <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus"
                                data-href="/pelanggaran/hapus/<?= $row['id_pelanggaran']; ?>"
                                title="Hapus Laporan Ini">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </button>
                    <?php endif; ?>

                    <div class="position-absolute bottom-0 start-0 w-100 p-2 text-white" 
                         style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                         <span class="badge bg-danger mb-1"><?= $row['nama_area']; ?></span>
                    </div>
                </div>

                <div class="card-body">
                    <small class="text-muted d-block mb-2">
                        <i class="bi bi-calendar-check"></i> <?= date('d F Y', strtotime($row['tanggal'])); ?>
                    </small>
                    <p class="card-text fw-medium">"<?= esc($row['keterangan']); ?>"</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- SCRIPT HAPUS & ZOOM -->
<script>
    // 1. Logic Zoom Foto
    function lihatFoto(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Bukti Pelanggaran',
            width: 600,
            showConfirmButton: false,
            showCloseButton: true
        });
    }

    // 2. Logic Tombol Hapus (SweetAlert)
    const tombolHapus = document.querySelectorAll('.btn-hapus');
    tombolHapus.forEach(btn => {
        btn.addEventListener('click', function() {
            const urlTujuan = this.getAttribute('data-href');
            
            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Foto dan data akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan;
                }
            });
        });
    });
</script>