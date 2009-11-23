<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database connection.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_MySQL extends Database {

	/**
	 * @var array   MySQL types
	 */
	protected static $_types = array
	(
		'bool'                      => array('type' => 'bool'),
		'bigint unsigned'           => array('type' => 'int', 'min' => '0', 'max' => '18446744073709551615'),
		'datetime'                  => array('type' => 'string'),
		'decimal unsigned'          => array('type' => 'float', 'exact' => TRUE, 'min' => '0'),
		'double'                    => array('type' => 'float'),
		'double precision unsigned' => array('type' => 'float', 'min' => '0'),
		'double unsigned'           => array('type' => 'float', 'min' => '0'),
		'enum'                      => array('type' => 'string'),
		'fixed'                     => array('type' => 'float', 'exact' => TRUE),
		'fixed unsigned'            => array('type' => 'float', 'exact' => TRUE, 'min' => '0'),
		'float unsigned'            => array('type' => 'float', 'min' => '0'),
		'int unsigned'              => array('type' => 'int', 'min' => '0', 'max' => '4294967295'),
		'integer unsigned'          => array('type' => 'int', 'min' => '0', 'max' => '4294967295'),
		'longblob'                  => array('type' => 'string', 'binary' => TRUE),
		'longtext'                  => array('type' => 'string'),
		'mediumblob'                => array('type' => 'string', 'binary' => TRUE),
		'mediumint'                 => array('type' => 'int', 'min' => '-8388608', 'max' => '8388607'),
		'mediumint unsigned'        => array('type' => 'int', 'min' => '0', 'max' => '16777215'),
		'mediumtext'                => array('type' => 'string'),
		'national varchar'          => array('type' => 'string'),
		'numeric unsigned'          => array('type' => 'float', 'exact' => TRUE, 'min' => '0'),
		'nvarchar'                  => array('type' => 'string'),
		'real unsigned'             => array('type' => 'float', 'min' => '0'),
		'set'                       => array('type' => 'string'),
		'smallint unsigned'         => array('type' => 'int', 'min' => '0', 'max' => '65535'),
		'text'                      => array('type' => 'string'),
		'tinyblob'                  => array('type' => 'string', 'binary' => TRUE),
		'tinyint'                   => array('type' => 'int', 'min' => '-128', 'max' => '127'),
		'tinyint unsigned'          => array('type' => 'int', 'min' => '0', 'max' => '255'),
		'tinytext'                  => array('type' => 'string'),
		'year'                      => array('type' => 'string'),
	);

	// Use SET NAMES to set the character set
	protected static $_set_names;

	// MySQL uses a backtick for identifiers
	protected $_identifier = '`';

	public function connect()
	{
		if ($this->_connection)
			return;

		if (Database_MySQL::$_set_names === NULL)
		{
			// Determine if we can use mysql_set_charset(), which is only
			// available on PHP 5.2.3+ when compiled against MySQL 5.0+
			Database_MySQL::$_set_names = ! function_exists('mysql_set_charset');
		}

		// Extract the connection parameters, adding required variabels
		extract($this->_config['connection'] + array(
			'database'   => '',
			'hostname'   => '',
			'port'       => NULL,
			'socket'     => NULL,
			'username'   => '',
			'password'   => '',
			'persistent' => FALSE,
		));

		// Clear the connection parameters for security
		unset($this->_config['connection']);

		try
		{
			if (empty($persistent))
			{
				// Create a connection and force it to be a new link
				$this->_connection = mysql_connect($hostname, $username, $password, TRUE);
			}
			else
			{
				// Create a persistent connection
				$this->_connection = mysql_pconnect($hostname, $username, $password);
			}
		}
		catch (ErrorException $e)
		{
			// No connection exists
			$this->_connection = NULL;

			throw $e;
		}

		if ( ! mysql_select_db($database, $this->_connection))
		{
			// Unable to select database
			throw new Database_Exception(':error',
				array(':error' => mysql_error($this->_connection)),
				mysql_errno($this->_connection));
		}

		if ( ! empty($this->_config['charset']))
		{
			// Set the character set
			$this->set_charset($this->_config['charset']);
		}
	}

	public function disconnect()
	{
		try
		{
			// Database is assumed disconnected
			$status = TRUE;

			if (is_resource($this->_connection))
			{
				$status = mysql_close($this->_connection);
			}
		}
		catch (Exception $e)
		{
			// Database is probably not disconnected
			$status = is_resource($this->_connection);
		}

		return $status;
	}

	public function set_charset($charset)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if (Database_MySQL::$_set_names === TRUE)
		{
			// PHP is compiled against MySQL 4.x
			$status = (bool) mysql_query('SET NAMES '.$this->quote($charset), $this->_connection);
		}
		else
		{
			// PHP is compiled against MySQL 5.x
			$status = mysql_set_charset($charset, $this->_connection);
		}

		if ($status === FALSE)
		{
			throw new Database_Exception(':error',
				array(':error' => mysql_error($this->_connection)),
				mysql_errno($this->_connection));
		}
	}

	public function query($type, $sql, $as_object)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( ! empty($this->_config['profiling']))
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		// Execute the query
		if (($result = mysql_query($sql, $this->_connection)) === FALSE)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			throw new Database_Exception(':error [ :query ]',
				array(':error' => mysql_error($this->_connection), ':query' => $sql),
				mysql_errno($this->_connection));
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT)
		{
			// Return an iterator of results
			return new Database_MySQL_Result($result, $sql, $as_object);
		}
		elseif ($type === Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				mysql_insert_id($this->_connection),
				mysql_affected_rows($this->_connection),
			);
		}
		else
		{
			// Return the number of rows affected
			return mysql_affected_rows($this->_connection);
		}
	}

	public function list_tables($like = NULL)
	{
		if (is_string($like))
		{
			// Search for table names
			$result = $this->query(Database::SELECT, 'SHOW TABLES LIKE '.$this->quote($like), FALSE);
		}
		else
		{
			// Find all table names
			$result = $this->query(Database::SELECT, 'SHOW TABLES', FALSE);
		}

		$tables = array();
		foreach ($result as $row)
		{
			$tables[] = $row[0];
		}

		return $tables;
	}

	public function list_columns($table, $like = NULL)
	{
		// Quote the table name
		$table = $this->quote_table($table);

		if (is_string($like))
		{
			// Search for column names
			$result = $this->query(Database::SELECT, 'SHOW COLUMNS FROM '.$table.' LIKE '.$this->quote($like), FALSE);
		}
		else
		{
			// Find all column names
			$result = $this->query(Database::SELECT, 'SHOW COLUMNS FROM '.$table, FALSE);
		}

		$count = 0;
		$columns = array();
		foreach ($result as $row)
		{
			list($type, $length) = $this->_parse_type($row['Type']);

			if (isset(Database_MySQL::$_types[$type]))
			{
				$column = Database_MySQL::$_types[$type];
			}
			elseif (isset(Database::$_types[$type]))
			{
				$column = Database::$_types[$type];
			}
			else
			{
				$column = array();
			}

			$column['column_name']      = $row['Field'];
			$column['column_default']   = $row['Default'];
			$column['data_type']        = $type;
			$column['is_nullable']      = ($row['Null'] == 'YES');
			$column['ordinal_position'] = ++$count;

			switch ($column['data_type'])
			{
				case 'binary':
				case 'char':
				case 'varbinary':
				case 'varchar':
					$column['character_maximum_length'] = $length;
				break;
				case 'decimal':
					list($column['numeric_precision'], $column['numeric_scale']) = explode(',', $length);
				break;
				case 'tinyblob':
				case 'tinytext':
					$column['character_maximum_length'] = 255;
				break;
				case 'blob':
				case 'text':
					$column['character_maximum_length'] = 65535;
				break;
				case 'mediumblob':
				case 'mediumtext':
					$column['character_maximum_length'] = 16777215;
				break;
				case 'longblob':
				case 'longtext':
					$column['character_maximum_length'] = 4294967295;
				break;
			}

			$columns[$row['Field']] = $column;
		}

		return $columns;
	}

	public function escape($value)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if (($value = mysql_real_escape_string((string) $value, $this->_connection)) === FALSE)
		{
			throw new Database_Exception(':error',
				array(':error' => mysql_errno($this->_connection)),
				mysql_error($this->_connection));
		}

		// SQL standard is to use single-quotes for all values
		return "'$value'";
	}

} // End Database_MySQL
