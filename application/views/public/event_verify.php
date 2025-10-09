<div class="content p-4">
    <div class="container text-center">

        <div class="card shadow-lg border-0 mx-auto" style="max-width:600px;">
            <div class="card-body p-5">
                <img src="<?= base_url('assets/img/logo-arsada.png') ?>" height="70" class="mb-3">
                <h3 class="fw-bold text-primary mb-2">Verifikasi Kehadiran</h3>
                <p class="text-muted mb-4">ARSADA Event Management System</p>

                <?php if ($already) : ?>
                    <div class="alert alert-success">
                        ✅ Peserta ini sudah diverifikasi hadir sebelumnya.
                    </div>
                <?php elseif ($this->session->flashdata('msg_success')) : ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('msg_success') ?></div>
                <?php elseif ($this->session->flashdata('msg_error')) : ?>
                    <div class="alert alert-warning"><?= $this->session->flashdata('msg_error') ?></div>
                <?php endif; ?>

                <h4 class="mt-3 text-dark fw-bold"><?= html_escape($participant->participant_name) ?></h4>
                <p class="text-secondary mb-1"><?= html_escape($participant->participant_email) ?></p>
                <p class="text-secondary mb-3"><?= html_escape($participant->participant_phone) ?></p>

                <div class="border rounded bg-light p-3 mb-3">
                    <strong><?= html_escape($participant->event_title) ?></strong><br>
                    <small><?= html_escape($participant->venue) ?></small><br>
                    <small><?= date('d M Y H:i', strtotime($participant->start_at)) ?></small>
                </div>

                <?php if (!$already) : ?>
                    <a href="<?= site_url('verify/' . $participant->registration_id . '/confirm') ?>" class="btn btn-success btn-lg px-5">
                        <i class="feather-check-circle me-1"></i> Tandai Hadir
                    </a>
                <?php endif; ?>

                <hr class="my-4">

                <p class="text-muted small">© <?= date('Y') ?> Arsada Event System</p>
            </div>
        </div>
    </div>
</div>