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

	public function __construct(array $result, $sql)
	{
		parent::__construct($result, $sql);

		// Find the number of rows in the result
		$this->_total_rows = count($result);
	}

	public function __destruct()
	{
		// Cached results do not use resources
	}

	public function as_array($key = NULL, $value = NULL)
	{
		if ($key === NULL AND $value === NULL)
		{
			// Return the full result array
			return $this->_result;
		}

		$array = array();

		foreach ($this->_result as $row)
		{
			if ($value !== NULL)
			{
				// Return the result as a $key => $value list
				$array[$row[$key]] = $row[$value];
			}
			else
			{
				// Return the result as a $key => $row list
				$array[$row[$key]] = $row;
			}
		}

		return $array;
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
