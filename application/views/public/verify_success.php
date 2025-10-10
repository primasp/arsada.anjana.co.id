<div class="content p-5">
    <div class="container text-center">
        <div class="card shadow-lg border-0 mx-auto" style="max-width:700px;">
            <div class="card-body p-5">
                <img src="<?= base_url('assets/img/logo-arsada.jpg') ?>" height="70" class="mb-3">
                <h3 class="fw-bold text-primary mb-3">ARSADA EVENT SYSTEM</h3>

                <?php if ($success) : ?>
                    <div class="alert alert-success fw-semibold py-3 mb-4">
                        ✅ Kehadiran berhasil diverifikasi!
                    </div>
                    <h4 class="text-dark mb-2"><?= html_escape($participant->participant_name) ?></h4>
                    <p class="text-muted mb-1"><?= html_escape($participant->participant_email) ?></p>
                    <p class="mb-3">
                        <strong>Event:</strong> <?= html_escape($participant->event_title) ?><br>
                        <small class="text-secondary"><?= html_escape($participant->venue) ?></small>
                    </p>

                    <div class="border rounded bg-light p-3 mb-3">
                        <strong>Waktu Verifikasi:</strong><br>
                        <span class="text-success"><?= $verified_at ?></span>
                    </div>

                    <div class="text-center mt-4">
                        <i class="feather-check-circle text-success" style="font-size:4rem;"></i>
                        <p class="text-muted mt-3">
                            Anda akan diarahkan kembali ke halaman verifikasi utama dalam beberapa detik...
                        </p>
                    </div>

                    <script>
                        // setTimeout(() => {
                        //     window.location.href = "<?= site_url('verify') ?>";
                        // }, 5000); // kembali otomatis setelah 5 detik

                        setTimeout(() => {
                            window.location.href = "<?= site_url('verify?event_id=' . $event_id) ?>";
                        }, 5000); // kembali otomatis ke verify + event_id sebelumnya
                    </script>

                <?php else : ?>
                    <div class="alert alert-warning py-3 mb-4">
                        ⚠️ Peserta ini sudah tercatat hadir sebelumnya.
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= site_url('verify') ?>" class="btn btn-outline-secondary">
                            <i class="feather-arrow-left me-1"></i> Kembali ke Halaman Verifikasi
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>