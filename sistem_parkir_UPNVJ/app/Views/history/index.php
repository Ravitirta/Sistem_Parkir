<?php 
    $totalUang = 0;
    foreach ($data_history as $d) {
        $totalUang += $d['bayar'];
    }
?>

<!-- Header & Filter Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">History Transaksi</h2>
        <p class="text-secondary mb-0">Arsip data kendaraan keluar & pendapatan.</p>
    </div>
    
    <!-- Form Filter -->
    <form action="<?= base_url('dashboard/history') ?>" method="get" class="d-flex gap-2 flex-wrap">
        
        <!-- Filter Bulan -->
        <select name="bulan" class="form-select border-0 shadow-sm" style="width: 140px;">
            <?php 
                $listBulan = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                foreach ($listBulan as $key => $val) : 
            ?>
                <option value="<?= $key ?>" <?= ($key == $bulan_ini) ? 'selected' : '' ?>><?= $val ?></option>
            <?php endforeach; ?>
        </select>
        
        <!-- Filter Tahun -->
        <select name="tahun" class="form-select border-0 shadow-sm" style="width: 100px;">
            <?php 
                $thnSkrg = date('Y');
                for ($t = $thnSkrg; $t >= $thnSkrg - 5; $t--) : 
            ?>
                <option value="<?= $t ?>" <?= ($t == $tahun_ini) ? 'selected' : '' ?>><?= $t ?></option>
            <?php endfor; ?>
        </select>
        
        <button type="submit" class="btn btn-primary shadow-sm text-nowrap">
            <i class="bi bi-search me-1"></i> Tampilkan
        </button>
    </form>
</div>

<!-- Card Ringkasan Total -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
    <div class="card-body p-4 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">
                Total Pendapatan (<?= $listBulan[$bulan_ini] . ' ' . $tahun_ini ?>)
            </h6>
            <h2 class="fw-bold text-success mb-0">Rp <?= number_format($totalUang, 0, ',', '.') ?></h2>
        </div>
        <div class="bg-success-subtle text-success p-3 rounded-circle">
            <i class="bi bi-cash-stack fs-3"></i>
        </div>
    </div>
</div>

<!-- Card Tabel Data -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i>Data Arsip Transaksi</h6>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">No</th>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Tanggal Keluar</th>
                        
                        <!-- KOLOM BARU: AREA PARKIR -->
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Area Parkir</th>
                        
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Plat Nomor</th>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Jenis</th>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Petugas</th>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Jam Masuk</th>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold">Jam Keluar</th>
                        <th class="px-4 py-3 text-uppercase fs-7 fw-bold text-end">Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_history)): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-5 text-center text-muted">
                                <div class="mb-3"><i class="bi bi-clipboard-x fs-1 opacity-50"></i></div>
                                Tidak ada data history transaksi pada bulan ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($data_history as $row): ?>
                            <tr>
                                <td class="px-4 py-3 text-secondary text-center"><?= $no++ ?></td>
                                <td class="px-4 py-3 fw-medium text-nowrap">
                                    <?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?>
                                </td>

                                <!-- ISI KOLOM BARU: AREA PARKIR -->
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark border fw-normal">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        <?= esc($row['nama_area'] ?? '-') ?>
                                    </span>
                                </td>

                                <td class="px-4 py-3 fw-bold font-monospace bg-light rounded text-dark text-center">
                                    <?= esc($row['plat_nomor']) ?>
                                </td>
                                <td class="px-4 py-3"><?= esc($row['jenis_kendaraan']) ?></td>
                                <td class="px-4 py-3 text-muted"><?= esc($row['nama_petugas']) ?></td>
                                <td class="px-4 py-3 text-secondary font-monospace"><?= date('H:i', strtotime($row['waktu_masuk'])) ?></td>
                                <td class="px-4 py-3 text-secondary font-monospace"><?= date('H:i', strtotime($row['waktu_keluar'])) ?></td>
                                <td class="px-4 py-3 text-end fw-bold text-success">
                                    Rp <?= number_format($row['bayar'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
