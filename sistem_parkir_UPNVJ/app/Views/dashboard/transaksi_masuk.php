<h2 class="mb-4">Transaksi Kendaraan Masuk</h2>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <form action="/dashboard/simpanMasuk" method="post">
            
            <div class="mb-4">
                <label class="form-label text-muted">Plat Nomor</label>
                <input type="text" name="plat_nomor" class="form-control form-control-lg" placeholder="Contoh: B 1234 XYZ" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted">Area Parkir</label>
                <select name="id_area" class="form-select form-select-lg" required>
                    <?php foreach($area as $a): ?>
                        <option value="<?= $a['id_area']; ?>"><?= $a['nama_area']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted">Jenis Kendaraan</label>
                <select name="id_kendaraan" class="form-select form-select-lg" required>
                    <?php foreach($kendaraan as $k): ?>
                        <option value="<?= $k['id_kendaraan']; ?>"><?= $k['jenis_kendaraan']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-2">
                Simpan Transaksi
            </button>

        </form>

    </div>
</div>
