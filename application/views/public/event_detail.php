<?php
$poster = !empty($event->poster_url)
    ? $event->poster_url
    : (!empty($event->poster_path) ? base_url($event->poster_path) : base_url('assets/img/event/blog-01.jpg'));
?>

<div class="content p-4">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('event') ?>">Event</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= html_escape($event->title) ?></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Greeting Block -->
    <div class="good-morning-blk mt-5 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="morning-user">
                    <h4 class="fw-bold" style="margin-bottom: 5px; font-size: 1.5rem;">
                        <?= $greeting; ?>, <span>Peserta</span>
                    </h4>
                    <p class="text-muted mb-0">Semoga harimu menyenangkan!</p>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <img src="<?= base_url('assets/img/morning-img-01.png'); ?>" alt="Welcome" class="img-fluid" style="max-height: 120px;">
            </div>
        </div>
    </div>

    <!-- Event Detail -->
    <div class="container">
        <div class="card border-0 shadow-sm p-4">
            <!-- <div class="row align-items-start"> -->
            <div class="row align-items-center">

                <!-- Poster -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <!-- <img src="<?= html_escape($event->poster_url ?? base_url('assets/img/event/blog-1.jpg')) ?>" alt="<?= html_escape($event->title) ?>" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; max-height: 350px;"> -->
                    <img src="<?= html_escape($poster) ?>" alt="Poster <?= html_escape($event->title) ?>" class="img-fluid rounded shadow-sm w-100" loading="lazy">

                </div>

                <!-- Event Info -->
                <div class="col-md-8">
                    <h2 class="fw-bold text-primary"><?= html_escape($event->title) ?></h2>

                    <p class="text-muted mb-1">
                        <i class="feather-calendar me-1"></i>
                        <?= date('d M Y H:i', strtotime($event->start_at)) ?> -
                        <?= date('d M Y H:i', strtotime($event->end_at)) ?>
                    </p>

                    <p class="text-muted mb-3">
                        <i class="feather-map-pin me-1"></i>
                        <?= html_escape($event->venue) ?>
                    </p>

                    <p class="mb-4">
                        <?= nl2br(html_escape($event->description)) ?>
                    </p>

                    <!-- Tombol Aksi -->
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= site_url('event/daftar/' . $event->event_code) ?>" class="btn btn-success px-4 shadow-sm">
                            <i class="feather-check-circle me-1"></i> Pendaftaran Peserta
                        </a>

                        <a href="<?= site_url('event') ?>" class="btn btn-outline-secondary px-4 shadow-sm">
                            <i class="feather-arrow-left me-1"></i> Kembali ke Daftar Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>