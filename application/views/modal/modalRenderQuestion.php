  <div class="modal fade" id="modalRender" tabindex="-1">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title"><i class="feather-eye me-2"></i>Preview Form — <?= html_escape($form->name) ?></h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <?php foreach ($sections as $s) : ?>
                      <div class="card mb-4 shadow-sm">
                          <div class="card-header bg-light fw-bold"><?= html_escape($s->title) ?></div>
                          <div class="card-body">
                              <?php if (!empty($questions[$s->section_id])) : ?>
                                  <?php foreach ($questions[$s->section_id] as $q) : ?>
                                      <div class="mb-3">
                                          <label class="form-label fw-semibold">
                                              <?= html_escape($q->label) ?>
                                              <?php if (!empty($q->is_required) && $q->is_required == "t") : ?>
                                                  <span class="text-danger">*</span>
                                              <?php endif; ?>
                                          </label>

                                          <?php
                                            $type = $q->question_type;
                                            $name = "q_" . $q->question_id;
                                            ?>

                                          <?php if ($type === 'short_text') : ?>
                                              <input type="text" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>" <?= $q->is_required == "t" ? "required" : "" ?>>

                                          <?php elseif ($type === 'long_text') : ?>
                                              <textarea class="form-control" rows="3" placeholder="<?= html_escape($q->placeholder) ?>" <?= $q->is_required == "t" ? "required" : "" ?>></textarea>

                                          <?php elseif ($type === 'number') : ?>
                                              <input type="number" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>">

                                          <?php elseif ($type === 'email') : ?>
                                              <input type="email" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>">

                                          <?php elseif ($type === 'date') : ?>
                                              <input type="date" class="form-control">

                                          <?php elseif ($type === 'datetime') : ?>
                                              <input type="datetime-local" class="form-control">

                                          <?php elseif ($type === 'file') : ?>
                                              <input type="file" class="form-control">

                                          <?php elseif ($type === 'url') : ?>
                                              <input type="url" class="form-control" placeholder="<?= html_escape($q->placeholder) ?>">

                                          <?php elseif ($type === 'single_choice' || $type === 'radio') : ?>
                                              <?php if (!empty($q->options)) : ?>
                                                  <?php foreach ($q->options as $opt) : ?>
                                                      <div class="form-check">
                                                          <input class="form-check-input" type="radio" name="<?= $name ?>" id="opt_<?= $opt->option_id ?>" value="<?= html_escape($opt->option_value) ?>">
                                                          <label class="form-check-label" for="opt_<?= $opt->option_id ?>">
                                                              <?= html_escape($opt->option_label) ?>
                                                          </label>
                                                      </div>
                                                  <?php endforeach; ?>
                                              <?php endif; ?>

                                          <?php elseif ($type === 'multi_choice' || $type === 'checkbox') : ?>
                                              <?php if (!empty($q->options)) : ?>
                                                  <?php foreach ($q->options as $opt) : ?>
                                                      <div class="form-check">
                                                          <input class="form-check-input" type="checkbox" name="<?= $name ?>[]" id="opt_<?= $opt->option_id ?>" value="<?= html_escape($opt->option_value) ?>">
                                                          <label class="form-check-label" for="opt_<?= $opt->option_id ?>">
                                                              <?= html_escape($opt->option_label) ?>
                                                          </label>
                                                      </div>
                                                  <?php endforeach; ?>
                                              <?php endif; ?>

                                          <?php elseif ($type === 'select') : ?>
                                              <select class="form-select" name="<?= $name ?>">
                                                  <?php if (!empty($q->options)) : ?>
                                                      <?php foreach ($q->options as $opt) : ?>
                                                          <option value="<?= html_escape($opt->option_value) ?>">
                                                              <?= html_escape($opt->option_label) ?>
                                                          </option>
                                                      <?php endforeach; ?>
                                                  <?php else : ?>
                                                      <option>- Tidak ada opsi -</option>
                                                  <?php endif; ?>
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
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      <i class="feather-x me-1"></i> Tutup
                  </button>
              </div>
          </div>
      </div>
  </div>