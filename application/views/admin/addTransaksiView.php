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


    <form id="transaksiForm">
        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="row">
                        <div class="col-6">
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="row pt-2">
                                        <div class="report-head">
                                            <h4> <i class="fa fa-edit fa-2x" style="color: #4B0082;" data-bs-toggle="tooltip"></i> &nbsp;Transaksi</h4>
                                        </div>
                                    </div>
                                    <hr class="pb-4" style="border-top: 2px solid #6a0dad; opacity: 1;">
                                    <div class="row ps-4">
                                        <!-- <div class="col-md-5 pt-2">
                                            <div class="input-block local-forms cal-icon">
                                                <label class="focus-label fw-bold">Tgl. Berobat<span class="text-danger">*</span></label>
                                                <input type="text" id="tgl_berobat" name="tgl_berobat" class="form-control floating" value="<?= date('d/m/Y') ?>" min="<?= date('d/m/Y') ?>" readonly required>
                                            </div>
                                        </div> -->


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
                                                <!-- <input type="number" class="form-control numeric floating" id="quantity" name="quantity" placeholder="" required> -->
                                                <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row  ps-4">
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



                                    <div class="row  ps-4">
                                        <div class="col-md-6">
                                            <div class="input-block local-forms text-center">
                                                <label class="my-0  fw-bold">Pilih Property </label>
                                                <select name="property" id="property" class="form-control select2 ">
                                                    <option value="">-- Pilih Property --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block local-forms text-center">
                                                <label class="my-0  fw-bold">Pilih Kamar</label>
                                                <select name="kamar" id="kamar" class="form-control select2 ">
                                                    <option value="">-- Pilih Kamar --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row ps-4 pb-4">
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="auto_renew" name="auto_renew">
                                                <label class="form-check-label fw-bold" for="auto_renew">
                                                    Lanjut Otomatis Setelah Masa Sewa Berakhir
                                                </label>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="row ps-4 pt-3">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6">
                                            <div class="input-block local-forms text-center">
                                                <label class="my-0 fw-bold">Total Harga</label>
                                                <div class="input-group">
                                                    <input type="text" id="total_price" name="total_price" class="form-control" placeholder="Total Harga" readonly>
                                                    <button type="button" class=" btn btn-warning btnEditPrice" class="btn btn-warning">Ubah Harga</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row ps-4 pt-2">
                                        <div class="col-md-12">
                                            <label class="fw-bold d-block mb-2">Metode Pembayaran</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_mode" id="pay_full" value="FULL" checked>
                                                <label class="form-check-label" for="pay_full">Lunas (Full)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_mode" id="pay_dp" value="DP">
                                                <label class="form-check-label" for="pay_dp">DP (Down Payment)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="dp_section" class="row ps-4 pt-5  d-none">
                                        <div class="col-md-4">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">DP (Nominal)</label>
                                                <input type="text" class="form-control" id="dp_amount" name="dp_amount" placeholder="cth: 1.500.000">
                                                <small class="text-muted">Jika isi nominal, persentase akan diabaikan.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">DP (%)</label>
                                                <input type="number" min="0" max="100" class="form-control" id="dp_percent" name="dp_percent" placeholder="cth: 30">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Jatuh Tempo DP</label>
                                                <input type="date" class="form-control" id="dp_due_date" name="dp_due_date">
                                            </div>
                                        </div>
                                        <div class="col-md-12 pt-2">
                                            <div class="input-block local-top-form">
                                                <label class="local-top">Unggah Bukti DP</label>
                                                <input type="file" accept="image/*,application/pdf" name="dp_proof_of_payment" id="dp_proof_of_payment" class="form-control pt-3">
                                            </div>
                                        </div>
                                        <div class="col-md-6 pt-2">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Sisa yang Harus Dibayar</label>
                                                <input type="text" class="form-control" id="remaining_amount" name="remaining_amount" readonly>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body pt-0">
                                <div class="settings-form">
                                    <div class="row pt-2">
                                        <div class="report-head">
                                            <h4> <i class="fa fa-user-md fa-2x" style="color: #4B0082;" data-bs-toggle="tooltip"></i> &nbsp;Data Penyewa</h4>
                                        </div>
                                    </div>
                                    <hr class="pb-4" style="border-top: 2px solid #6a0dad; opacity: 1;">
                                    <div class="row ps-4">
                                        <div class="col-md-7">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" id="full_name" name="full_name" class="form-control floating" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">NIK KTP</label>
                                                <input type="text" id="nik_ktp" name="nik_ktp" class="form-control floating">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ps-4">
                                        <div class="col-md-7">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Email</label>
                                                <input type="email" id="email" name="email" class="form-control floating">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Nomor Telepon</label>
                                                <input type="text" id="phone_number" name="phone_number" class="form-control floating">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ps-4">
                                        <div class="col-md-12">
                                            <div class="input-block local-forms">
                                                <label class="focus-label">Alamat</label>
                                                <textarea class="form-control" id="address" name="address"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ps-4">


                                        <div class="col-md-6 ">
                                            <div class="input-block local-top-form">
                                                <label class="local-top">Unggah Bukti Pembayaran </label>
                                                <input type="file" accept="image/*" name="proof_of_payment" id="proof_of_payment" onchange="loadFile(event, 'proof_of_payment_label')" class="form-control pt-3">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-block local-top-form">
                                                <label class="local-top">Unggah Bukti KTP </label>
                                                <input type="file" accept="image/*" name="upload_ktp" id="upload_ktp" onchange="loadFile(event, 'upload_ktp_label')" class="form-control pt-3">
                                            </div>
                                        </div>

                                        <input type="hidden" name="createby" id="createby" value="<?= $this->session->userdata('username_ap'); ?>">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-2">
                <div class="card sticky-sidebar">
                    <div class="card-body">
                        <button type="submit" id="btnProsesReserve" class="btn btn-primary w-100 mb-2">Simpan</button>
                        <button id="btnCancelPasien" class="btn btn-secondary w-100">Batal</button>


                    </div>
                </div>

            </div>
        </div>
    </form>