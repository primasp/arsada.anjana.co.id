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

    <!-- <script src="<?= base_url(); ?>assets/plugins/moment/moment.min.js"></script> -->



</head>

<body>
    <div class="main-wrapper">
        <div class="header ">
            <div class="header-left w-auto">
                <a href="index.html" class="logo">
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


                <li class="nav-item ">
                    <p class="pt-2"><a href="<?= base_url(); ?>Login" class="hasnotifications nav-link fw-bold fs-5"><i class="fa-solid fa-right-to-bracket"></i> &nbsp; Login</a></p>

                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-end">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="profile.html">My Profile</a>
                    <a class="dropdown-item" href="edit-profile.html">Edit Profile</a>
                    <a class="dropdown-item" href="settings.html">Settings</a>
                    <a class="dropdown-item" href="login.html">Logout</a>
                </div>
            </div>
        </div>