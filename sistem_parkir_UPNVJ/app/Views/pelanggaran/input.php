<h2 class="mb-3">Laporkan Pelanggaran</h2>

<div class="card shadow-sm p-4">
    <!-- enctype penting untuk upload file -->
    <form action="/pelanggaran/simpanLaporan" method="post" enctype="multipart/form-data">
        
        <div class="mb-3">
            <label>Tanggal Kejadian</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Area Parkir</label>
            <select name="id_area" class="form-select" required>
                <?php foreach($area as $a): ?>
                    <option value="<?= $a['id_area']; ?>"><?= $a['nama_area']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Bukti Foto</label>
            <input type="file" name="foto" class="form-control" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label>Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
        <a href="/pelanggaran" class="btn btn-secondary">Batal</a>
    </form>
</div>