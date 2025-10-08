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




    <!-- ====== Event List ====== -->
    <div class="row">
        <?php if (!empty($events)) : ?>
            <?php foreach ($events as $e) : ?>
                <?php
                $poster = !empty($e->poster_url) ? $e->poster_url : (!empty($e->poster_path) ? base_url($e->poster_path) : base_url('assets/img/event/blog-1.jpg'));
                $isActive = (bool)$e->is_active;
                $badgeCls = $e->status === 'open' ? 'bg-success' : ($e->status === 'closed' ? 'bg-secondary' : 'bg-warning');
                ?>
                <div class="col-sm-6 col-md-6 col-xl-4">
                    <div class="blog grid-blog">
                        <div class="blog-image">
                            <a href="blog-details.html">
                                <img src="<?= html_escape($poster) ?>" class="card-img-top" alt="Poster <?= html_escape($e->title) ?>" style="height: 220px; object-fit: cover;">
                            </a>
                            <div class="position-absolute top-0 end-0 m-2">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/events/' . $e->event_id . '/edit') ?>">Edit</a>
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/events/' . $e->event_id . '/builder') ?>">Builder</a>
                                <a class="btn btn-sm btn-success" href="<?= site_url('admin/events/' . $e->event_id . '/publish') ?>">Publish</a>
                                <a href="#" class="btn btn-sm btn-outline-danger btnDeleteEvent" data-id="<?= $e->event_id ?>">Hapus</a>
                            </div>
                            <div class="blog-views">
                                <h5><?= html_escape($e->event_code) ?></h5>
                            </div>
                            <ul class="nav view-blog-list blog-views">
                                <li><i class="feather-eye me-1"></i><?= $e->status ?></li>
                            </ul>

                        </div>
                        <div class="blog-content">
                            <div class="blog-grp-blk">
                                <div class="blog-img-blk">
                                    <!-- <a href="profile.html"><img class="img-fluid" src="assets/img/profiles/avatar-01.jpg" alt=""></a> -->
                                    <!-- <div class="content-blk-blog ms-2">
                                        <h4><a href="profile.html">Jenifer Robinson</a></h4>
                                        <h5>M.B.B.S, Diabetologist</h5>
                                    </div> -->
                                </div>
                                <span><i class="feather-calendar me-1"></i><?= $e->start_at ?> – <?= $e->end_at ?></span>
                            </div>
                            <h3 class="blog-title"><a href="blog-details.html"><?= html_escape($e->title) ?></a></h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...</p>
                            <a href="blog-details.html" class="read-more d-flex"> Read more in 8 Minutes<i class="fa fa-long-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
        <?php endif; ?>
    </div>






</div>