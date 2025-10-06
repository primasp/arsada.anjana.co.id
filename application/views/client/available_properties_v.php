<?php if (!empty($properties)) : ?>


    <h3 class="card-title mb-4 text-center">Alma Property</h3>
    <div class="row gy-4">



        <?php foreach ($properties as $property) : ?>

            <!-- Kartu Pertama -->
            <div class="col-md-6 col-sm-12 col-lg-6 col-xl-3">
                <div class="card h-100 border border-primary shadow-sm">
                    <?php if ($property->property_photo == "") { ?>
                        <a href="#" class="property-item" data-property-id="<?php echo $property->property_id; ?>" data-start-date="<?php echo $start_date; ?>" data-end-date="<?php echo $end_date; ?>" data-rent-period="<?php echo $rent_period; ?>" data-quantity="<?php echo $quantity; ?>">
                            <img src="<?= base_url(); ?>assets/img/property/default.png" class="card-img-top p-3" alt="Image">
                        </a>
                    <?php } else { ?>
                        <a href="#" class="property-item" data-property-id="<?php echo $property->property_id; ?>" data-start-date="<?php echo $start_date; ?>" data-end-date="<?php echo $end_date; ?>" data-rent-period="<?php echo $rent_period; ?>" data-quantity="<?php echo $quantity; ?>">
                            <img src="<?= base_url(); ?>assets/img/property/<?php echo $property->property_photo ?>" alt="Image">
                        </a>
                    <?php } ?>

                    <div class="card-body text-center">
                        <h5 class="card-title text-primary fw-bold"><?= $property->property_name ?></h5>
                        <p class="card-text text-muted">
                            <?= $property->address ?>
                        </p>
                        <a href="#" class="btn btn-outline-primary btn-sm property-item" data-property-name="<?php echo $property->property_name; ?>" data-property-id="<?php echo $property->property_id; ?>" data-start-date="<?php echo $start_date; ?>" data-end-date="<?php echo $end_date; ?>" data-rent-period="<?php echo $rent_period; ?>" data-quantity="<?php echo $quantity; ?>">Lihat kamar Tersedia</a>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>


































    <?php else : ?>
        <p>Tidak ada properti yang tersedia pada periode yang dipilih.</p>
    <?php endif; ?>