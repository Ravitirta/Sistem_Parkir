<h2 class="mb-4">Transaksi Kendaraan Masuk</h2>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <!-- Tambahkan ID form agar bisa dipanggil JS -->
        <form action="/dashboard/simpanMasuk" method="post" id="formTransaksi">
            
            <!-- INPUT PLAT NOMOR -->
            <div class="mb-4">
                <label class="form-label text-muted">Plat Nomor</label>
                <!-- Hapus 'required' -->
                <input type="text" name="plat_nomor" id="plat_nomor" class="form-control form-control-lg" placeholder="Contoh: 3477WCD">
            </div>

            <!-- INPUT AREA PARKIR -->
            <div class="mb-4">
                <label class="form-label text-muted">Area Parkir</label>
                <!-- Hapus 'required' -->
                <select name="id_area" id="id_area" class="form-select form-select-lg">
                    <!-- Tambahkan opsi default kosong untuk validasi -->
                    <option value="">-- Pilih Area Parkir --</option>
                    <?php foreach($area as $a): ?>
                        <option value="<?= $a['id_area']; ?>"><?= $a['nama_area']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- INPUT JENIS KENDARAAN -->
            <div class="mb-4">
                <label class="form-label text-muted">Jenis Kendaraan</label>
                <!-- Hapus 'required' -->
                <select name="id_kendaraan" id="id_kendaraan" class="form-select form-select-lg">
                    <!-- Tambahkan opsi default kosong untuk validasi -->
                    <option value="">-- Pilih Jenis Kendaraan --</option>
                    <?php foreach($kendaraan as $k): ?>
                        <option value="<?= $k['id_kendaraan']; ?>"><?= $k['jenis_kendaraan']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary btn-lg mt-2">
                Simpan Transaksi
            </button>

        </form>

    </div>
</div>

<!-- SCRIPT VALIDASI JAVASCRIPT -->
<script>
    document.getElementById('formTransaksi').addEventListener('submit', function(event) {
        
        // 1. Ambil nilai inputan
        var plat = document.getElementById('plat_nomor').value.trim();
        var area = document.getElementById('id_area').value;
        var jenis = document.getElementById('id_kendaraan').value;
        
        // 2. Variable untuk pesan error
        var pesanError = '';

        // 3. Cek logika validasi
        if (!plat) {
            pesanError = 'Mohon isi Plat Nomor kendaraan!';
        } else if (!area) {
            pesanError = 'Mohon pilih Area Parkir!';
        } else if (!jenis) {
            pesanError = 'Mohon pilih Jenis Kendaraan!';
        }

        // 4. Jika ada error
        if (pesanError) {
            // Stop form agar tidak terkirim ke server
            event.preventDefault(); 
            
            // Tampilkan SweetAlert Warning
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: pesanError,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke, Saya Lengkapi'
            });
        }
        // Jika tidak ada error, form akan lanjut submit secara otomatis
    });
</script>