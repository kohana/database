<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'database' => array(
		/**
		 * Database settings for session storage.
		 *
		 * string   group  configuation group name
		 * string   table  session table name
		 * integer  gc     number of requests before gc is invoked
		 */
		'group' => 'default',
		'table' => 'sessions',
		'gc'    => 500,
	),
);
