<div class="content p-3">
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
    <div class="row">



        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Form Builder</h3>
                    <div class="text-muted">Event: <strong><?= html_escape($event->title) ?>
                        </strong> — Form: <strong><?= html_escape($form->name) ?></strong></div>
                </div>
                <!-- <a class="btn btn-secondary" href="<?= site_url('admin/events') ?>">Kembali</a> -->
            </div>
            <div class="btn-group">
                <a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalRender">
                    <i class="feather-eye me-1"></i> View Render
                </a>
                <a class="btn btn-secondary" href="<?= site_url('admin/events') ?>">Kembali</a>
            </div>
            <input type="hidden" id="form_id" value="<?= $form->form_id ?>">
            <!-- Section List -->
            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card">
                        <!-- <div class="card-header d-flex justify-content-between align-itemscenter"> -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Sections</strong>
                            <button class="btn btn-sm btn-primary" id="btnAddSection">+
                                Section</button>
                        </div>
                        <ul class="list-group list-group-flush" id="sectionList">
                            <?php foreach ($sections as $s) : ?>
                                <!-- <li class="list-group-item d-flex justify-content-between align-items-center section-item" data-id="<?= $s->section_id ?>">
                                    <span><?= html_escape($s->title) ?></span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btnAddQuestion" data-section="<?= $s->section_id ?>">+ Question</button>
                                        <button class="btn btn-outline-danger btnDelSection" data-id="<?= $s->section_id ?>">Hapus</button>
                                    </div>
                                </li> -->



                                <li class="list-group-item d-flex justify-content-between align-items-center section-item" data-id="<?= $s->section_id ?>">
                                    <div class="d-flex align-items-center">
                                        <span class="drag-handle me-2 text-muted" title="Geser untuk ubah urutan" style="cursor: grab;">
                                            <i data-feather="move"></i>
                                        </span>
                                        <span><?= html_escape($s->title) ?></span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btnAddQuestion" data-section="<?= $s->section_id ?>">+ Question</button>
                                        <button class="btn btn-outline-danger btnDelSection" data-id="<?= $s->section_id ?>">Hapus</button>
                                    </div>
                                </li>





                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>






                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><strong>Pertanyaan</strong></div>
                        <div class="list-group list-group-flush question-list" id="questionList">
                            <?php foreach ($sections as $s) : ?>
                                <div class="list-group-item bg-light fw-semibold">
                                    <?= html_escape($s->title) ?>
                                </div>

                                <?php if (!empty($questions[$s->section_id])) : ?>
                                    <?php foreach ($questions[$s->section_id] as $q) : ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center question-item" data-id="<?= $q->question_id ?>">
                                            <div class="d-flex align-items-start">
                                                <span class="drag-handle me-2 text-muted" title="Geser untuk ubah urutan" style="cursor: grab;">
                                                    <i data-feather="move"></i>
                                                </span>
                                                <div>
                                                    <div>
                                                        <strong>
                                                            <?= html_escape($q->label) ?>
                                                            <?php if (!empty($q->is_required) && $q->is_required == "t") : ?>
                                                                <span class="text-danger" title="Wajib diisi">*</span>
                                                            <?php endif; ?>
                                                        </strong>
                                                        <small class="text-muted">(<?= $q->question_type ?>)</small>
                                                    </div>
                                                    <?php if (in_array($q->question_type, ['single_choice', 'multi_choice', 'radio', 'checkbox', 'select'])) : ?>
                                                        <div class="mt-1">
                                                            <button class="btn btn-sm btn-outline-secondary btnAddOption" data-qid="<?= $q->question_id ?>">+ Option</button>

                                                            <?php if (!empty($q->options)) : ?>
                                                                <ul class="small mt-2 ms-3 text-muted">
                                                                    <?php foreach ($q->options as $opt) : ?>
                                                                        <li><?= html_escape($opt->option_label) ?>
                                                                            <small class="text-secondary">(<?= html_escape($opt->option_value) ?>)</small>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            <?php else : ?>
                                                                <div class="small text-muted ms-3">Belum ada opsi</div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                            <button class="btn btn-sm btn-outline-danger btnDelQuestion" data-id="<?= $q->question_id ?>">Hapus</button>
                                        </div>





                                        <!-- <div class="list-group-item d-flex justify-content-between align-items-center question-item" data-id="<?= $q->question_id ?>">
                                            <div class="d-flex align-items-start">
                                                <span class="drag-handle me-2 text-muted" title="Geser untuk ubah urutan" style="cursor: grab;">
                                                    <i data-feather="move"></i>
                                                </span>
                                                <div>
                                                    <strong>
                                                        <?= html_escape($q->label) ?>
                                                        <?php if (!empty($q->is_required) && $q->is_required == "t") : ?>
                                                            <span class="text-danger" title="Wajib diisi">*</span>
                                                        <?php endif; ?>
                                                    </strong>
                                                    <small class="text-muted">(<?= $q->question_type ?>)</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger btnDelQuestion" data-id="<?= $q->question_id ?>">Hapus</button>
                                        </div> -->









                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="list-group-item text-muted fst-italic ps-4">Belum ada pertanyaan</div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

























            </div>
        </div>
        <!-- Simple modals inline -->






    </div>
</div>