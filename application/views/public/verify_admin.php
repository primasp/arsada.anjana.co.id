<div class="content p-3">

    <!-- ===== Page Header ===== -->
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <h4 class="fw-bold text-primary mb-0 pt-2">
                    <!-- <i class="feather-plus-circle me-2"></i>Verifikasi Kehadiran -->
                </h4>
                <!-- / <p class="text-muted mb-0">Lengkapi informasi berikut untuk menambahkan event baru ke sistem.</p> -->
            </div>
            <div class="col-sm-4 text-end">
                <a href="<?= site_url('admin/events') ?>" class="btn btn-outline-secondary">
                    <i class="feather-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="card shadow-lg border-0 mx-auto" style="max-width:750px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="<?= base_url('assets/img/logo-arsada.jpg') ?>" height="60">
                    <h3 class="fw-bold text-primary mt-2">Verifikasi Kehadiran Peserta</h3>
                    <p class="text-muted">ARSADA Event Management System</p>
                </div>

                <?php if ($this->session->flashdata('msg_success')) : ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('msg_success') ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('verify') ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Event</label>
                        <!-- <select name="event_id" class="form-select" required>
                            <option value="">-- Pilih Event Aktif --</option>
                            <?php foreach ($events as $e) : ?>
                                <option value="<?= $e->event_id ?>" <?= set_select('event_id', $e->event_id) ?>>
                                    <?= html_escape($e->title) ?>
                                    (<?= date('d M Y', strtotime($e->start_at)) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select> -->


                        <select name="event_id" class="form-select" required>
                            <option value="">-- Pilih Event Aktif --</option>
                            <?php foreach ($events as $e) : ?>
                                <option value="<?= $e->event_id ?>" <?= ($selected_event_id == $e->event_id) ? 'selected' : '' ?>>
                                    <?= html_escape($e->title) ?>
                                    (<?= date('d M Y', strtotime($e->start_at)) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>




                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Registrasi</label>
                        <input type="text" name="registration_code" class="form-control" placeholder="Masukkan nomor registrasi..." required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="feather-search me-1"></i> Cari Peserta
                        </button>
                    </div>
                </form>

                <?php if (isset($participant)) : ?>
                    <hr class="my-4">
                    <div class="text-center">
                        <h4 class="fw-bold text-success">Data Peserta Ditemukan</h4>
                        <p><strong><?= html_escape($participant->participant_name) ?></strong></p>
                        <p><?= html_escape($participant->participant_email) ?></p>
                        <p><?= html_escape($participant->participant_phone) ?></p>
                        <p><strong>Event:</strong> <?= html_escape($participant->event_title) ?></p>
                        <p><strong>Lokasi:</strong> <?= html_escape($participant->venue) ?></p>
                        <p><strong>Waktu:</strong> <?= date('d M Y H:i', strtotime($participant->start_at)) ?></p>

                        <?php if ($participant->status_kehadiran === 'HADIR') : ?>
                            <div class="alert alert-success">✅ Sudah diverifikasi hadir.</div>
                        <?php else : ?>
                            <a href="<?= site_url('verify/' . $participant->registration_id . '/confirm') ?>" class="btn btn-success px-5">
                                <i class="feather-check-circle me-1"></i> Tandai Hadir
                            </a>
                        <?php endif; ?>
                    </div>
                <?php elseif (!empty($not_found)) : ?>
                    <hr class="my-4">
                    <div class="alert alert-warning text-center">Data tidak ditemukan. Silakan verifikasi manual.</div>
                    <form method="POST" action="<?= site_url('verify-confirm-manual') ?>">
                        <!-- <form method="POST" action="<?= site_url('verify/confirm') ?>"> -->
                        <!-- <input type="hidden" name="event_id" value="<?= $event_id ?>"> -->
                        <input type="hidden" name="event_id" value="<?= $selected_event_id ?>">
                        <div class="mb-3">
                            <label>Nama Peserta</label>
                            <input type="text" name="participant_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="participant_email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>No. Telepon</label>
                            <input type="text" name="participant_phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Tanda Tangan Digital</label>
                            <canvas id="signature-pad" class="border border-secondary rounded w-100" height="180"></canvas>
                            <input type="hidden" name="signature_data" id="signature-data">
                            <div class="text-center mt-2">
                                <button type="button" id="clear-signature" class="btn btn-outline-secondary btn-sm">
                                    Hapus Tanda Tangan
                                </button>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success px-5">
                                <i class="feather-check me-1"></i> Verifikasi Manual
                            </button>
                        </div>
                    </form>

                    <!-- <script>
                        // === Signature Pad (Digital Tanda Tangan) ===
                        const canvas = document.getElementById('signature-pad');
                        const ctx = canvas.getContext('2d');
                        let drawing = false;

                        canvas.addEventListener('mousedown', e => {
                            drawing = true;
                            ctx.beginPath();
                        });
                        canvas.addEventListener('mouseup', () => drawing = false);
                        canvas.addEventListener('mousemove', e => {
                            if (!drawing) return;
                            ctx.lineWidth = 2;
                            ctx.lineCap = 'round';
                            ctx.strokeStyle = '#000';
                            ctx.lineTo(e.offsetX, e.offsetY);
                            ctx.stroke();
                        });

                        // Convert tanda tangan ke Base64 sebelum submit
                        document.querySelector('form[action$="confirm-manual"]').addEventListener('submit', function() {
                            document.getElementById('signature-data').value = canvas.toDataURL();
                        });

                        // Tombol hapus tanda tangan
                        document.getElementById('clear-signature').addEventListener('click', function() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                        });
                    </script> -->


                    <script>
                        // === Signature Pad Fix Precision ===
                        const canvas = document.getElementById('signature-pad');
                        const ctx = canvas.getContext('2d');
                        let drawing = false;

                        // === Atur scaling untuk layar HiDPI (supaya tidak blur) ===
                        function resizeCanvas() {
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            const rect = canvas.getBoundingClientRect();
                            canvas.width = rect.width * ratio;
                            canvas.height = rect.height * ratio;
                            ctx.scale(ratio, ratio);
                        }
                        resizeCanvas();
                        window.addEventListener('resize', resizeCanvas);

                        // === Konversi posisi mouse agar akurat ===
                        function getPosition(event) {
                            const rect = canvas.getBoundingClientRect();
                            return {
                                x: event.clientX - rect.left,
                                y: event.clientY - rect.top
                            };
                        }

                        // === Event Mouse ===
                        canvas.addEventListener('mousedown', e => {
                            drawing = true;
                            ctx.beginPath();
                            const pos = getPosition(e);
                            ctx.moveTo(pos.x, pos.y);
                        });

                        canvas.addEventListener('mouseup', () => (drawing = false));

                        canvas.addEventListener('mousemove', e => {
                            if (!drawing) return;
                            const pos = getPosition(e);
                            ctx.lineWidth = 2;
                            ctx.lineCap = 'round';
                            ctx.strokeStyle = '#000';
                            ctx.lineTo(pos.x, pos.y);
                            ctx.stroke();
                        });

                        // === Event Touch (mobile support) ===
                        canvas.addEventListener('touchstart', e => {
                            e.preventDefault();
                            drawing = true;
                            const touch = e.touches[0];
                            const pos = getPosition(touch);
                            ctx.beginPath();
                            ctx.moveTo(pos.x, pos.y);
                        });

                        canvas.addEventListener('touchend', e => {
                            e.preventDefault();
                            drawing = false;
                        });

                        canvas.addEventListener('touchmove', e => {
                            e.preventDefault();
                            if (!drawing) return;
                            const touch = e.touches[0];
                            const pos = getPosition(touch);
                            ctx.lineWidth = 2;
                            ctx.lineCap = 'round';
                            ctx.strokeStyle = '#000';
                            ctx.lineTo(pos.x, pos.y);
                            ctx.stroke();
                        });

                        // === Simpan tanda tangan ke Base64 sebelum submit ===
                        const form = document.querySelector('form[action$="confirm-manual"]');
                        form.addEventListener('submit', function() {
                            document.getElementById('signature-data').value = canvas.toDataURL('image/png');
                        });

                        // === Tombol hapus tanda tangan ===
                        document.getElementById('clear-signature').addEventListener('click', function() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                        });
                    </script>



                <?php endif; ?>
            </div>
        </div>
    </div>



</div>