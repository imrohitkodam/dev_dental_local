<?php

/**
 * Extends JInput class to allow getting raw data from Input object. This can be removed when we don't provide support for Joomla 2.5.x
 *
 * @author        Ossolution Team
 * @package       OS
 * @subpackage    Controller
 */
class OSInput extends JInput
{
	/**
	 * Constructor.
	 *
	 * @param array $source  Source data (Optional, default is $_REQUEST)
	 * @param array $options Array of configuration parameters (Optional)
	 *
	 */
	public function __construct($source = null, array $options = array())
	{
		if (!isset($options['filter']))
		{
			//Set default filter so that getHtml can be returned properly
			$options['filter'] = JFilterInput::getInstance(null, null, 1, 1);
		}

		parent::__construct($source, $options);

		if (get_magic_quotes_gpc())
		{
			$this->data = self::stripSlashesRecursive($this->data);
		}
	}

	/**
	 * Magic method to get an input object
	 *
	 * @param   mixed  $name  Name of the input object to retrieve.
	 *
	 * @return  JInput  The request input object
	 *
	 * @since   11.1
	 */
	public function __get($name)
	{
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}

		$className = 'JInput' . ucfirst($name);

		if (class_exists($className))
		{
			$this->inputs[$name] = new $className(null, $this->options);

			return $this->inputs[$name];
		}

		$superGlobal = '_' . strtoupper($name);

		if (isset($GLOBALS[$superGlobal]))
		{
			$this->inputs[$name] = new OSInput($GLOBALS[$superGlobal], $this->options);

			return $this->inputs[$name];
		}

	}

	/**
	 * Check to see if a variable is a vaialble in the input or not
	 *
	 * @param string $name the variable name
	 *
	 * @return boolean
	 */
	public function has($name)
	{
		if (isset($this->data[$name]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Set data for the input, usually used to set validated data back to input for further processing
	 *
	 * @param $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
	/**
	 * Get the row data from input
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	protected static function stripSlashesRecursive($value)
	{
		$value = is_array($value) ? array_map(array('OSInput', 'stripSlashesRecursive'), $value) : stripslashes($value);

		return $value;
	}
}
