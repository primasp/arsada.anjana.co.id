<div class="content p-3">

    <!-- Page Header -->
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <h4 class="fw-bold mb-1 text-primary">Daftar Event</h4>
                <p class="text-muted mb-0">Kelola semua event, status, dan publikasi.</p>
            </div>
            <div class="col-sm-4 text-end">
                <a href="<?= base_url('admin/events/create') ?>" class="btn btn-primary">
                    <i class="feather-plus me-1"></i> Tambah Event
                </a>
            </div>
        </div>
    </div>

    <style>

    </style>

    <!-- ====== Event List ====== -->
    <div class="row g-3">
        <?php if (!empty($events)) : ?>
            <?php foreach ($events as $e) : ?>
                <?php
                $poster = !empty($e->poster_url)
                    ? $e->poster_url
                    : (!empty($e->poster_path) ? base_url($e->poster_path) : base_url('assets/img/event/blog-01.jpg'));
                $isActive  = !empty($e->is_active);
                $status    = strtolower($e->status ?? 'draft');
                $badgeCls  = $status === 'open' ? 'bg-success'
                    : ($status === 'closed' ? 'bg-secondary'
                        : ($status === 'archived' ? 'bg-dark' : 'bg-warning text-dark'));
                $startDisp = !empty($e->start_at) ? $e->start_at : '-';
                $endDisp   = !empty($e->end_at) ? $e->end_at   : '-';
                ?>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card card-event h-100">

                        <!-- Poster -->






                        <div class="position-relative poster-box">

                            <!-- Poster Gambar -->
                            <div class="ratio ratio-16x9">
                                <img src="<?= html_escape($poster) ?>" alt="Poster <?= html_escape($e->title) ?>" class="w-100 h-100" loading="lazy" style="">
                            </div>

                            <!-- Badge kiri atas -->
                            <div class="position-absolute top-0 start-0 p-2 d-flex align-items-center gap-2">
                                <span class="badge event-code bg-light text-dark fw-semibold"><?= html_escape($e->event_code) ?></span>
                                <span class="badge <?= $badgeCls ?> text-uppercase"><?= html_escape($status) ?></span>
                                <?php if (!$isActive) : ?><span class="badge bg-dark">Nonaktif</span><?php endif; ?>
                            </div>

                            <!-- Toolbar tombol di bawah poster -->
                            <div class="d-flex justify-content-end gap-1 p-2 bg-light border-top" style="border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem;">
                                <a class="btn btn-sm btn-light" href="<?= site_url('admin/events/' . $e->event_id . '/edit') ?>">Edit</a>
                                <a class="btn btn-sm btn-primary" href="<?= site_url('admin/events/' . $e->event_id . '/builder') ?>">Builder</a>
                                <a class="btn btn-sm btn-success" href="<?= site_url('admin/events/' . $e->event_id . '/publish') ?>">Publish</a>
                                <a href="#" class="btn btn-sm btn-outline-danger btnDeleteEvent" data-id="<?= $e->event_id ?>">Hapus</a>
                            </div>

                        </div>














                        <!-- Body -->
                        <div class="card-body">
                            <div class="small text-muted mb-2 d-flex align-items-center gap-3 flex-wrap">
                                <span><i class="feather-calendar me-1"></i><?= $startDisp ?> – <?= $endDisp ?></span>
                                <?php if (!empty($e->venue)) : ?>
                                    <span class="d-inline-flex align-items-center"><i class="feather-map-pin me-1"></i><?= html_escape($e->venue) ?></span>
                                <?php endif; ?>
                            </div>

                            <h5 class="card-title mb-1 text-truncate-2" title="<?= html_escape($e->title) ?>">
                                <?= html_escape($e->title) ?>
                            </h5>

                            <p class="card-text text-muted mb-0 text-truncate-3">
                                <?= html_escape($e->description ?? '—') ?>
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <a href="<?= site_url('admin/events/' . $e->event_id . '/builder') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="feather-sliders me-1"></i> Sesuaikan Form
                            </a>
                            <div class="text-muted small">
                                <i class="feather-eye me-1"></i><?= html_escape($e->status) ?>
                            </div>
                        </div>

                    </div>
                </div>

















            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-light border text-center mb-0">
                    Belum ada event. Klik <a href="<?= base_url('admin/events/create') ?>">Tambah Event</a> untuk mulai.
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>