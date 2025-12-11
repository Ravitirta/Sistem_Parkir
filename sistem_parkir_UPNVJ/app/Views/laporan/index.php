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

    <!-- BAGIAN 2: FITUR FILTER (PENCARIAN) -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-3">
            <form action="" method="get" class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold text-secondary"><i class="bi bi-funnel-fill me-1"></i> Filter Data:</span>
                </div>

                <!-- PILIH AREA -->
                <div class="col-auto">
                    <select name="area" class="form-select form-select-sm border-secondary shadow-sm" style="min-width: 150px;">
                        <option value="">-- Semua Area --</option>
                        <?php if(!empty($list_area)): ?>
                            <?php foreach($list_area as $a): ?>
                                <option value="<?= $a['id_area']; ?>" <?= ($a['id_area'] == ($area_ini ?? '')) ? 'selected' : ''; ?>>
                                    <?= esc($a['nama_area']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- PILIH BULAN -->
                <div class="col-auto">
                    <select name="bulan" class="form-select form-select-sm border-secondary shadow-sm">
                        <?php 
                            $bulan_list = [
                                '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                                '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                                '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                            ];
                            // Ambil dari variabel controller, kalau tidak ada pakai bulan ini
                            $pilih_bulan = $bulan_ini ?? date('m');
                            
                            foreach($bulan_list as $key => $val): 
                        ?>
                            <option value="<?= $key; ?>" <?= ($key == $pilih_bulan) ? 'selected' : ''; ?>>
                                <?= $val; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- PILIH TAHUN -->
                <div class="col-auto">
                    <select name="tahun" class="form-select form-select-sm border-secondary shadow-sm">
                        <?php 
                            $tahun_skrg = date('Y');
                            $pilih_tahun = $tahun_ini ?? $tahun_skrg;
                            
                            // Tampilkan 3 tahun ke belakang
                            for($i = $tahun_skrg; $i >= $tahun_skrg - 3; $i--): 
                        ?>
                            <option value="<?= $i; ?>" <?= ($i == $pilih_tahun) ? 'selected' : ''; ?>>
                                <?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- BAGIAN 3: NAVIGASI TAB -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="laporanTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2 shadow-sm me-2 rounded-top border-0" 
                    id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian" type="button" 
                    style="border-top: 4px solid #0d6efd !important;">
                <i class="bi bi-journal-text me-2"></i>Laporan Harian
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 shadow-sm rounded-top border-0" 
                    id="bulanan-tab" data-bs-toggle="tab" data-bs-target="#bulanan" type="button" 
                    style="border-top: 4px solid #198754 !important;">
                <i class="bi bi-graph-up-arrow me-2"></i>Rekap Bulanan
            </button>
        </li>
    </ul>

    <!-- BAGIAN 4: ISI KONTEN -->
    <div class="tab-content" id="myTabContent">
        
        <!-- ============== -->
        <!-- TAB HARIAN     -->
        <!-- ============== -->
        <div class="tab-pane fade show active" id="harian" role="tabpanel">
            
            <!-- REKAP PENDAPATAN HARI INI PER AREA -->
            <div class="row mb-3">
                <?php $total_hari_ini = 0; ?>
                <?php if(!empty($rekap_harian)): ?>
                    <?php foreach($rekap_harian as $rh): 
                        $total_hari_ini += $rh['total_harian']; 
                    ?>
                    <div class="col-md-3 mb-2">
                        <div class="card bg-primary bg-opacity-10 border-primary shadow-sm h-100">
                            <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary fw-bold text-uppercase"><?= esc($rh['nama_area']); ?></small>
                                    <div class="fw-bold text-dark">Rp <?= number_format($rh['total_harian'], 0, ',', '.'); ?></div>
                                </div>
                                <i class="bi bi-cash text-primary fs-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Total Keseluruhan Hari Ini -->
                    <div class="col-md-3 mb-2">
                        <div class="card bg-success text-white shadow-sm border-0 h-100">
                            <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="opacity-75 text-uppercase">TOTAL HARI INI</small>
                                    <div class="fw-bold">Rp <?= number_format($total_hari_ini, 0, ',', '.'); ?></div>
                                </div>
                                <i class="bi bi-wallet2 fs-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning py-2 small shadow-sm border-0">
                            <i class="bi bi-info-circle-fill me-2"></i> Belum ada pendapatan masuk hari ini.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-list-check me-2"></i>Transaksi Selesai (Hari Ini)
                    </h6>
                    <span class="badge bg-light text-secondary">Realtime</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="bg-light text-dark">
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
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                            Belum ada data transaksi selesai hari ini.
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

        <!-- =============== -->
        <!-- TAB BULANAN     -->
        <!-- =============== -->
        <div class="tab-pane fade" id="bulanan" role="tabpanel">
            
            <!-- Info Filter -->
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-3">
                <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                <div>
                    Menampilkan data rekap untuk periode: 
                    <strong><?= $bulan_list[$pilih_bulan]; ?> <?= $pilih_tahun; ?></strong>
                </div>
            </div>

            <!-- Kartu Total -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden text-white" 
                         style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 opacity-75 fw-bold">Total Pendapatan (Periode Ini)</h6>
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

            <!-- Tabel Detail -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="bi bi-pie-chart-fill me-2"></i>Rincian Pendapatan Per Area
                    </h6>
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
                                            Tidak ada data laporan untuk periode ini.
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no=1; foreach($laporan_bulanan as $bulanan) : ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                        
                                        <!-- BAGIAN INI YANG SUDAH DIPERBAIKI (TANGGAL) -->
                                        <td>
                                            <span class="badge bg-secondary font-monospace">
                                                <?= date('Y-m', strtotime($bulanan['tanggal_transaksi'])); ?>
                                            </span>
                                        </td>
                                        
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
        </div>

    </div> 
</div>
