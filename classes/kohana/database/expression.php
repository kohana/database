<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database expressions.
 *
 * @package    Database
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Database_Expression {

	// Raw expression string
	protected $_value;

	/**
	 * Sets the expression string.
	 */
	public function __construct($value)
	{
		// Set the expression string
		$this->_value = $value;
	}

	/**
	 * Get the expression value as a string.
	 *
	 * @return  string
	 */
	public function value()
	{
		return (string) $this->_value;
	}

	/**
	 * Return the value of the expression as a string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->value();
	}

} // End Database_Expression
