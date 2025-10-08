    <div class="modal" tabindex="-1" id="modalQuestion">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                <!-- <option value="single_choice">Single Choice</option> -->
                                <option value="single_choice">Single Choice (Radio)</option>
                                <!-- <option value="multi_choice">Multi Choice</option> -->
                                <option value="multi_choice">Multi Choice (Checkbox)</option>
                                <option value="select">Select (Dropdown)</option> <!-- 🔥 Tambahan -->
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
                                <option value="false">Tidak</option>
                                <option value="true">Ya</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Placeholder</label>
                        <input type="text" class="form-control" id="qPlaceholder">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" id="saveQuestion">Simpan</button>
                </div>
            </div>
        </div>
    </div>