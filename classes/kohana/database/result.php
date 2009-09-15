<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database result wrapper.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Kohana_Database_Result implements Countable, Iterator, SeekableIterator, ArrayAccess {

	// Executed SQL for this result
	protected $_query;

	// Raw result resource
	protected $_result;

	// Total number of rows and current row
	protected $_total_rows  = 0;
	protected $_current_row = 0;

	// Return rows as an object or associative array
	protected $_as_object;

	/**
	 * Sets the total number of rows and stores the result locally.
	 *
	 * @param   mixed   query result
	 * @param   string  SQL query
	 * @return  void
	 */
	public function __construct($result, $sql, $as_object)
	{
		// Store the result locally
		$this->_result = $result;

		// Store the SQL locally
		$this->_query = $sql;

		// Results as objects or associative arrays
		$this->_as_object = $as_object;
	}

	/**
	 * Result destruction cleans up all open result sets.
	 */
	abstract public function __destruct();

	/**
	 * Return all of the rows in the result as an array.
	 *
	 * @param   string  column for an associative keys
	 * @param   string  column for an associative values
	 * @return  array
	 */
	public function as_array($key = NULL, $value = NULL)
	{
		// Go back to beginning of result set
		$this->rewind();

		$results = array();

		foreach ($this as $row)
		{
			if ($key !== NULL)
			{		
				if ($value !== NULL)
				{
					// $key => $value list
					if ($this->_as_object)
					{
						$results[$row->$key] = $row->$value;
					}
					else
					{
						$results[$row[$key]] = $row[$value];
					}
				}
				else
				{
					// $key => $row list 
					if ($this->_as_object)
					{
						$results[$row->$key] = $row;
					}
					else
					{
						$results[$row[$key]] = $row;
					}	
				}
			}
			else
			{
				// Add each row to the array
				$results[] = $row;
			}
		}

		return $results;
	}

	/**
	 * Return the named column from the current row.
	 *
	 * @param   string  column to get
	 * @param   mixed   default value if the column does not exist
	 * @return  mixed
	 */
	public function get($name, $default = NULL)
	{
		$row = $this->current();
		
		if ($this->_as_object AND isset($row->$name))
		{
			return $row->$name;
		}
		elseif ( ! $this->_as_object AND isset($row[$name]))
		{
			return $row[$name];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Countable: count
	 */
	public function count()
	{
		return $this->_total_rows;
	}

	/**
	 * ArrayAccess: offsetExists
	 */
	public function offsetExists($offset)
	{
		if ($this->_total_rows > 0)
		{
			$min = 0;
			$max = $this->_total_rows - 1;

			return ! ($offset < $min OR $offset > $max);
		}

		return FALSE;
	}
	
	/**
	 * ArrayAccess: offsetGet
	 */
	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return NULL;
			
		return $this->current();
	}

	/**
	 * ArrayAccess: offsetSet
	 *
	 * @throws  Kohana_Database_Exception
	 */
	final public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('Database results are read-only');
	}

	/**
	 * ArrayAccess: offsetUnset
	 *
	 * @throws  Kohana_Database_Exception
	 */
	final public function offsetUnset($offset)
	{
		throw new Kohana_Exception('Database results are read-only');
	}

	/**
	 * Iterator: key
	 */
	public function key()
	{
		return $this->_current_row;
	}

	/**
	 * Iterator: next
	 */
	public function next()
	{
		++$this->_current_row;
		return $this;
	}

	/**
	 * Iterator: prev
	 */
	public function prev()
	{
		--$this->_current_row;
		return $this;
	}

	/**
	 * Iterator: rewind
	 */
	public function rewind()
	{
		$this->_current_row = 0;
		return $this;
	}

	/**
	 * Iterator: valid
	 */
	public function valid()
	{
		return $this->offsetExists($this->_current_row);
	}

} // End Database_Result
