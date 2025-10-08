<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'AuthController';






$route['admin/events'] = 'Admin/EventController/index';
$route['admin/events/create'] = 'Admin/EventController/create';
$route['admin/events/store'] = 'Admin/EventController/store';
$route['admin/events/(:any)/edit'] = 'Admin/EventController/edit/$1';
$route['admin/events/(:any)/update'] = 'Admin/EventController/update/$1';

$route['admin/forms/section/store'] = 'Admin/FormBuilderController/store_section';
$route['admin/forms/question/store'] = 'Admin/FormBuilderController/store_question';
$route['admin/forms/section/(:any)/delete'] = 'Admin/FormBuilderController/delete_section/$1';
$route['admin/forms/question/(:any)/delete']  = 'admin/FormBuilderController/delete_question/$1';
$route['admin/forms/option/store'] = 'Admin/FormBuilderController/store_option';
$route['admin/forms/sort-items'] = 'Admin/FormBuilderController/sort_items';

$route['event'] = 'Public/EventPublicController/index';
$route['event/(:any)'] = 'Public/EventPublicController/detail/$1';
$route['event/daftar/(:any)'] = 'Public/EventPublicController/daftar/$1';
$route['event/(:any)/submit'] = 'Public/EventPublicController/submit/$1';



// $route['admin/events/edit'] = 'Admin/EventController/edit';

$route['admin/events/(:any)/builder'] = 'Admin/FormBuilderController/index/$1';



// $route['Client'] = 'ClientController';
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
