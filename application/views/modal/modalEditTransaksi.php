<div class="modal fade" id="editTrxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editTrxForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_transaction_id">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Periode Sewa</label>
                            <select id="edit_rent_period" class="form-control">
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah Sewa</label>
                            <input type="number" id="edit_quantity" class="form-control" min="1" value="1">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Auto Renew</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="edit_auto_renew" value="1">
                                <label class="form-check-label" for="edit_auto_renew">
                                    Perpanjang Otomatis
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" id="edit_start_date" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir (otomatis)</label>
                            <input type="text" id="edit_end_date" class="form-control" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Pembayaran</label>
                            <select id="edit_verif_bayar" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="approve">Approve</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>