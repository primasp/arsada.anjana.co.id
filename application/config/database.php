<?php
defined('BASEPATH') or exit('No direct script access allowed');



$active_group = 'local';
$query_builder = TRUE;

$db['AWS'] = array(
	'dsn'	=> '',
	'hostname' => '16.78.218.162',
	// 'username' => 'primacare',
	// 'password' => 'Primacare@123',

	'username' => 'postgres',
	'password' => 'Postgres@123',
	'schema' => 'event_manager',
	'database' => 'arsadadb',
	'dbdriver' => 'postgre',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port' => 5432
);



$db['local'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'postgres',
	'schema' => 'event_manager',
	'password' => 'Postgres@123',
	'database' => 'arsadadb',
	'dbdriver' => 'postgre',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port' => 5432
);
