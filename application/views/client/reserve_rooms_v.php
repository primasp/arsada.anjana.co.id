<?php if (!empty($room)) : ?>


    <!-- <h3 class="card-title mb-4">Form Reservasi</h3> -->
    <!-- <div class="row gy-4"> -->



    <!-- <form id="formReservedRoom" action="<?php echo base_url('ClientController/save_rental_transaction'); ?>" method="post" class="p-3 bg-light border rounded" enctype="multipart/form-data"> -->
    <form id="formReservedRoom">



        <input type="hidden" name="room_id" id="room_id" value="<?php echo $room->room_id; ?>">
        <input type="hidden" name="property_id" id="property_id" value="<?php echo $room->property_id; ?>">
        <input type="hidden" name="quantity" id="quantity" value="<?= $quantity; ?>">




        <div class="card-box">
            <h3 class="card-title pb-4">Form Reservasi <b>Kamar : <?= $room->room_number ?></b></h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-img-wrap">
                        <img class="inline-block" src="<?= base_url(); ?>assets/img/room/<?php echo $room->room_photo ?>" alt="user">
                    </div>
                    <div class="profile-basic">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="full_name" name="full_name" class="form-control floating" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">NIK KTP<span class="text-danger">*</span></label>
                                    <input type="text" id="nik_ktp" name="nik_ktp" class="form-control floating" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Email<span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control floating" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Nomor Telepon<span class="text-danger">*</span></label>
                                    <input type="text" id="phone_number" name="phone_number" class="form-control floating" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Alamat<span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Periode Sewa</label>
                                    <input type="text" id="rent_period" name="rent_period" class="form-control floating" value="<?php echo $rent_period; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Tanggal Mulai Sewa</label>
                                    <input type="text" id="start_date" name="start_date" class="form-control floating" value="<?php echo $start_date; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Tanggal Akhir Sewa</label>
                                    <input type="text" id="end_date" name="end_date" class="form-control floating" value="<?php echo $end_date; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Total Bayar Sewa</label>
                                    <input type="text" id="total_rent" name="total_rent" class="form-control floating" value="Rp <?= number_format($totalSewa, 0, ',', '.'); ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Metode Pembayaran</label>
                                    <input type="text" id="payment_method" name="payment_method" class="form-control floating" value="Transfer Bank" readonly>
                                </div>
                            </div>


                            <!-- <div class="col-md-3">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Metode Pembayaran<span class="text-danger">*</span></label>
                                    <select id="payment_method" name="payment_method" class="form-control select2" required>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="cash">Tunai</option>
                                    </select>
                                </div>
                            </div> -->

                            <div class="col-md-6 ">
                                <div class="input-block local-top-form">
                                    <label class="local-top">Unggah Bukti Pembayaran <span class="login-danger">*</span></label>
                                    <!-- <input type="file" accept="image/*" name="proof_of_payment" id="proof_of_payment" onchange="loadFile(event, 'proof_of_payment_label')" class="form-control" required> -->
                                    <input type="file" id="proof_of_payment" name="proof_of_payment" class="form-control" accept="image/*,application/pdf" required onchange="validateFile(this)">
                                    <small class="form-text text-muted">Unggah file dalam format JPG, PNG, atau PDF.</small>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-block local-top-form">
                                    <label class="local-top">Unggah KTP <span class="login-danger">*</span></label>
                                    <!-- <input type="file" accept="image/*" name="upload_ktp" id="upload_ktp" onchange="loadFile(event, 'upload_ktp_label')" class="form-control" required> -->
                                    <input type="file" id="upload_ktp" name="upload_ktp" class="form-control" accept="image/*,application/pdf" required onchange="validateFile(this)">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Apakah Anda Membawa Pasangan?</label>
                                    <select id="is_bringing_partner" name="is_bringing_partner" class="form-control" onchange="toggleMarriageProof()" required>
                                        <option value="">Pilih</option>
                                        <option value="yes">Ya</option>
                                        <option value="no">Tidak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4" id="marriage_proof_section" style="display:none;">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Unggah Bukti Pernikahan <span class="login-danger">*</span></label>
                                    <!-- <input type="file" accept="image/*" name="marriage_proof" id="marriage_proof" class="form-control"> -->
                                    <input type="file" id="marriage_proof" name="marriage_proof" class="form-control" accept="image/*,application/pdf" required onchange="validateFile(this)">
                                </div>
                            </div>




                            <div class="col-md-4">
                                <div class="input-block local-forms">
                                    <label class="focus-label">Jumlah Orang yang Akan Menyewa<span style="color: red"> *</span></label>
                                    <input type="text" id="number_of_people" name="number_of_people" class="form-control" inputmode="numeric" pattern="[0-9]*" min="1" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Masukkan angka">
                                </div>
                            </div>




                            <div class="row pt-3">

                                <div class="col-md-3"></div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary w-100 mb-2" type="submit" id="btnProsesReserve">BOOKING</button>


                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>




<?php else : ?>
    <p>Tidak ada ruangan yang tersedia pada periode yang dipilih.</p>
<?php endif; ?>