<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cached database result.
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_Result_Cached extends Database_Result {

	public function __construct(array $result, $sql, $as_object)
	{
		if (count($result) > 0)
		{
			// Determine if we stored as objects or associative arrays	
			$as_object = get_class($result[0]);
		}

		parent::__construct($result, $sql, $as_object);

		// Find the number of rows in the result
		$this->_total_rows = count($result);
	}

	public function __destruct()
	{
		// Cached results do not use resources
	}

	public function seek($offset)
	{
		if ($this->offsetExists($offset))
		{
			$this->_current_row = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return FALSE;

		// Return an array of the row
		return $this->_result[$this->_current_row];
	}

} // End Database_Result_Cached
