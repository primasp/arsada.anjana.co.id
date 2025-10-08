<?php
$roleMap  = ['RU0001' => 'Admin', 'RU0002' => 'Karyawan', 'RU0003' => 'User'];
$roleName = $roleMap[$this->session->userdata('role_id_ap')] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>assets/img/favicon.png">
    <title>Arsada 2025 - Client View</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/fontawesome/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/select2.min.css">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap-datetimepicker.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/datatables/datatables.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/datatables/datatables.min.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/feather.css">

    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css">

    <!-- <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/property.css?v=<?= time() ?>"> -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- Dynamic CSS from Controller -->
    <?php if (isset($css)) : ?>

        <?php foreach ((array)$css as $file) : ?>
            <link rel="stylesheet" type="text/css" href="<?= base_url('assets/' . $file . '?v=' . time()); ?>">
        <?php endforeach; ?>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- <script src="<?= base_url(); ?>assets/plugins/moment/moment.min.js"></script> -->
    <link rel="stylesheet" href="<?= base_url('assets/css/dataTables.bootstrap4.min.css') ?>">


</head>

<body>
    <div class="main-wrapper">
        <div class="header ">
            <div class="header-left w-auto">
                <a href="#" class="logo">
                    <img src="<?= base_url(); ?>assets/img/logo.png" width="35" height="35" alt=""> <span>Arsada 2025</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><img src="<?= base_url(); ?>assets/img/icons/bar-icon.svg" alt=""></a>
            <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img src="<?= base_url(); ?>assets/img/icons/bar-icon.svg" alt=""></a>
            <div class="top-nav-search mob-view">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <a class="btn"><img src="<?= base_url(); ?>assets/img/icons/search-normal.svg" alt=""></a>
                </form>
            </div>
            <ul class="nav user-menu float-end">


                <li class="nav-item dropdown has-arrow user-profile-list">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                        <div class="user-names">
                            <h5><?= $this->session->userdata('username_ap') ?></h5>
                            <span><?= $roleName ?></span>
                        </div>
                        <span class="user-img">
                            <img src="<?= base_url(); ?>assets/img/user-06.jpg" alt="Admin">
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">Edit Profile</a>
                        <a class="dropdown-item" href="#">Settings</a>
                        <a class="dropdown-item" href=" <?php echo site_url('Logout'); ?>">Logout</a>
                    </div>
                </li>

            </ul>

        </div>