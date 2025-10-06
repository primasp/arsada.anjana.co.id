<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'ClientController';

$route['Client'] = 'ClientController';
$route['Login'] = 'AuthController';
$route['Logout'] = 'AuthController/logout';

$route['Dashboard-Admin'] = 'DashboardAdm_C/index';
$route['Transaksi-Pending'] = 'Transaksi_C/pendingTransaksi';
$route['Transaksi-All'] = 'Transaksi_C/allTransaksi';
$route['Transaksi-Add'] = 'Transaksi_C/addTransaksi';

$route['Auto-Schedule'] = 'ClientController/auto_renew';

$route['Forgot-Password'] = 'AuthController/forgot_password';
$route['Reset-Password/(:any)'] = 'AuthController/reset_password/$1';


$route['All-Room'] = 'Master_C/ms_room';
$route['Add-Room'] = 'Master_C/add_room';
$route['All-Property'] = 'Master_C/ms_property';
$route['Add-Property'] = 'Master_C/add_property';

// $route['default_controller'] = 'AuthController';






$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
| -------------------------------------------------------------------------
| Sample REST API Routes
| -------------------------------------------------------------------------
*/
$route['api/example/users/(:num)'] = 'api/example/users/id/$1'; // Example 4
$route['api/example/users/(:num)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'api/example/users/id/$1/format/$3$4'; // Example 8
