<h2 class="mb-3">Verifikasi Laporan Masuk</h2>
<a href="/pelanggaran" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
</a>

<div class="card shadow-sm p-3 border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Foto Bukti</th>
                    <th>Detail Kejadian</th>
                    <th>Keterangan Pelapor</th>
                    <th class="text-center" width="200">Aksi Validasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data_pending)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                            Tidak ada laporan baru yang perlu diverifikasi.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data_pending as $row): ?>
                    <tr>
                        <!-- Kolom Foto (Klik untuk Zoom) -->
                        <td width="150" class="text-center">
                            <img src="/uploads/<?= $row['foto']; ?>" 
                                 class="rounded shadow-sm" 
                                 style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;"
                                 onclick="lihatFotoBesar('/uploads/<?= $row['foto']; ?>')">
                            <div class="small text-muted mt-1">Klik untuk zoom</div>
                        </td>

                        <!-- Kolom Detail -->
                        <td>
                            <span class="badge bg-primary mb-1"><?= $row['nama_area']; ?></span><br>
                            <small class="text-muted"><i class="bi bi-calendar-event"></i> Tanggal:</small><br>
                            <strong><?= date('d F Y', strtotime($row['tanggal'])); ?></strong>
                        </td>

                        <!-- Kolom Keterangan -->
                        <td>
                            <div class="p-2 bg-light rounded border">
                                "<?= esc($row['keterangan']); ?>"
                            </div>
                        </td>

                        <!-- Kolom Aksi (Tombol) -->
                        <td class="text-center">
                            <div class="d-grid gap-2">
                                
                                <!-- TOMBOL TERIMA (VALID) -->
                                <!-- Perhatikan: href saya matikan jadi javascript:void(0) agar tidak pindah halaman langsung -->
                                <!-- Link aslinya saya simpan di data-href -->
                                <a href="javascript:void(0)" 
                                   data-href="/pelanggaran/verifikasi/<?= $row['id_pelanggaran']; ?>/valid" 
                                   class="btn btn-success btn-sm btn-terima">
                                   <i class="bi bi-check-lg"></i> Terima (Valid)
                                </a>

                                <!-- TOMBOL TOLAK (INVALID) -->
                                <a href="javascript:void(0)" 
                                   data-href="/pelanggaran/verifikasi/<?= $row['id_pelanggaran']; ?>/invalid" 
                                   class="btn btn-danger btn-sm btn-tolak">
                                   <i class="bi bi-x-lg"></i> Tolak (Invalid)
                                </a>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SCRIPT JAVASCRIPT KHUSUS VALIDASI -->
<script>
    // 1. LOGIKA TOMBOL TERIMA (POP UP HIJAU)
    // Kita cari semua tombol yang punya class 'btn-terima'
    const tombolTerima = document.querySelectorAll('.btn-terima');
    
    tombolTerima.forEach(btn => {
        btn.addEventListener('click', function() {
            // Ambil link tujuan dari atribut data-href
            const urlTujuan = this.getAttribute('data-href');
            
            // Tampilkan SweetAlert
            Swal.fire({
                title: 'Konfirmasi Validasi',
                text: "Apakah Anda yakin foto ini BENAR pelanggaran?",
                icon: 'question', // Ikon tanda tanya
                showCancelButton: true,
                confirmButtonColor: '#198754', // Warna Hijau
                cancelButtonColor: '#6c757d',  // Warna Abu
                confirmButtonText: 'Ya, Terima Laporan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                // Jika user klik tombol Ya
                if (result.isConfirmed) {
                    window.location.href = urlTujuan; // Pindah halaman
                }
            });
        });
    });

    // 2. LOGIKA TOMBOL TOLAK (POP UP MERAH)
    const tombolTolak = document.querySelectorAll('.btn-tolak');
    
    tombolTolak.forEach(btn => {
        btn.addEventListener('click', function() {
            const urlTujuan = this.getAttribute('data-href');
            
            Swal.fire({
                title: 'Tolak Laporan?',
                text: "Laporan ini akan dianggap TIDAK VALID dan dihapus dari antrian.",
                icon: 'warning', // Ikon peringatan seru
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Warna Merah
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak Laporan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan;
                }
            });
        });
    });

    // 3. FITUR ZOOM FOTO
    function lihatFotoBesar(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Bukti Pelanggaran',
            width: 600,
            padding: '1em',
            showConfirmButton: false, // Hilangkan tombol OK biar fokus ke foto
            showCloseButton: true     // Tambah tombol silang di pojok
        });
    }
</script>