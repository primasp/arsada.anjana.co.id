<div class="content p-4 bg-light">

    <!-- Page Header -->
    <div class="page-header border-0 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-primary mb-1">Daftar Event Publik</h2>
                <p class="text-muted mb-0">Temukan dan ikuti event terbaru dari Arsada &amp; Anjana.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= site_url() ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="feather-arrow-left me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Greeting Section -->
    <div class="good-morning-blk mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="morning-user">
                    <h4 class="fw-bold" style="font-size:1.5rem;">
                        <?= $greeting; ?>, <span>Peserta</span>
                    </h4>
                    <p class="text-muted mb-0">Semoga harimu menyenangkan!</p>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <img src="<?= base_url('assets/img/morning-img-01.png'); ?>" alt="Welcome" class="img-fluid" style="max-height:120px;">
            </div>
        </div>
    </div>

    <!-- Event List -->
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($events)) : ?>
                <?php foreach ($events as $e) : ?>
                    <?php
                    // Gambar poster
                    // $poster = !empty($e->poster_url)
                    //     ? $e->poster_url
                    //     : base_url('assets/img/event/blog-1.jpg');

                    $poster = !empty($e->poster_url) ? $e->poster_url : (!empty($e->poster_path) ? base_url($e->poster_path) : base_url('assets/img/event/blog-01.jpg'));

                    // Badge status event (PHP 7 compatible)
                    if ($e->status === 'published') {
                        $badgeClass = 'bg-success';
                    } elseif ($e->status === 'open') {
                        $badgeClass = 'bg-primary';
                    } elseif ($e->status === 'closed') {
                        $badgeClass = 'bg-secondary';
                    } elseif ($e->status === 'draft') {
                        $badgeClass = 'bg-warning text-dark';
                    } else {
                        $badgeClass = 'bg-light text-muted';
                    }

                    // Status waktu event
                    $now   = time();
                    $start = strtotime($e->start_at);
                    $end   = strtotime($e->end_at);

                    if ($now < $start) {
                        $eventStatus = '<span class="badge bg-info">Akan Datang</span>';
                    } elseif ($now >= $start && $now <= $end) {
                        $eventStatus = '<span class="badge bg-success">Sedang Berlangsung</span>';
                    } else {
                        $eventStatus = '<span class="badge bg-secondary">Selesai</span>';
                    }
                    ?>
                    <div class="col-sm-6 col-lg-4">
                        <!-- <div class="card shadow-sm border-0 h-100 event-card"> -->
                        <div class="card card-event h-100">
                            <div class="position-relative poster-box">
                                <!-- <img src="<?= html_escape($poster) ?>" class="card-img-top rounded-top" alt="<?= html_escape($e->title) ?>" style="height:230px; object-fit:cover;"> -->

                                <div class="ratio ratio-16x9">
                                    <img src="<?= html_escape($poster) ?>" alt="Poster <?= html_escape($e->title) ?>" class="w-100 h-100" loading="lazy">
                                </div>



                                <span class="badge <?= $badgeClass ?> position-absolute top-0 start-0 m-2 text-uppercase">
                                    <?= ucfirst($e->status) ?>
                                </span>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold text-dark mb-2"><?= html_escape($e->title) ?></h5>

                                <div class="mb-2">
                                    <i class="feather-calendar text-primary me-1"></i>
                                    <small class="text-muted">
                                        <?= date('d M Y H:i', strtotime($e->start_at)) ?> —
                                        <?= date('d M Y H:i', strtotime($e->end_at)) ?>
                                    </small>
                                </div>

                                <div class="mb-2">
                                    <i class="feather-map-pin text-danger me-1"></i>
                                    <small class="text-muted"><?= html_escape($e->venue) ?></small>
                                </div>

                                <p class="text-secondary small flex-grow-1 mb-3">
                                    <?= character_limiter(strip_tags($e->description), 100) ?>
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div><?= $eventStatus ?></div>

                                    <?php if (!empty($e->max_participants)) : ?>
                                        <span class="badge bg-light text-dark border">
                                            <i class="feather-users me-1"></i>
                                            Maks <?= $e->max_participants ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a href="<?= site_url('event/' . $e->event_code) ?>" class="btn btn-primary w-100 mt-3 shadow-sm">
                                    <i class="feather-eye me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12 text-center">
                    <p class="text-muted fs-5">Belum ada event yang tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Inline CSS -->
<style>
    .event-card {
        transition: all .25s ease-in-out;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }

    .event-card img {
        border-top-left-radius: .5rem;
        border-top-right-radius: .5rem;
    }
</style>