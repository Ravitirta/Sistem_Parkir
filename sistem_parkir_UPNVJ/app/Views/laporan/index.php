<div class="container-fluid">

    <!-- JUDUL -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800 fw-bold m-0">
            <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Pendapatan
        </h1>
        <span class="badge bg-light text-dark border py-2 px-3">
            <i class="bi bi-calendar-check me-2"></i><?= date('d F Y'); ?>
        </span>
    </div>

    <!-- ============================================== -->
    <!-- FITUR FILTER PENCARIAN (PILIH PERIODE)         -->
    <!-- ============================================== -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-3">
            <form action="" method="get" class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold text-secondary"><i class="bi bi-funnel-fill me-1"></i> Filter Data:</span>
                </div>
                
                <!-- PILIH BULAN -->
                <div class="col-auto">
                    <select name="bulan" class="form-select form-select-sm border-secondary shadow-sm" style="min-width: 150px;">
                        <?php 
                            $bulan_list = [
                                '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                                '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                                '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                            ];
                            // Default ke bulan ini jika belum ada pilihan
                            $bulan_pilih = $bulan_ini ?? date('m');
                            
                            foreach($bulan_list as $key => $val): 
                        ?>
                            <option value="<?= $key; ?>" <?= ($key == $bulan_pilih) ? 'selected' : ''; ?>>
                                <?= $val; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- PILIH TAHUN -->
                <div class="col-auto">
                    <select name="tahun" class="form-select form-select-sm border-secondary shadow-sm" style="min-width: 100px;">
                        <?php 
                            $tahun_sekarang = date('Y');
                            $tahun_pilih = $tahun_ini ?? $tahun_sekarang;
                            
                            // Tampilkan 3 tahun ke belakang
                            for($i = $tahun_sekarang; $i >= $tahun_sekarang - 3; $i--): 
                        ?>
                            <option value="<?= $i; ?>" <?= ($i == $tahun_pilih) ? 'selected' : ''; ?>>
                                <?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- TOMBOL CARI -->
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                    <a href="/dashboard/laporan" class="btn btn-outline-secondary btn-sm shadow-sm" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB NAVIGASI -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="laporanTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold px-4 py-2 shadow-sm me-2 rounded-top border-0" 
                    id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian" type="button" 
                    style="border-top: 4px solid #0d6efd !important;">
                Laporan Harian
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold px-4 py-2 shadow-sm rounded-top border-0" 
                    id="bulanan-tab" data-bs-toggle="tab" data-bs-target="#bulanan" type="button" 
                    style="border-top: 4px solid #198754 !important;">
                Rekap Bulanan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <!-- TAB HARIAN (TIDAK TERPENGARUH FILTER - SELALU LIVE HARI INI) -->
        <div class="tab-pane fade show active" id="harian" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-list-task me-2"></i>Transaksi Selesai (Hari Ini)</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">Realtime Data</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center py-3">No</th>
                                    <th>Tanggal</th>
                                    <th>Plat Nomor</th>
                                    <th>Area</th>
                                    <th class="text-center">Masuk - Keluar</th>
                                    <th class="text-end pe-4">Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($laporan_harian)) : ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data transaksi selesai hari ini.</td></tr>
                                <?php else : ?>
                                    <?php $no=1; foreach($laporan_harian as $h) : ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= date('d/m/Y', strtotime($h['tanggal_transaksi'])); ?></td>
                                        <td><span class="badge bg-dark font-monospace"><?= esc($h['plat_nomor'] ?? '-'); ?></span></td>
                                        <td><?= esc($h['nama_area'] ?? 'Umum'); ?></td>
                                        <td class="text-center small">
                                            <?= esc($h['waktu_masuk']); ?> <i class="bi bi-arrow-right mx-1 text-muted"></i> <?= esc($h['waktu_keluar']); ?>
                                        </td>
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
            </div>
        </div>

        <!-- TAB BULANAN (YANG DIPENGARUHI FILTER) -->
        <div class="tab-pane fade" id="bulanan" role="tabpanel">
            
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-3">
                <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                <div>
                    Menampilkan data rekap untuk periode: 
                    <strong><?= $bulan_list[$bulan_pilih]; ?> <?= $tahun_pilih; ?></strong>
                </div>
            </div>

            <!-- KARTU TOTAL PENDAPATAN (HITUNGAN DARI TABEL BAWAH) -->
            <?php 
                $total_filter = 0;
                foreach($laporan_bulanan as $row) {
                    $total_filter += $row['pendapatan_per_area'];
                }
            ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(45deg, #198754, #20c997);">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 opacity-75 fw-bold">Total Pendapatan (Filter Ini)</h6>
                                <h2 class="display-6 fw-bold mb-0">Rp <?= number_format($total_filter, 0, ',', '.'); ?></h2>
                            </div>
                            <i class="bi bi-wallet2" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-success"><i class="bi bi-pie-chart-fill me-2"></i>Rincian Pendapatan Per Area</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-success">
                                <tr>
                                    <th class="text-center py-3" width="60">No</th>
                                    <th>Periode (Tahun-Bulan)</th>
                                    <th>Area Parkir</th>
                                    <th class="text-end pe-5">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($laporan_bulanan)) : ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-search fs-1 d-block mb-2 text-warning"></i>
                                            Tidak ada data laporan untuk periode <b><?= $bulan_list[$bulan_pilih]; ?> <?= $tahun_pilih; ?></b>.
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no=1; foreach($laporan_bulanan as $bulanan) : ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $no++; ?></td>
                                        <td><span class="badge bg-secondary font-monospace"><?= esc($bulanan['tahun_bulan']); ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <span class="fw-bold fs-5">
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
        </div>

    </div> 
</div>