<h2 class="mb-4">Monitor Status Area Parkir</h2>

<div class="card shadow-sm border-0">
    <div class="card-body">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title text-muted">Realtime Updates</h5>
            <a href="" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i> Refresh Data
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nama Area</th>
                        <th scope="col" class="text-center">Jam Update</th>
                        <th scope="col">Kapasitas (Terisi / Max)</th>
                        <th scope="col">Visualisasi</th>
                        <th scope="col" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($statusParkir)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data status area.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($statusParkir as $row): ?>
                            <?php 
                                // Hitung Persentase untuk Progress Bar
                                if($row['kapasitas_max'] > 0){
                                    $persen = ($row['kapasitas_now'] / $row['kapasitas_max']) * 100;
                                } else {
                                    $persen = 0;
                                }

                                // Tentukan Warna Progress Bar
                                if($persen >= 90) { $warna = 'bg-danger'; }      // Merah (Kritis)
                                elseif($persen >= 70) { $warna = 'bg-warning'; } // Kuning (Hampir Penuh)
                                else { $warna = 'bg-success'; }                  // Hijau (Aman)
                                
                                // Tentukan Badge Status
                                $badgeColor = ($row['status_area'] == 'Penuh') ? 'bg-danger' : 'bg-success';
                            ?>
                            <tr>
                                <td class="fw-bold"><?= esc($row['nama_area']); ?></td>
                                
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($row['jam']); ?> WIB
                                    </span>
                                </td>

                                <td>
                                    <span class="fw-bold"><?= esc($row['kapasitas_now']); ?></span> 
                                    <span class="text-muted small">/ <?= esc($row['kapasitas_max']); ?></span>
                                </td>

                                <td style="width: 30%;">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar <?= $warna ?>" 
                                             role="progressbar" 
                                             style="width: <?= $persen ?>%;" 
                                             aria-valuenow="<?= $persen ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?= number_format($persen, 1) ?>% Terisi
                                    </small>
                                </td>

                                <td class="text-center">
                                    <span class="badge <?= $badgeColor ?> rounded-pill px-3">
                                        <?= esc($row['status_area']); ?>
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