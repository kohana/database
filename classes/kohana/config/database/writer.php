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

		$query = DB::query(Database::INSERT, 'REPLACE INTO `'.$this->_table_name.'`(`group_name`, `config_key`, `config_value`) VALUES(:group_name, :config_key, :config_value)')
			->parameters(array(
				':group_name'   => $group,
				':config_key'   => $key,
				':config_value' => $config
			))
			->execute($this->_db_instance);

		return TRUE;
	}
}
