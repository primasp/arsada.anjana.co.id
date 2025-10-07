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
                <a class="btn btn-secondary" href="<?= site_url('admin/events') ?>">Kembali</a>
            </div>
            <input type="hidden" id="form_id" value="<?= $form->form_id ?>">
            <!-- Section List -->
            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-itemscenter">
                            <strong>Sections</strong>
                            <button class="btn btn-sm btn-primary" id="btnAddSection">+
                                Section</button>
                        </div>
                        <ul class="list-group list-group-flush" id="sectionList">
                            <?php foreach ($sections as $s) : ?>
                                <li class="list-group-item d-flex justify-content-between alignitems-center" data-id="<?= $s->section_id ?>">
                                    <span><?= html_escape($s->title) ?></span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btnAddQuestion" datasection="<?= $s->section_id ?>">+ Question</button>
                                        <button class="btn btn-outline-danger btnDelSection" dataid="<?= $s->section_id ?>">Hapus</button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><strong>Pertanyaan</strong></div>
                        <div class="list-group list-group-flush" id="questionList">
                            <?php foreach ($questions as $section_id => $items) : ?>
                                <?php foreach ($items as $q) : ?>
                                    <div class="list-group-item d-flex justify-content-between
align-items-center" data-id="<?= $q->question_id ?>">
                                        <div>
                                            <div><strong><?= html_escape($q->label) ?></strong> <small class="text-muted">(<?= $q->question_type ?>)</small></div>
                                            <?php if (in_array(
                                                $q->question_type,
                                                ['single_choice', 'multi_choice', 'radio', 'checkbox']
                                            )) : ?>
                                                <div class="mt-1">
                                                    <button class="btn btn-sm btn-outline-secondary
btnAddOption" data-qid="<?= $q->question_id ?>">+ Option</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger btnDelQuestion" data-id="<?= $q->question_id ?>">Hapus</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Simple modals inline -->
        <div class="modal" tabindex="-1" id="modalSection">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></ button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" id="sectionTitle">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="sectionDesc"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</ button>
                            <button class="btn btn-primary" id="saveSection">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="modalQuestion">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></ button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="qSectionId">
                        <div class="mb-3">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" id="qLabel">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Tipe</label>
                                <select class="form-select" id="qType">
                                    <option value="short_text">Short Text</option>
                                    <option value="long_text">Long Text</option>
                                    <option value="single_choice">Single Choice</option>
                                    <option value="multi_choice">Multi Choice</option>
                                    <option value="email">Email</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="datetime">Datetime</option>
                                    <option value="url">URL</option>
                                    <option value="file">File</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Required?</label>
                                <select class="form-select" id="qRequired">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Placeholder</label>
                            <input type="text" class="form-control" id="qPlaceholder">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</ button>
                            <button class="btn btn-primary" id="saveQuestion">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="modalOption">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Opsi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></ button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="optQid">
                        <div class="mb-3">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" id="optLabel">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value</label>
                            <input type="text" class="form-control" id="optValue">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</ button>
                            <button class="btn btn-primary" id="saveOption">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>