<div class="content p-3">

    <!-- Page Header -->
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
    <!-- /Page Header -->

    <div class="good-morning-blk mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="morning-user">
                    <!-- <h2>Good Morning, <span>Client</span></h2> -->
                    <h4 style="margin-bottom: 5px; font-size: 1.5rem;"><?= $greeting; ?>, <span> Client</span></h4>
                    <!-- <h2>Good Morning, <span><?= $user['full_name'] ?></span></h2> -->
                    <p>Have a nice day</p>
                </div>
            </div>
            <div class="col-md-6 position-blk">
                <div class="morning-img">
                    <img src="<?= base_url(); ?>assets/img/morning-img-01.png" alt="" class="img-fluid  h-100">
                </div>
            </div>
        </div>
    </div>


    <div class="row">

        <div class="col-sm-4">
            <form id="cariPropertyForm">
                <div class="card sticky-sidebar">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-block local-forms">
                                    <label class="my-0">Periode Sewa : <span style="color: red"> *</span></label>
                                    <select name="rent_period" id="rent_period" class="form-small js-example-basic-single select2" required>
                                        <option class="text-center" value="">-- Pilih Periode Sewa --</option>
                                        <option value="harian">Harian</option>
                                        <option value="mingguan">Mingguan</option>
                                        <option value="bulanan">Bulanan</option>
                                    </select>
                                </div>
                            </div>





                            <div class="col-md-4">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Jumlah Sewa<span style="color: red"> *</span></label>
                                    <input type="text" name="quantity" id="quantity" class="form-control" inputmode="numeric" pattern="[0-9]*" min="1" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Masukkan angka">
                                </div>
                            </div>




                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Tanggal Mulai Sewa<span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control floating" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Tanggal Akhir Sewa</label>

                                    <input type="text" name="end_date" id="end_date" class="form-control" disabled>
                                </div>
                            </div>
                        </div>



                        <button id="btnSubmit" type="submit" class="btn btn-primary w-100 mb-2">
                            <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="btnText">Cari Property Tersedia</span>
                        </button>


                        <button id="btnCancelPasien" class="btn btn-secondary w-100">Batal</button>

                    </div>
                </div>
            </form>
        </div>


        <div class="col-sm-8">



            <div class="card-box" id="available-properties">

                <div id="loader" class="d-none text-center my-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>


            <div id="available-rooms" style="display: none;">
                <div id="loader" class="d-none text-center my-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>


            <div id="reserve-rooms" style="display: none;">
                <div id="loader" class="d-none text-center my-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

























        </div>
    </div>

</div>