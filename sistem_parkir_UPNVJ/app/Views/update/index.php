<div class="container-fluid">
    
    <!-- JUDUL HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 fw-bold m-0">
                <i class="bi bi-box-arrow-left me-2 text-primary"></i>Kendaraan Keluar & Pembayaran
            </h1>
            <p class="text-muted small m-0">Kelola proses checkout dan pembayaran parkir.</p>
        </div>
    </div>

    <!-- NOTIFIKASI SYSTEM (BERHASIL/GAGAL) -->
    <?php if(session()->getFlashdata('berhasil')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Sukses!',
                    text: '<?= session()->getFlashdata('berhasil'); ?>',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    <?php endif; ?>

    <?php if(session()->getFlashdata('gagal')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '<?= session()->getFlashdata('gagal'); ?>',
                });
            });
        </script>
    <?php endif; ?>

    <!-- FILTER PENCARIAN DENGAN VALIDASIJS -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <!-- ID 'formCari' untuk ditangkap JS -->
            <form action="<?= base_url('dashboard/update') ?>" method="get" class="row g-3 align-items-end" id="formCari">
                
                <!-- PILIH AREA -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small">Filter Area Parkir</label>
                    <select name="area" id="inputArea" class="form-select">
                        <option value="">-- Semua Area --</option>
                        <?php foreach($areas as $a): ?>
                            <option value="<?= $a['id_area']; ?>" <?= ($a['id_area'] == $selected_area) ? 'selected' : ''; ?>>
                                <?= esc($a['nama_area']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- CARI PLAT NOMOR -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Cari Plat Nomor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <!-- HAPUS 'required'& validasi pindah ke JS -->
                        <input type="text" name="plat_nomor" id="inputPlat" class="form-control" 
                               placeholder="Masukkan Plat Nomor..." 
                               value="<?= esc($request->getVar('plat_nomor') ?? '') ?>">
                    </div>
                </div>

                <!-- TOMBOL CARI -->
                <div class="col-md-2">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-search me-1"></i> Cari Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list-task me-2"></i>Daftar Kendaraan Parkir</h6>
            <a href="<?= base_url('dashboard/update') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i>Reset / Refresh
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <!-- KOLOM AREA PARKIR -->
                            <th class="ps-4 py-3">Area Parkir</th>
                            <th>Jenis Kendaraan</th>
                            <th>Plat Nomor</th>
                            <th>Waktu Masuk</th>
                            <th>Status / Durasi</th>
                            <th>Harga Bayar</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transaksiMasuk)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    Tidak ada kendaraan yang sesuai filter pencarian.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transaksiMasuk as $transaksi): ?>
                                <?php
                                    // Untuk mengecek apakah data ini sudah dihitung biayanya (ada di session)
                                    $hitungData = session()->get('hitung_bayar_' . $transaksi['id_transaksi']); 
                                    $isCalculated = !empty($hitungData) && (session()->getFlashdata('perhitungan_berhasil') === $transaksi['id_transaksi'] || !empty($hitungData));
                                ?>
                                <tr class="<?= $isCalculated ? 'table-warning' : '' ?>">
                                    
                                    <!-- 1. Area Parkir -->
                                    <td class="ps-4 fw-bold text-primary">
                                        <i class="bi bi-geo-alt-fill me-1 small"></i><?= esc($transaksi['nama_area']) ?>
                                    </td>

                                    <!-- 2. Jenis -->
                                    <td><?= esc($transaksi['jenis_kendaraan']) ?></td>
                                    
                                    <!-- 3. Plat Nomor -->
                                    <td>
                                        <span class="badge bg-dark font-monospace text-uppercase px-2 py-1">
                                            <?= esc($transaksi['plat_nomor']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- 4. Waktu Masuk -->
                                    <td class="small font-monospace"><?= esc($transaksi['waktu_masuk']) ?></td>
                                    
                                    <!-- 5. Status / Durasi -->
                                    <td class="small">
                                        <?php if($isCalculated): ?>
                                            <span class="badge bg-warning text-dark mb-1">
                                                <i class="bi bi-clock-history me-1"></i>Out: <?= $hitungData['waktu_keluar_real'] ?>
                                            </span><br>
                                            <strong>Durasi: <?= $hitungData['durasi'] ?> Jam</strong>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Sedang Parkir...</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- 6. Harga Bayar -->
                                    <td>
                                        <?php if($isCalculated): ?>
                                            <span class="fw-bold text-success fs-5">
                                                Rp <?= number_format($hitungData['total_bayar'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- 7. Tombol Aksi -->
                                    <td class="text-end pe-4">
                                        <?php if(!$isCalculated): ?>
                                            <!-- Tombol HITUNG -->
                                            <form action="<?= base_url('dashboard/update/calculate/' . $transaksi['id_transaksi']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="area_redirect" value="<?= $selected_area ?>">
                                                <button type="submit" class="btn btn-sm btn-info text-white shadow-sm px-3">
                                                    <i class="bi bi-calculator me-1"></i>Hitung
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Tombol BAYAR dengan validasi  JS -->
                                            <form action="<?= base_url('dashboard/update/checkout/' . $transaksi['id_transaksi']) ?>" method="post" class="d-inline" id="formCheckout<?= $transaksi['id_transaksi'] ?>">
                                                <?= csrf_field() ?>
                                                <!-- pakai type="button" agar tidak submit otomatis, handle pakai JS -->
                                                <button type="button" class="btn btn-sm btn-success shadow-sm px-3 btn-bayar" 
                                                        data-id="<?= $transaksi['id_transaksi'] ?>" 
                                                        data-plat="<?= esc($transaksi['plat_nomor']) ?>" 
                                                        data-bayar="<?= number_format($hitungData['total_bayar'],0,',','.') ?>"
                                                        data-durasi="<?= $hitungData['durasi'] ?>">
                                                    <i class="bi bi-cash-coin me-1"></i>Bayar & Selesai
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- VALIDASI JAVASCRIPT (SWEETALERT2) -->
<script>
    // 1. VALIDASI FORM PENCARIAN
    document.getElementById('formCari').addEventListener('submit', function(e) {
        const plat = document.getElementById('inputPlat').value.trim();
        const area = document.getElementById('inputArea').value;


        if (plat === '' && area === '') {
            e.preventDefault(); // Mencegah form terkirim
            Swal.fire({
                icon: 'info',
                title: 'Filter Kosong',
                text: 'Menampilkan semua data karena tidak ada filter yang dipilih.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "<?= base_url('dashboard/update') ?>";
            });
        }
    });

    // 2. VALIDASI TOMBOL BAYAR (KONFIRMASI)
    // Menangkap semua tombol dengan class 'btn-bayar'
    const btnBayar = document.querySelectorAll('.btn-bayar');
    
    btnBayar.forEach(btn => {
        btn.addEventListener('click', function() {
            // Ambil data dari atribut data-*
            const id = this.getAttribute('data-id');
            const plat = this.getAttribute('data-plat');
            const bayar = this.getAttribute('data-bayar');
            const durasi = this.getAttribute('data-durasi');

            // Konfirmasi dengan SweetAlert 
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="text-start bg-light p-3 rounded border">
                        <p class="mb-1">Kendaraan: <strong>${plat}</strong></p>
                        <p class="mb-1">Durasi Parkir: <strong>${durasi} Jam</strong></p>
                        <hr class="my-2">
                        <h4 class="text-success text-center fw-bold">Total: Rp ${bayar}</h4>
                    </div>
                    <p class="mt-3 text-muted small">Pastikan uang sudah diterima.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754', // Hijau
                cancelButtonColor: '#6c757d',  // Abu
                confirmButtonText: 'Ya, Terima & Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika Yes, submit
                    document.getElementById('formCheckout' + id).submit();
                }
            });
        });
    });
</script>
