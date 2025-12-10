<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800 fw-bold m-0">
            <i class="bi bi-clock-history me-2 text-primary"></i>History Transaksi (>30 Hari)
        </h1>
        <span class="badge bg-light text-dark border py-2 px-3">
            <i class="bi bi-calendar-check me-2"></i><?= date('d F Y'); ?>
        </span>
    </div>

    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-3">
           <form action="<?= base_url('dashboard/history') ?>" method="get" class="d-flex gap-2">
                <div class="col-auto">
                    <span class="fw-bold text-secondary"><i class="bi bi-funnel-fill me-1"></i> Filter Data:</span>
                </div>
                
                <div class="col-auto">
                    <select name="bulan" class="form-select form-select-sm border-secondary shadow-sm" style="min-width: 150px;">
                        <?php 
                            $bulan_list = [
                                '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                                '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                                '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                            ];
                            // Mengambil variabel $bulan_ini dari Controller
                            $pilih_bulan = $bulan_ini ?? date('m');
                            
                            foreach($bulan_list as $key => $val): 
                        ?>
                            <option value="<?= $key; ?>" <?= ($key == $pilih_bulan) ? 'selected' : ''; ?>>
                                <?= $val; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="tahun" class="form-select form-select-sm border-secondary shadow-sm" style="min-width: 100px;">
                        <?php 
                            $tahun_skrg = date('Y');
                            $pilih_tahun = $tahun_ini ?? $tahun_skrg;
                            
                            // Loop: Tahun sekarang sampai 1 tahun ke belakang saja
                            // Karena data > 1 tahun dihapus oleh database event scheduler
                            for($i = $tahun_skrg; $i >= $tahun_skrg - 1; $i--): 
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
                    <a href="<?= base_url('dashboard/history') ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-table me-2"></i>Data Arsip Transaksi</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center py-3">No</th>
                            <th>Tanggal Keluar</th>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Petugas</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Keluar</th>
                            <th class="text-end pe-4">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data_history)) : ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data history untuk periode ini.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no=1; foreach($data_history as $row) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= date('d/m/Y', strtotime($row['waktu_keluar'])); ?></td>
                                
                                <td><span class="badge bg-dark font-monospace"><?= esc($row['plat_nomor'] ?? '-'); ?></span></td>
                                <td><?= esc($row['jenis_kendaraan'] ?? '-'); ?></td>
                                <td><?= esc($row['nama_petugas'] ?? '-'); ?></td>
                                
                                <td class="text-center small"><?= date('H:i', strtotime($row['waktu_masuk'])); ?></td>
                                <td class="text-center small"><?= date('H:i', strtotime($row['waktu_keluar'])); ?></td>
                                
                                <td class="text-end pe-4 fw-bold text-success">
                                    Rp <?= number_format($row['bayar'] ?? 0, 0, ',', '.'); ?>
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
