<h2 class="text-2xl font-semibold mb-6 text-gray-800">Update Kendaraan Keluar & Pembayaran</h2>

<!-- Kolom Pencarian Kendaraan -->
<div class="mb-4">
    <!-- Form ini mengarahkan ke Controller Update::index() dengan metode GET untuk filter -->
    <form action="<?= base_url('dashboard/update') ?>" method="get" class="d-flex">
        <input type="text" name="plat_nomor" placeholder="Cari Plat Nomor..." 
               class="form-control me-2" value="<?= esc($this->request->getVar('plat_nomor') ?? '') ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="<?= base_url('dashboard/update') ?>" class="btn btn-secondary ms-2">Reset</a>
    </form>
</div>

<!-- Daftar List Kendaraan yang Masih Parkir -->
<div class="overflow-x-auto bg-white shadow-lg rounded-lg">
    <table class="table table-hover">
        <thead class="bg-light">
            <tr>
                <th scope="col">Jenis Kendaraan</th>
                <th scope="col">Plat Nomor</th>
                <th scope="col">Waktu Masuk</th>
                <th scope="col">Waktu Keluar (Realtime)</th>
                <th scope="col">Harga Bayar</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transaksiMasuk)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Tidak ada kendaraan yang sedang parkir atau tidak ditemukan.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($transaksiMasuk as $transaksi): ?>
                    <?php
                        // Ambil data perhitungan dari flashdata
                        $hitungData = session()->getFlashdata('hitung_bayar_' . $transaksi['id_transaksi']);
                        $isCalculated = !empty($hitungData);
                    ?>
                    <tr>
                        <td><?= esc($transaksi['jenis_kendaraan']) ?></td>
                        <td><?= esc($transaksi['plat_nomor']) ?></td>
                        <td><?= esc(date('H:i:s', strtotime($transaksi['waktu_masuk']))) ?></td>
                        
                        <td class="<?= $isCalculated ? 'text-primary fw-bold' : '' ?>">
                            <?= $isCalculated ? esc($hitungData['waktu_keluar_real']) : '---' ?>
                        </td>

                        <td class="<?= $isCalculated ? 'text-success fw-bold' : 'text-muted' ?>">
                            <?= $isCalculated ? 'Rp ' . number_format($hitungData['total_bayar'], 0, ',', '.') : 'Belum dihitung' ?>
                        </td>

                        <td>
                            <?php if (!$isCalculated): ?>
                                <!-- Tombol Hitung (OUT) -->
                                <form action="<?= base_url('dashboard/update/calculate/' . $transaksi['id_transaksi']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-info text-white">
                                        Hitung Bayar (OUT)
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Tombol Update (Selesai Checkout) -->
                                <form action="<?= base_url('dashboard/update/checkout/' . $transaksi['id_transaksi']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        Update (Selesai)
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