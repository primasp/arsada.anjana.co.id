<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Pasien </a></li>

                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-sm-12">

            <div class="card card-table show-entire">
                <div class="card-body">

                    <!-- Table Header -->
                    <div class="page-table-header mb-2">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <h3>Daftar Transaksi</h3>
                                    <div class="doctor-search-blk">
                                        <div class="top-nav-search table-search-blk">
                                            <form>
                                                <input id="search-input" type="text" class="form-control" placeholder="Search here">
                                                <a class="btn"><img src="assets/img/icons/search-normal.svg" alt=""></a>
                                            </form>
                                        </div>
                                        <div class="add-group">
                                            <a href="<?= base_url('Transaksi-Add'); ?>" class="btn btn-primary add-pluss ms-2">
                                                <img src="assets/img/icons/plus.svg" alt="">
                                            </a>
                                            <a href="javascript:;" class="btn btn-primary doctor-refresh ms-2"><img src="assets/img/icons/re-fresh.svg" alt=""></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto text-end float-end ms-auto download-grp">
                                <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-01.svg" alt=""></a>
                                <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-02.svg" alt=""></a>
                                <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-03.svg" alt=""></a>
                                <a href="javascript:;"><img src="assets/img/icons/pdf-icon-04.svg" alt=""></a>

                            </div>
                        </div>
                    </div>
                    <!-- /Table Header -->
                    <button id="approve-selected" class="btn btn-warning m-3">Approve Selected</button>
                    <div class="table-responsive">

                        <table id="all-transaksi-table" class="table border-0 custom-table comman-table datatable mb-0 ">
                            <thead>
                                <tr class="text-center align-middle">
                                    <th>
                                        <div class="form-check check-tables">
                                            <input class="form-check-input" type="checkbox" value="something">


                                        </div>
                                    </th>
                                    <th>Nama Penyewa</th>
                                    <th>Property Name</th>
                                    <th>Room Number</th>
                                    <th>Periode booking</th>
                                    <th>Pembayaran</th> <!-- NEW -->
                                    <th>Status</th> <!-- NEW -->
                                    <!-- <th>Total Sewa</th> -->
                                    <th>Dokumen</th>

                                    <th class="sticky-col">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingTransaksi as $pendingTransaksi) : ?>
                                    <tr>
                                        <td>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input select-checkbox" type="checkbox" data-transaction-id="<?= $pendingTransaksi->transaction_id ?>" data-room-id="<?= $pendingTransaksi->room_id ?>">
                                            </div>
                                        </td>
                                        <td><?= $pendingTransaksi->full_name ?></td>
                                        <td><?= $pendingTransaksi->property_name ?></td>
                                        <td class="text-center"><?= $pendingTransaksi->room_number ?></td>
                                        <td>
                                            <?= strtoupper($pendingTransaksi->rent_period) . " : <b>" . $pendingTransaksi->total_days . " Hari </b>" ?>

                                            <?php if (!empty($pendingTransaksi->auto_renew) && $pendingTransaksi->auto_renew == 't') : ?>
                                                <span title="Perpanjangan Otomatis">
                                                    <i class="fas fa-sync-alt text-success ms-1"></i>
                                                </span>
                                            <?php endif; ?><br>
                                            <?= $pendingTransaksi->start_date_formatted . " - " . $pendingTransaksi->end_date_formatted ?>

                                        </td>
                                        <!-- NEW: Pembayaran -->
                                        <td class="text-center">
                                            <span class="<?= $pendingTransaksi->pay_badge ?>"><?= $pendingTransaksi->pay_label ?></span><br>
                                            <small class="text-muted"><?= $pendingTransaksi->pay_text ?></small>
                                        </td>

                                        <!-- NEW: Status -->
                                        <td class="text-center">
                                            <span class="<?= $pendingTransaksi->status_badge ?>"><?= $pendingTransaksi->status_text ?></span>
                                        </td>

                                        <!-- <td><?= "Rp " . number_format($pendingTransaksi->total_sewa, 0, ',', '.') ?></td> -->
                                        <!-- <td class="text-center">
                                            <h5 class="pb-1">
                                                <a href="<?= base_url('assets/img/upload/' . $pendingTransaksi->ktp_upload) ?>" class="lihat-ktp" onclick="window.open(this.href, '_blank', 'width=600,height=400'); return false;">
                                                    <span class="upload_ktp"> KTP</span>
                                                </a>
                                            </h5>
                                            <h5>
                                                <a href="<?= base_url('assets/img/upload/' . $pendingTransaksi->proof_of_payment) ?>" class="lihat-bukti" onclick="window.open(this.href, '_blank', 'width=600,height=400'); return false;">
                                                    <span class="upload_bukti">BUKTI TRANSFER</span>
                                                </a>
                                            </h5>
                                        </td> -->

                                        <td class="text-center">
                                            <h5 class="pb-1">
                                                <a href="<?= base_url('assets/img/upload/' . $pendingTransaksi->ktp_upload) ?>" class="lihat-ktp" onclick="window.open(this.href, '_blank', 'width=600,height=400'); return false;">
                                                    <span class="upload_ktp">KTP</span>
                                                </a>
                                            </h5>

                                            <?php if (!empty($pendingTransaksi->dp_proof_of_payment)) : ?>
                                                <h5 class="pb-1">
                                                    <a href="<?= base_url('assets/img/upload/' . $pendingTransaksi->dp_proof_of_payment) ?>" onclick="window.open(this.href, '_blank', 'width=600,height=400'); return false;">
                                                        <span>BUKTI DP</span>
                                                    </a>
                                                </h5>
                                            <?php endif; ?>

                                            <?php if (!empty($pendingTransaksi->proof_of_payment)) : ?>
                                                <h5>
                                                    <a href="<?= base_url('assets/img/upload/' . $pendingTransaksi->proof_of_payment) ?>" class="lihat-bukti" onclick="window.open(this.href, '_blank', 'width=600,height=400'); return false;">
                                                        <span class="upload_bukti">BUKTI PELUNASAN</span>
                                                    </a>
                                                </h5>
                                            <?php endif; ?>
                                        </td>




                                        <td class="text-center">
                                            <button class="btn btn-info" onclick="viewProperty('<?= $pendingTransaksi->transaction_id ?>')">Lihat Detail</button>

                                            <?php if ($pendingTransaksi->canApproveDP) : ?>
                                                <button class="btn btn-warning approve-row" data-transaction-id="<?= $pendingTransaksi->transaction_id ?>" data-room-id="<?= $pendingTransaksi->room_id ?>" data-stage="dp">Approve DP</button>
                                            <?php endif; ?>

                                            <?php if ($pendingTransaksi->canApproveFull) : ?>
                                                <button class="btn btn-success approve-row" data-transaction-id="<?= $pendingTransaksi->transaction_id ?>" data-room-id="<?= $pendingTransaksi->room_id ?>" data-stage="full">Approve Pelunasan</button>
                                            <?php endif; ?>

                                            <button class="btn btn-danger reject-row" data-transaction-id="<?= $pendingTransaksi->transaction_id ?>" data-room-id="<?= $pendingTransaksi->room_id ?>">
                                                Tolak
                                            </button>
                                        </td>


                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>