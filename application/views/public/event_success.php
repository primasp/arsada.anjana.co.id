<div class="content p-3">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('event') ?>">Daftar Event</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Registrasi Berhasil</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="container py-4">
        <div class="card shadow-lg border-0 mx-auto" style="max-width:700px;">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="feather-check-circle text-success" style="font-size:4rem;"></i>
                </div>
                <h3 class="fw-bold text-success mb-2">Registrasi Berhasil!</h3>
                <p class="text-muted mb-4">
                    Terima kasih <strong><?= html_escape($participant_name) ?></strong>,<br>
                    Anda telah terdaftar di event <strong><?= html_escape($event->title) ?></strong>.
                </p>

                <div class="mb-4">
                    <h5 class="text-secondary">Nomor Registrasi Anda:</h5>
                    <h2 class="fw-bold text-primary"><?= html_escape($registration_id) ?></h2>
                </div>

                <?php if (!empty($qr_url)) : ?>
                    <div class="mb-4">
                        <img src="<?= $qr_url ?>" alt="QR Code" class="shadow-sm rounded" width="180">
                        <p class="small text-muted mt-2">Tunjukkan QR ini saat verifikasi kehadiran.</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pdf_url)) : ?>
                    <a href="<?= $pdf_url ?>" class="btn btn-primary btn-lg px-4 mb-3" target="_blank">
                        <i class="feather-download me-1"></i> Unduh Bukti Registrasi (PDF)
                    </a>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?= site_url('event') ?>" class="btn btn-outline-secondary">
                        <i class="feather-arrow-left me-1"></i> Kembali ke Daftar Event
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 text-muted small">
            <p>ARSADA Event Management System — <?= date('Y') ?></p>
        </div>
    </div>
</div>