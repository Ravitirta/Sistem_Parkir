<h2 class="mb-3">Verifikasi Laporan Masuk</h2>
<a href="/pelanggaran" class="btn btn-outline-secondary mb-3">&laquo; Kembali</a>

<div class="card shadow-sm p-3">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Foto</th>
                <th>Tanggal & Area</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($data_pending)): ?>
                <tr><td colspan="4" class="text-center">Tidak ada laporan baru.</td></tr>
            <?php else: ?>
                <?php foreach($data_pending as $row): ?>
                <tr>
                    <td width="150">
                        <img src="/uploads/<?= $row['foto']; ?>" width="100%" class="rounded">
                    </td>
                    <td>
                        <b><?= $row['tanggal']; ?></b><br>
                        Area: <?= $row['nama_area']; ?>
                    </td>
                    <td><?= $row['keterangan']; ?></td>
                    <td width="150">
                        <!-- Tombol Ceklis (Valid) -->
                        <a href="/pelanggaran/verifikasi/<?= $row['id_pelanggaran']; ?>/valid" 
                           class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi Pelanggaran Valid?')">
                           <i class="bi bi-check-lg"></i> Terima
                        </a>

                        <!-- Tombol Silang (Invalid) -->
                        <a href="/pelanggaran/verifikasi/<?= $row['id_pelanggaran']; ?>/invalid" 
                           class="btn btn-danger btn-sm" onclick="return confirm('Tolak Laporan ini?')">
                           <i class="bi bi-x-lg"></i> Tolak
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>