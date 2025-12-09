<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 fw-bold m-0">
                <i class="bi bi-box-arrow-left me-2 text-primary"></i>Kendaraan Keluar & Pembayaran
            </h1>
            <p class="text-muted small m-0">Kelola proses checkout kendaraan parkir.</p>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    <?php if(session()->getFlashdata('berhasil')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('berhasil'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('gagal')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('gagal'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FILTER PENCARIAN -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="<?= base_url('dashboard/update') ?>" method="get" class="row g-3 align-items-end">
                
                <!-- PILIH AREA -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small">Filter Area Parkir</label>
                    <select name="area" class="form-select">
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
                        <input type="text" name="plat_nomor" class="form-control" 
                               placeholder="Masukkan Plat Nomor..." 
                               value="<?= esc($request->getVar('plat_nomor') ?? '') ?>">
                    </div>
                </div>

                <!-- TOMBOL -->
                <div class="col-md-2">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Cari Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list-task me-2"></i>Daftar Kendaraan Parkir</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <!-- KOLOM BARU: AREA PARKIR -->
                            <th class="ps-4">Area Parkir</th>
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
                                    Tidak ada kendaraan yang sesuai filter.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transaksiMasuk as $transaksi): ?>
                                <?php
                                    // Cek apakah sudah dihitung
                                    $hitungData = session()->get('hitung_bayar_' . $transaksi['id_transaksi']); 
                                    $isCalculated = !empty($hitungData) && (session()->getFlashdata('perhitungan_berhasil') === $transaksi['id_transaksi'] || !empty($hitungData));
                                ?>
                                <tr class="<?= $isCalculated ? 'table-warning' : '' ?>">
                                    
                                    <!-- ISI KOLOM BARU: AREA -->
                                    <td class="ps-4 fw-bold text-primary">
                                        <?= esc($transaksi['nama_area']) ?>
                                    </td>

                                    <td><?= esc($transaksi['jenis_kendaraan']) ?></td>
                                    
                                    <td>
                                        <span class="badge bg-dark font-monospace text-uppercase px-2 py-1">
                                            <?= esc($transaksi['plat_nomor']) ?>
                                        </span>
                                    </td>
                                    
                                    <td class="small"><?= esc($transaksi['waktu_masuk']) ?></td>
                                    
                                    <td class="small">
                                        <?php if($isCalculated): ?>
                                            <span class="badge bg-warning text-dark">Out: <?= $hitungData['waktu_keluar_real'] ?></span><br>
                                            Durasi: <?= $hitungData['durasi'] ?> Jam
                                        <?php else: ?>
                                            <span class="text-muted">Sedang Parkir...</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="fw-bold text-success">
                                        <?php if($isCalculated): ?>
                                            Rp <?= number_format($hitungData['total_bayar'], 0, ',', '.') ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-end pe-4">
                                        <?php if(!$isCalculated): ?>
                                            <!-- Tombol Hitung -->
                                            <form action="<?= base_url('dashboard/update/calculate/' . $transaksi['id_transaksi']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <!-- Kirim ID Area juga agar saat refresh tetap di filter area tersebut -->
                                                <input type="hidden" name="area_redirect" value="<?= $selected_area ?>">
                                                <button type="submit" class="btn btn-sm btn-info text-white shadow-sm">
                                                    <i class="bi bi-calculator me-1"></i>Hitung
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Tombol Bayar/Selesai -->
                                            <form action="<?= base_url('dashboard/update/checkout/' . $transaksi['id_transaksi']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success shadow-sm" onclick="return confirm('Konfirmasi pembayaran diterima?')">
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
