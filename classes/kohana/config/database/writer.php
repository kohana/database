<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database writer for the config system
 *
 * @package    Kohana
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Config_Database_Writer extends Config_Database_Reader implements Config_Writer
{
	/**
	 * Writes the passed config for $group
	 *
	 * Returns chainable instance on success or throws 
	 * Kohana_Config_Exception on failure
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The configuration to write
	 * @return boolean
	 */
	public function write($group, $key, $config)
	{
		$config = serialize($config);

		$query = DB::update($this->_table_name)
			->value('config_value', $config)
			->where('group_name', '=', $group)
			->where('config_key', '=', $key)
			->execute($this->_db_instance);

		// If this config option DNX
		if ($query === 0)
		{
			DB::insert($this->_table_name, array('group_name', 'config_key', 'config_value'))
				->values(array($group, $key, $config))
				->execute($this->_db_instance);
		}

		return TRUE;
	}
}
