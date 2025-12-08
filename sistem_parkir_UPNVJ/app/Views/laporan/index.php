<div class="container-fluid">

    <!-- BAGIAN 1: JUDUL HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 fw-bold m-0">
                <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Pendapatan
            </h1>
            <p class="text-muted small m-0">Rekapitulasi data transaksi parkir.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border shadow-sm py-2 px-3">
                <i class="bi bi-calendar-check text-success me-2"></i>
                <?= date('d F Y'); ?>
            </span>
        </div>
    </div>

    <!-- BAGIAN 2: NAVIGASI TAB -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="laporanTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2 shadow-sm me-2 rounded-top border-0" 
                    id="harian-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#harian" 
                    type="button" 
                    role="tab" 
                    aria-controls="harian" 
                    aria-selected="true"
                    style="border-top: 4px solid #0d6efd !important;">
                <i class="bi bi-journal-text me-2"></i>Laporan Harian
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 shadow-sm rounded-top border-0" 
                    id="bulanan-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#bulanan" 
                    type="button" 
                    role="tab" 
                    aria-controls="bulanan" 
                    aria-selected="false"
                    style="border-top: 4px solid #198754 !important;">
                <i class="bi bi-graph-up-arrow me-2"></i>Rekap Bulanan
            </button>
        </li>
    </ul>

    <!-- BAGIAN 3: ISI KONTEN TAB -->
    <div class="tab-content" id="myTabContent">
        
        <!-- ========================== -->
        <!-- KONTEN TAB HARIAN -->
        <!-- ========================== -->
        <div class="tab-pane fade show active" id="harian" role="tabpanel" aria-labelledby="harian-tab">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-list-check me-2"></i>Transaksi Selesai (Minggu Ini)
                    </h6>
                    <span class="badge bg-light text-secondary">Terbaru di atas</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="text-center py-3">No</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3">Plat Nomor</th>
                                    <th class="py-3">Area</th>
                                    <th class="text-center py-3">Masuk</th>
                                    <th class="text-center py-3">Keluar</th>
                                    <th class="text-end py-3 pe-4">Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($laporan_harian)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                            Belum ada data transaksi minggu ini.
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no=1; foreach($laporan_harian as $h) : ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary"><?= $no++; ?></td>
                                        <td><?= date('d/m/Y', strtotime($h['tanggal_transaksi'])); ?></td>
                                        <td>
                                            <span class="badge bg-dark font-monospace px-2 py-1">
                                                <?= esc($h['plat_nomor'] ?? '-'); ?>
                                            </span>
                                        </td>
                                        <td><?= esc($h['nama_area'] ?? 'Umum'); ?></td>
                                        <td class="text-center small"><?= esc($h['waktu_masuk']); ?></td>
                                        <td class="text-center small"><?= esc($h['waktu_keluar']); ?></td>
                                        <td class="text-end pe-4 fw-bold text-success">
                                            Rp <?= number_format($h['bayar'], 0, ',', '.'); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-muted small text-end py-2">
                    * Data harian akan direset otomatis setiap hari Senin.
                </div>
            </div>
        </div>

        <!-- ========================== -->
        <!-- KONTEN TAB BULANAN -->
        <!-- ========================== -->
        <div class="tab-pane fade" id="bulanan" role="tabpanel" aria-labelledby="bulanan-tab">
            
            <!-- Kartu Total Pendapatan -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm overflow-hidden text-white" 
                         style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 opacity-75 fw-bold">Total Pendapatan Bulan Ini</h6>
                                <div class="text-white-50 small mb-2"><?= date('F Y'); ?></div>
                                <h1 class="display-5 fw-bold mb-0">
                                    Rp <?= number_format($total_bulanan, 0, ',', '.'); ?>
                                </h1>
                            </div>
                            <div class="opacity-25">
                                <i class="bi bi-wallet2" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Rincian Per Area -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="bi bi-pie-chart-fill me-2"></i>Rincian Pendapatan Per Area
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-success">
                                <tr>
                                    <th class="text-center py-3" width="60">No</th>
                                    <th class="py-3">Nama Area Parkir</th>
                                    <th class="text-end py-3 pe-5">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($laporan_bulanan)) : ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="bi bi-exclamation-circle fs-2 d-block mb-2 text-warning"></i>
                                            Belum ada pendapatan bulan ini.
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no=1; foreach($laporan_bulanan as $bulanan) : ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <span class="fw-bold fs-5 text-dark">
                                                    <?= esc($bulanan['nama_area'] ?? 'Area Tidak Diketahui'); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-5">
                                            <span class="fw-bold fs-5 text-success">
                                                Rp <?= number_format($bulanan['pendapatan_per_area'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3 shadow-sm border-0">
                <i class="bi bi-info-circle-fill me-2"></i>
                Data bulan lalu akan otomatis dipindahkan ke menu <b>History</b> saat berganti bulan.
            </div>
        </div>

    </div> 
</div>
