<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 */
			'hostname'   => 'localhost',
			'username'   => 'root',
			'password'   => 'r00tdb',
			'persistent' => FALSE,
		),
		'database'   => 'kohana',
		'charset'    => 'utf8',
		'caching'    => FALSE,
		'profiling'  => TRUE,
	),
	'alternate' => array(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn
			 * string   username
			 * string   password
			 * boolean  persistent
			 */
			'dsn'        => 'mysql://host=localhost',
			'username'   => 'root',
			'password'   => 'r00tdb',
			'persistent' => FALSE,
		),
		'database'   => 'kohana',
		'charset'    => 'utf8',
		'caching'    => FALSE,
		'profiling'  => TRUE,
	),
);