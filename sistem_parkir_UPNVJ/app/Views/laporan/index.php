<?= $this->extend('layout/template'); ?> 

<?= $this->section('content'); ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Laporan Pendapatan & Transaksi</h1>

    <ul class="nav nav-tabs mb-3" id="laporanTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian" type="button" role="tab" aria-controls="harian" aria-selected="true">
                📄 Laporan Harian (Minggu Ini)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="bulanan-tab" data-bs-toggle="tab" data-bs-target="#bulanan" type="button" role="tab" aria-controls="bulanan" aria-selected="false">
                📊 Laporan Bulanan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <div class="tab-pane fade show active" id="harian" role="tabpanel" aria-labelledby="harian-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Transaksi (Senin - Minggu Ini)</h6>
                    <small class="text-muted">Data akan otomatis reset setiap hari Senin.</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Plat Nomor</th>
                                    <th>Area Parkir</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Bayar (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($laporan_harian)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data transaksi minggu ini.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no=1; foreach($laporan_harian as $harian) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= date('d-m-Y', strtotime($harian['tanggal_transaksi'])); ?></td>
                                        <td><span class="badge bg-secondary"><?= esc($harian['plat_nomor']); ?></span></td>
                                        <td><?= esc($harian['nama_area']); ?></td>
                                        <td><?= esc($harian['waktu_masuk']); ?></td>
                                        <td><?= esc($harian['waktu_keluar']); ?></td>
                                        <td class="fw-bold">Rp <?= number_format($harian['bayar'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="bulanan" role="tabpanel" aria-labelledby="bulanan-tab">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Pendapatan Bulan Ini (<?= date('F Y'); ?>)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp <?= number_format($total_bulanan, 0, ',', '.'); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">Rekap Pendapatan Per Area</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" width="100%" cellspacing="0">
                                    <thead class="table-success">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Area Parkir</th>
                                            <th>Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($laporan_bulanan)) : ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada rekap bulan ini.</td>
                                            </tr>
                                        <?php else : ?>
                                            <?php $no=1; foreach($laporan_bulanan as $bulanan) : ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= date('d-m-Y', strtotime($bulanan['tanggal_transaksi'])); ?></td>
                                                <td><?= esc($bulanan['nama_area']); ?></td>
                                                <td>Rp <?= number_format($bulanan['pendapatan_per_area'], 0, ',', '.'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mt-3" role="alert">
                                <i class="fas fa-info-circle"></i> Data bulan lalu akan otomatis dipindahkan ke menu <b>History</b> saat berganti bulan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->endSection(); ?>