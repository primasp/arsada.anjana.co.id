<div class="content p-3">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('event') ?>">Event</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active"><?= html_escape($event->title) ?></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="good-morning-blk mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="morning-user">
                    <h4 style="margin-bottom: 5px; font-size: 1.5rem;"><?= $greeting; ?>, <span> Peserta</span></h4>
                    <p>Have a nice day</p>
                </div>
            </div>
            <div class="col-md-6 position-blk">
                <div class="morning-img">
                    <img src="<?= base_url(); ?>assets/img/morning-img-01.png" alt="" class="img-fluid h-100">
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Informasi Event -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary mb-1"><?= html_escape($event->title) ?></h2>
                        <p class="text-muted">
                            <?= html_escape($event->venue) ?> —
                            <?= date('d M Y H:i', strtotime($event->start_at)) ?>
                        </p>
                        <p><?= nl2br(html_escape($event->description)) ?></p>

                        <!-- 🔙 Tombol Kembali -->
                        <a href="<?= site_url('event/' . $event->event_code) ?>" class="btn btn-outline-secondary btn-sm px-3 mt-2 shadow-sm">
                            <i class="feather-arrow-left me-1"></i> Kembali ke Daftar Event
                        </a>
                    </div>

                    <?php if ($this->session->flashdata('msg_success')) : ?>
                        <div class="alert alert-success"><?= $this->session->flashdata('msg_success') ?></div>
                    <?php endif; ?>

                    <!-- Form Pendaftaran -->
                    <form action="<?= site_url('event/' . $event->event_code . '/submit') ?>" method="POST" enctype="multipart/form-data" class="shadow-sm bg-white p-4 rounded">

                        <?php foreach ($sections as $s) : ?>
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-light fw-bold"><?= html_escape($s->title) ?></div>
                                <div class="card-body">

                                    <?php if (!empty($questions[$s->section_id])) : ?>
                                        <?php foreach ($questions[$s->section_id] as $q) : ?>
                                            <?php
                                            $type = $q->question_type;
                                            $name = "q_" . $q->question_id;
                                            $required = ($q->is_required == 't') ? 'required' : '';
                                            ?>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <?= html_escape($q->label) ?>
                                                    <?php if ($q->is_required == 't') : ?>
                                                        <span class="text-danger">*</span>
                                                    <?php endif; ?>
                                                </label>

                                                <?php if ($type === 'short_text') : ?>
                                                    <input type="text" name="<?= $name ?>" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>" <?= $required ?>>

                                                <?php elseif ($type === 'long_text') : ?>
                                                    <textarea name="<?= $name ?>" rows="3" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>" <?= $required ?>></textarea>

                                                <?php elseif ($type === 'number') : ?>
                                                    <input type="number" name="<?= $name ?>" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>" <?= $required ?>>

                                                <?php elseif ($type === 'email') : ?>
                                                    <input type="email" name="<?= $name ?>" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>" <?= $required ?>>

                                                <?php elseif ($type === 'date') : ?>
                                                    <input type="date" name="<?= $name ?>" class="form-control" <?= $required ?>>

                                                <?php elseif ($type === 'datetime') : ?>
                                                    <input type="datetime-local" name="<?= $name ?>" class="form-control" <?= $required ?>>

                                                <?php elseif ($type === 'url') : ?>
                                                    <input type="url" name="<?= $name ?>" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>" <?= $required ?>>

                                                <?php elseif ($type === 'file') : ?>
                                                    <input type="file" name="<?= $name ?>" class="form-control" <?= $required ?>>

                                                <?php elseif ($type === 'single_choice' || $type === 'radio') : ?>
                                                    <?php foreach ($q->options as $opt) : ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="<?= $name ?>" value="<?= html_escape($opt->option_value) ?>" id="opt_<?= $opt->option_id ?>" <?= $required ?>>
                                                            <label class="form-check-label" for="opt_<?= $opt->option_id ?>">
                                                                <?= html_escape($opt->option_label) ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>

                                                <?php elseif ($type === 'multi_choice' || $type === 'checkbox') : ?>
                                                    <?php foreach ($q->options as $opt) : ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="<?= $name ?>[]" value="<?= html_escape($opt->option_value) ?>" id="opt_<?= $opt->option_id ?>" <?= $required ?>>
                                                            <label class="form-check-label" for="opt_<?= $opt->option_id ?>">
                                                                <?= html_escape($opt->option_label) ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>

                                                <?php elseif ($type === 'select') : ?>
                                                    <select name="<?= $name ?>" class="form-select" <?= $required ?>>
                                                        <option value="">-- Pilih --</option>
                                                        <?php foreach ($q->options as $opt) : ?>
                                                            <option value="<?= html_escape($opt->option_value) ?>">
                                                                <?= html_escape($opt->option_label) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <p class="text-muted fst-italic">Belum ada pertanyaan pada section ini.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="feather-send me-1"></i> Kirim Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>