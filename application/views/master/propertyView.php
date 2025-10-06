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
                                    <h3>Daftar Property</h3>
                                    <div class="doctor-search-blk">
                                        <div class="top-nav-search table-search-blk">
                                            <form>
                                                <input id="search-input" type="text" class="form-control" placeholder="Search here">
                                                <a class="btn"><img src="assets/img/icons/search-normal.svg" alt=""></a>
                                            </form>
                                        </div>





                                        <div class="add-group">
                                            <!-- <a href="add-patient.html" class="btn btn-primary add-pluss ms-2"><img src="assets/img/icons/plus.svg" alt=""></a> -->

                                            <!-- <button type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#modalTambahProperty">
                                                <img src="assets/img/icons/plus.svg" alt="">
                                            </button> -->


                                            <a href="<?= base_url('Add-Property'); ?>" class="btn btn-primary add-pluss ms-2">
                                                <img src="assets/img/icons/plus.svg" alt="">
                                            </a>
                                            <a href="javascript:;" class="btn btn-primary doctor-refresh ms-2"><img src="assets/img/icons/re-fresh.svg" alt=""></a>
                                        </div>



                                        <!-- <button type="button" class="btn btn-secondary mt-1" data-bs-toggle="modal" data-bs-target="#centermodal">Center modal</button> -->


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

                    <div class="table-responsive">
                        <table id="all-transaksi-table" class="table border-0 custom-table comman-table datatable mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check check-tables">
                                            <input class="form-check-input" type="checkbox" value="something">
                                        </div>
                                    </th>
                                    <th>NAMA PROPERTY</th>
                                    <th>JENIS PROPERTY</th>
                                    <th>PEMILIK PROPERTY</th>
                                    <th>ALAMAT</th>
                                    <th>TERSEDIA</th>

                                    <!-- <th class="sticky-col">ACTIONS</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($properties as $property) : ?>
                                    <tr>
                                        <td>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input" type="checkbox" value="something">
                                            </div>
                                        </td>
                                        <td><?= $property['property_name'] ?></td>
                                        <td><?= $property['property_type'] ?></td>
                                        <td><?= $property['owner_name'] ?></td>
                                        <td><?= $property['city'] . ", " . $property['address'] ?></td>
                                        <td><?= $property['tersedia'], " / " . $property['total_rooms'] ?></td>
                                        <!-- <td>

                                            <div class="chat-search-list">
                                                <ul>
                                                  
                                                    <li title="Buat Registrasi"><a href="javascript:;"><i class="fa-regular fa-address-card"></i></li>
                                    
                                                    <li title="Riwayat Rekam Medis" data-id="<?= $property['property_id'] ?>" class="send-data-btn"><a href="javascript:;"> <i class="fa-solid fa-notes-medical"></i></li>
                                                    <div class="dropdown dropdown-action">
                                                        <li><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> <i class="fa-solid fa-bars "></i></a>

                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="edit-patient.html"><i class="fa-solid fa-circle-info m-r-5"></i>Detail Pasien</a>
                                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_patient"><i class="fa-solid fa-user-pen m-r-5"></i> Edit Pasien</a>
                                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_patient"><i class="fa-solid fa-user-slash m-r-5"></i>Nonaktifkan</a>
                                                            </div>
                                                        </li>
                                                    </div>


                                                </ul>
                                            </div>
                                        </td> -->

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>