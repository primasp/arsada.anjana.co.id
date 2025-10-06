<?php if (!empty($rooms)) : ?>


    <h3 class="card-title mb-4"><?= $property_name; ?> </h3>
    <div class="row gy-4">



        <?php foreach ($rooms as $rooms) : ?>

            <!-- Kartu Pertama -->
            <div class="col-md-6 col-sm-12 col-lg-6 col-xl-3">
                <div class="card h-100 border border-primary shadow-sm">
                    <?php if ($rooms->room_photo == "") { ?>
                        <a href="#" class="reserve-item" data-room-id="<?php echo $room->room_id; ?>" data-start-date="<?php echo $start_date; ?>" data-end-date="<?php echo $end_date; ?>" data-rent-period="<?php echo $rent_period; ?>" data-quantity="<?php echo $quantity; ?>">
                            <img src="<?= base_url(); ?>assets/img/room/default_room.png" class="card-img-top p-3" alt="Image">
                        </a>
                    <?php } else { ?>
                        <a href="#" class="reserve-item text-center" data-room-id="<?php echo $rooms->room_id; ?>" data-start-date="<?php echo $start_date; ?>" data-end-date="<?php echo $end_date; ?>" data-rent-period="<?php echo $rent_period; ?>" data-quantity="<?php echo $quantity; ?>">
                            <img src="<?= base_url(); ?>assets/img/room/<?php echo $rooms->room_photo ?>" class="w-75" alt="Image">
                        </a>
                    <?php } ?>

                    <div class="card-body text-center">
                        <h5 class="card-title text-primary fw-bold"><?= $rooms->room_number ?></h5>
                        <p class="card-text text-muted">
                            <?= $rooms->facilities ?>
                        </p>
                        <a href="#" class="btn btn-outline-primary btn-sm reserve-item" data-room-id="<?php echo $rooms->room_id; ?>" data-start-date="<?php echo $start_date; ?>" data-end-date="<?php echo $end_date; ?>" data-rent-period="<?php echo $rent_period; ?>" data-quantity="<?php echo $quantity; ?>">Booking Room</a>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>


    <?php else : ?>
        <p>Tidak ada ruangan yang tersedia pada periode yang dipilih.</p>
    <?php endif; ?>