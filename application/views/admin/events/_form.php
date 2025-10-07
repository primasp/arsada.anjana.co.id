<?php echo form_open($action, ['class' => 'needs-validation', 'novalidate' => true]); ?>
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Kode Event</label>
        <input type="text" name="event_code" class="form-control" required value="<?= set_value('event_code', $event->event_code ?? '') ?>">
    </div>
    <div class="col-md-9">
        <label class="form-label">Judul</label>
        <input type="text" name="title" class="form-control" required value="<?= set_value('title', $event->title ?? '') ?>">
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3"><?= set_value('description', $event->description ?? '') ?></textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Venue</label>
        <input type="text" name="venue" class="form-control" value="<?= set_value('venue', $event->venue ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Timezone</label>
        <input type="text" name="timezone" class="form-control" value="<?= set_value('timezone', $event->timezone ?? 'Asia/Jakarta') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="draft" <?= set_select('status', 'draft', isset($event) && $event->status == 'draft') ?>>draft</option>
            <option value="open" <?= set_select('status', 'open', isset($event) && $event->status == 'open') ?>>open</option>
            <option value="closed" <?= set_select('status', 'closed', isset($event) && $event->status == 'closed') ?>>closed</option>
            <option value="archived" <?= set_select('status', 'archived', isset($event) && $event->status == 'archived') ?>>archived</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Start</label>
        <input type="datetime-local" name="start_at" class="form-control" required value="<?= set_value('start_at', isset($event->start_at) ? date('Y-m-d\TH:i', strtotime($event->start_at)) : '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">End</label>
        <input type="datetime-local" name="end_at" class="form-control" required value="<?= set_value('end_at', isset($event->end_at) ? date('Y-m-d\TH:i', strtotime($event->end_at)) : '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Kuota</label>
        <input type="number" name="max_participants" class="form-control" value="<?= set_value('max_participants', $event->max_participants ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Publik?</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_public" value="1" <?= set_checkbox('is_public', '1', isset($event) ? (bool)$event->is_public : true) ?>>
            <label class="form-check-label">Ya</label>
        </div>
    </div>
</div>
<hr>
<div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Simpan</button>
    <a href="<?= site_url('admin/events') ?>" class="btn btn-secondary">Batal</a>
</div>
<?php echo form_close(); ?>