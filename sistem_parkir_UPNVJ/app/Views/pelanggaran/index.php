<div class="container-fluid">
    
    <!-- HEADER & JUDUL -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800 m-0 fw-bold">
            <i class="bi bi-images text-primary me-2"></i>Galeri Pelanggaran
        </h2>
        
        <div class="d-flex gap-2">
            <!-- Tombol Lapor (Untuk Umum) -->
            <a href="/pelanggaran/lapor" class="btn btn-primary shadow-sm">
                <i class="bi bi-camera-fill me-2"></i>Lapor Baru
            </a>

            <!-- Tombol Kelola (Khusus Petugas) -->
            <?php if(session()->get('logged_in')): ?>
                <a href="/pelanggaran/manage" class="btn btn-warning text-dark shadow-sm fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Verifikasi Masuk
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- NOTIFIKASI FLASH MESSAGE -->
    <?php if(session()->getFlashdata('sukses')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '<?= session()->getFlashdata('sukses'); ?>',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    <?php endif; ?>

    <!-- GRID GALERI PELANGGARAN -->
    <div class="row g-4">
        
        <?php if(empty($data_valid)): ?>
            <!-- Tampilan Jika Data Kosong -->
            <div class="col-12 text-center py-5">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Area Parkir Tertib!</h4>
                        <p class="text-muted">Belum ada laporan pelanggaran yang terverifikasi saat ini.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            
            <!-- Loop Data -->
            <?php foreach($data_valid as $row): ?>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0 overflow-hidden card-hover">
                    
                    <!-- UPLOAD FOTO -->
                    <div class="position-relative">
                        <img src="/uploads/<?= $row['foto']; ?>" 
                             class="card-img-top" 
                             alt="Pelanggaran" 
                             style="height: 220px; object-fit: cover; cursor: pointer;"
                             onclick="lihatFoto('/uploads/<?= $row['foto']; ?>', '<?= esc($row['nama_area']); ?>')">
                        
                        <!-- Badge Area (Pojok Kiri Bawah Foto) -->
                        <div class="position-absolute bottom-0 start-0 w-100 p-2" 
                             style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <span class="badge bg-danger">
                                <i class="bi bi-geo-alt-fill me-1"></i><?= $row['nama_area']; ?>
                            </span>
                        </div>

                        <!-- Tombol Hapus (Pojok Kanan Atas - Khusus Petugas) -->
                        <?php if(session()->get('logged_in')): ?>
                            <button class="btn btn-light btn-sm text-danger position-absolute top-0 end-0 m-2 shadow-sm rounded-circle btn-hapus"
                                    data-href="/pelanggaran/hapus/<?= $row['id_pelanggaran']; ?>"
                                    data-bs-toggle="tooltip" title="Hapus Permanen">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- BAGIAN KETERANGAN -->
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 text-muted small">
                            <i class="bi bi-calendar-event me-2"></i>
                            <span><?= date('d F Y', strtotime($row['tanggal'])); ?></span>
                        </div>
                        
                        <p class="card-text fw-medium text-dark mb-0" style="font-size: 0.95rem;">
                            "<?= esc($row['keterangan']); ?>"
                        </p>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<!-- SCRIPT JS TAMBAHAN -->
<script>
    // 1. Fitur Zoom Foto (SweetAlert)
    function lihatFoto(url, area) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Bukti Pelanggaran',
            title: 'Pelanggaran di ' + area,
            width: 600,
            padding: '1em',
            showConfirmButton: false,
            showCloseButton: true,
            background: '#fff',
            backdrop: `rgba(0,0,0,0.8)`
        });
    }

    // 2. Fitur Hapus dengan Konfirmasi (SweetAlert)
    const tombolHapus = document.querySelectorAll('.btn-hapus');
    tombolHapus.forEach(btn => {
        btn.addEventListener('click', function() {
            const urlTujuan = this.getAttribute('data-href');
            
            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Foto dan data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
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

<style>
    /* Efek Hover pada Card agar interaktif */
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
