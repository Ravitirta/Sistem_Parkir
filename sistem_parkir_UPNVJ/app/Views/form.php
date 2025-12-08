<h2 class="mb-4">Transaksi Kendaraan Keluar</h2>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <?php if (session()->getFlashdata('berhasil')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('berhasil') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('gagal')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('gagal') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('dashboard/simpanKeluar') ?>" method="post">
            <?= csrf_field() ?> 
            
            <div class="mb-3">
                <label class="form-label fw-bold">Masukkan ID Transaksi</label>
                <input type="text" name="id_transaksi" class="form-control form-control-lg" placeholder="Contoh: TR_001" required>
            </div>
            
            <button type="submit" class="btn btn-danger btn-lg">
                <i class="bi bi-box-arrow-left"></i> Proses Keluar
            </button>
        </form>

    </div>
</div>
