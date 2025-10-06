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
                        <form id="filterFormRoom">
                            <div class="row">
                                <!-- <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Employee Name </label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div> -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Pemilik Properti</label>
                                        <select id="owner_id" name="owner_id" class="form-control select">
                                            <option value="">Pilih Pemilik</option>
                                            <?php foreach ($owners as $owner) : ?>
                                                <option value="<?php echo $owner['owner_id']; ?>"><?php echo $owner['owner_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Properti</label>
                                        <select id="property_id" name="property_id" class="form-control select">
                                            <option value="">Pilih Properti</option>>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="input-block local-forms">
                                        <label>Status Kamar</label>
                                        <select id="room_status" name="room_status" class="form-control select">
                                            <option value="">Pilih Status kamar</option>
                                            <option value="00">Dalam perbaikan</option>
                                            <option value="01">Tersedia</option>
                                            <option value="02">Butuh Konfirmasi</option>
                                            <option value="03">Terisi</option>
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
                        <table class="table border-0 custom-table comman-table datatable mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check check-tables">
                                            <input class="form-check-input" type="checkbox" value="something">
                                        </div>
                                    </th>
                                    <th>Pemilik Property</th>
                                    <th>Nama Property</th>
                                    <th>Nomor Kamar</th>
                                    <th>Tipe Kamar</th>
                                    <th>Tarif Kamar</th>
                                    <th>Status Kamar</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr>
                                    <td>
                                        <div class="form-check check-tables">
                                            <input class="form-check-input" type="checkbox" value="something">
                                        </div>
                                    </td>
                                    <td>Alamas</td>
                                    <td class="profile-image"><a href="profile.html"><img width="28" height="28" src="assets/img/profiles/avatar-01.jpg" class="rounded-circle m-r-5" alt=""> Ulinha Property</a></td>
                                    <td>Kamar 1</td>
                                    <td>Tipe A</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="salary-view.html">Rp. 100.000</a>
                                    </td>
                                    <td>
                                        <div class="dropdown action-label">
                                            <a class="custom-badge status-purple dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                Nurse
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end status-staff">
                                                <a class="dropdown-item" href="javascript:;">Nurse</a>
                                                <a class="dropdown-item" href="javascript:;">Accountant</a>
                                                <a class="dropdown-item" href="javascript:;">Pharmacist</a>
                                            </div>
                                        </div>
                                    </td>


                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="edit-salary.html"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_patient"><i class="fa fa-trash-alt m-r-5"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>