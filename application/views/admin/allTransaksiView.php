<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('All-Room') ?>">Kamar </a></li>
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
                                    <h3>Master Kamar</h3>
                                    <div class="doctor-search-blk">
                                        <div class="top-nav-search table-search-blk">
                                            <form>
                                                <input type="text" class="form-control" placeholder="Search here">
                                                <a class="btn"><img src="assets/img/icons/search-normal.svg" alt=""></a>
                                            </form>
                                        </div>
                                        <div class="add-group">
                                            <a href="add-salary.html" class="btn btn-primary add-pluss ms-2"><img src="assets/img/icons/plus.svg" alt=""></a>
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
                    <div class="staff-search-table">
                        <form id="filterFormTransaksi">
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Property</label>
                                        <select id="property" name="property" class="form-control select2">
                                            <option value="">Pilih Property </option>
                                            <?php foreach ($properties as $property) : ?>
                                                <option value="<?php echo $property['property_id']; ?>"><?php echo $property['property_name']; ?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Status Penyewaan</label>
                                        <select id="status_sewa" name="status_sewa" class="form-control select2">
                                            <option value="">Pilih Status Sewa Kamar</option>
                                            <option value="Upcoming">Akan Datang</option>
                                            <option value="Berakhir Hari Ini">Berakhir Hari Ini</option>
                                            <option value="Ending Soon (<2 Days)">Berakhir Dalam 2 Hari</option>
                                            <option value="Ongoing">Sedang Disewakan</option>
                                            <option value="Lease Ended">Masa Sewa Berakhir</option>
                                            <option value="Auto Renew">Berulang Otomatis</option>
                                            <option value="Di Stop">Di Stop</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Status Bayar</label>
                                        <select id="bayar_status" name="bayar_status" class="form-control select2">
                                            <option value="">Pilih Status Bayar</option>
                                            <option value="pending">Pending</option>
                                            <option value="approve">Approve</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="doctor-submit">
                                        <button type="submit" class="btn btn-primary submit-list-form me-2">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="all-transaksi-table" class="table border-0 custom-table comman-table datatable mb-0">
                            <thead>
                                <tr>

                                    <th>ID Transaksi</th>
                                    <th>Property</th>
                                    <th>Kamar</th>
                                    <th>Nama Penyewa</th>
                                    <th>Periode Sewa</th>
                                    <th>Total Harga Sewa</th>
                                    <th>Status Pembayaran</th>

                                    <th>Status Sewa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>