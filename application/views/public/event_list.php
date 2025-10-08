<div class="content p-3">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Admin Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="good-morning-blk mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="morning-user">
                    <!-- <h2>Good Morning, <span>Client</span></h2> -->
                    <h4 style="margin-bottom: 5px; font-size: 1.5rem;"><?= $greeting; ?>, <span> Peserta</span></h4>
                    <!-- <h2>Good Morning, <span><?= $user['full_name'] ?></span></h2> -->
                    <p>Have a nice day</p>
                </div>
            </div>
            <div class="col-md-6 position-blk">
                <div class="morning-img">
                    <img src="<?= base_url(); ?>assets/img/morning-img-01.png" alt="" class="img-fluid  h-100">
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="content p-4 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-primary mb-2">Daftar Event</h2>
                    <p class="text-muted">Temukan dan ikuti event terbaru dari Arsada & Anjana.</p>
                </div>

                <div class="row g-4">
                    <?php if (!empty($events)) : ?>
                        <?php foreach ($events as $e) : ?>
                            <?php
                            $poster = !empty($e->poster_url)
                                ? $e->poster_url
                                : base_url('assets/img/event/blog-1.jpg');
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card shadow-sm h-100 border-0">
                                    <div class="position-relative">
                                        <img src="<?= html_escape($poster) ?>" class="card-img-top" alt="<?= html_escape($e->title) ?>" style="height:220px; object-fit:cover; border-radius:.5rem .5rem 0 0;">
                                        <?php if ($e->status === 'published') : ?>
                                            <span class="badge bg-success position-absolute top-0 start-0 m-2">Published</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold mb-1"><?= html_escape($e->title) ?></h5>
                                        <p class="text-muted small mb-2">
                                            <i class="feather-calendar"></i>
                                            <?= date('d M Y H:i', strtotime($e->start_at)) ?> -
                                            <?= date('d M Y H:i', strtotime($e->end_at)) ?>
                                        </p>
                                        <p class="text-muted small mb-3">
                                            <i class="feather-map-pin"></i>
                                            <?= html_escape($e->venue) ?>
                                        </p>
                                        <p class="text-secondary flex-grow-1"><?= character_limiter(strip_tags($e->description), 100) ?></p>

                                        <a href="<?= site_url('event/' . $e->event_code) ?>" class="btn btn-primary mt-auto w-100">
                                            <i class="feather-eye me-1"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col-12 text-center">
                            <p class="text-muted fs-5">Belum ada event yang tersedia.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>