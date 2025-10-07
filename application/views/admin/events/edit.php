<?php $action = site_url('admin/events/' . $event->event_id . '/update'); ?>

<div class="content p-3">

    <!-- ===== Page Header ===== -->
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <h4 class="fw-bold text-primary mb-0">
                    <i class="feather-plus-circle me-2"></i>Edit Event
                </h4>
            </div>
            <div class="col-sm-4 text-end">
                <a href="<?= site_url('admin/events') ?>" class="btn btn-outline-secondary">
                    <i class="feather-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- ===== Event Form Card ===== -->
    <div class="card shadow-sm border-0 rounded">
        <div class="card-body p-4">


            <?php $this->load->view('admin/events/_form', compact('action', 'event')); ?>

        </div>
    </div>
</div>