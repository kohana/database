<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query builder for JOIN statements.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_Query_Builder_Join extends Database_Query_Builder {

	// Type of JOIN
	protected $_type;

	// JOIN ...
	protected $_table;

	// ON ...
	protected $_on = array();

	/**
	 * Creates a new JOIN statement for a table. Optionally, the type of JOIN
	 * can be specified as the second parameter.
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   string  type of JOIN: INNER, RIGHT, LEFT, etc
	 * @return  void
	 */
	public function __construct($table, $type = NULL)
	{
		// Set the table to JOIN on
		$this->_table = $table;

		if ($type !== NULL)
		{
			// Set the JOIN type
			$this->_type = (string) $type;
		}
	}

	/**
	 * Adds a new condition for joining.
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   string  logic operator
	 * @param   mixed   column name or array($column, $alias) or object
	 * @return  $this
	 */
	public function on($c1, $op, $c2)
	{
		$this->_on[] = array($c1, $op, $c2);

		return $this;
	}

	/**
	 * Compile the SQL partial for a JOIN statement and return it.
	 *
	 * @param   object  Database instance
	 * @return  string
	 */
	public function compile(Database $db)
	{
		if ($this->_type)
		{
			$sql = strtoupper($this->_type).' JOIN';
		}
		else
		{
			$sql = 'JOIN';
		}

		// Quote the table name that is being joined
		$sql .= ' '.$db->quote_table($this->_table).' ON ';

		$conditions = array();
		foreach ($this->_on as $condition)
		{
			// Split the condition
			list($c1, $op, $c2) = $condition;

			// Quote each of the identifiers used for the condition
			$conditions[] = $db->quote_identifier($c1).' '.strtoupper($op).' '.$db->quote_identifier($c2);
		}

		// Concat the conditions "... AND ..."
		$conditions = implode(' AND ', $conditions);

		if (count($this->_on) > 1)
		{
			// Wrap the conditions in a group. Some databases (Postgre) will fail
			// when singular conditions are grouped like this.
			$sql .= '('.$conditions.')';
		}
		else
		{
			$sql .= $conditions;
		}

		return $sql;
	}

	public function reset()
	{
		$this->_type =
		$this->_table = NULL;

		$this->_on = array();
	}

} // End Database_Query_Builder_Join
