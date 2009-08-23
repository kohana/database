<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database query builder for DELETE statements.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_Query_Builder_Delete extends Database_Query_Builder_Where {

	// DELETE FROM ...
	protected $_table;

	/**
	 * Set the table for a delete.
	 *
	 * @param   mixed  table name or array($table, $alias) or object
	 * @return  void
	 */
	public function __construct($table)
	{
		// Set the inital table name
		$this->_table = $table;

		// Start the query with no SQL
		return parent::__construct(Database::DELETE, '');
	}

	/**
	 * Sets the table to delete from.
	 *
	 * @param   mixed  table name or array($table, $alias) or object
	 * @return  $this
	 */
	public function table($table)
	{
		$this->_table = $table;

		return $this;
	}

	/**
	 * Compile the SQL query and return it.
	 *
	 * @param   object  Database instance
	 * @return  string
	 */
	public function compile(Database $db)
	{
		// Start a deletion query
		$query = 'DELETE FROM '.$db->quote_table($this->_table);

		if ( ! empty($this->_where))
		{
			// Add deletion conditions
			$query .= ' WHERE '.Database_Query_Builder::compile_conditions($db, $this->_where);
		}

		return $query;
	}

	public function reset()
	{
		$this->_table = NULL;
		$this->_where = array();

		return $this;
	}

} // End Database_Query_Builder_Delete
