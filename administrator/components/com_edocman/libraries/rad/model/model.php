<?php
/**
 * @package     OS
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2014 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Base Model class
 *
 * @package       OS
 * @subpackage    Model
 * @since         1.0
 */
class OSModel
{

	/**
	 * Full name of the component this model belong to
	 *
	 * @var string
	 */
	protected $option = null;

	/**
	 * The model name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Model state
	 *
	 * @var OSModelState
	 */
	protected $state;

	/**
	 * The database driver.
	 *
	 * @var JDatabaseDriver
	 */
	protected $db;

	/**
	 *
	 * Prefix for model, table class name
	 *
	 * @var string
	 */
	protected $classPrefix;

	/**
	 * The prefix of the database table
	 *
	 * @var string
	 */
	protected $tablePrefix;

	/**
	 * The name of the database table
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Ignore request or not. If set to Yes, model states won't be set when it is created
	 *
	 * @var boolean
	 */
	public $ignoreRequest = false;

	/**
	 * Remember model states value in session
	 *
	 * @var boolean
	 */
	public $rememberStates = false;

	/**
	 * An array of error messages or Exception objects.
	 *
	 * @var    array
	 */
	protected $errors = array();

	/**
	 * @param string $name   The name of model to instantiate
	 *
	 * @param string $prefix Prefix for the model class name, ComponentnameModel
	 *
	 * @param array  $config Configuration array for model
	 *
	 * @return OSModel A model object
	 */
	public static function getInstance($name, $prefix, $config = array())
	{
		$name  = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$class = ucfirst($prefix) . ucfirst($name);
		if (!class_exists($class))
		{
			if (isset($config['default_model_class']))
			{
				$class = $config['default_model_class'];
			}
			else
			{
				$class = 'OSModel';
			}
		}

		return new $class($config);
	}

	/**
	 * Constructor
	 *
	 * @param array $config An array of configuration options
	 *
	 */
	public function __construct($config = array())
	{
		if (isset($config['name']))
		{
			$this->name = $config['name'];
		}
		else
		{
			$className = get_class($this);
			$pos       = strpos($className, 'Model');
			if ($pos !== false)
			{
				$this->name = substr($className, $pos + 5);
			}
			else
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}
		}

		// Set the model state
		if (isset($config['state']))
		{
			$this->state = $config['state'];
		}
		else
		{
			$this->state = new OSModelState();
		}

		if (isset($config['db']))
		{
			$this->db = $config['db'];
		}
		else
		{
			$this->db = JFactory::getDbo();
		}

		if (isset($config['option']))
		{
			$this->option = $config['option'];
		}
		else
		{
			$className = get_class($this);
			$pos       = strpos($className, 'Model');
			if ($pos !== false)
			{
				$this->option = 'com_' . substr($className, 0, $pos);
			}
			else
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}
		}

		if (isset($config['table_prefix']))
		{
			$this->tablePrefix = $config['table_prefix'];
		}
		else
		{
			$component         = substr($this->option, 4);
			$this->tablePrefix = '#__' . strtolower($component) . '_';
		}

		if (isset($config['table']))
		{
			$this->table = $config['table'];
		}
		else
		{
			$this->table = $this->tablePrefix . strtolower(OSInflector::pluralize($this->name));
		}

		if (isset($config['ignore_request']))
		{
			$this->ignoreRequest = $config['ignore_request'];
		}

		if (isset($config['remember_states']))
		{
			$this->rememberStates = $config['remember_states'];
		}

		if (isset($config['class_prefix']))
		{
			$this->classPrefix = $config['class_prefix'];
		}
		else
		{
			$component         = substr($this->option, 4);
			$this->classPrefix = ucfirst($component);
		}
		//Add tables path, make it easy to use table in module in this case
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/table');
	}

	/**
	 * Get JTable object for the model
	 *
	 * @param string $name
	 *
	 * @return JTable
	 */
	public function getTable($name = '')
	{
		if (!$name)
		{
			$name = OSInflector::singularize($this->name);
			$tableName = $this->table;
		}
		else
		{
			$tableName = $this->tablePrefix . strtolower(OSInflector::pluralize($name));
		}
		$class = $this->classPrefix . 'Table' . ucfirst($name);
		if (class_exists($class))
		{
			return new $class($this->db);
		}
		else
		{
			return new OSTable($tableName, 'id', $this->db);
		}
	}

	/**
	 * Set the model state properties
	 *
	 * @param string|array The   name of the property, an array
	 *
	 * @param              mixed The value of the property
	 *
	 * @return OSModel
	 */
	public function set($property, $value = null)
	{
		$changed = false;
		if (is_array($property))
		{
			if (isset($property['cid']) && !isset($property['id']))
			{
				$property['id'] = (int) $property['cid'][0];
			}
			foreach ($property as $key => $value)
			{
				if (isset($this->state->$key) && $this->state->$key != $value)
				{
					$changed = true;
					break;
				}
			}

			$this->state->setData($property);
		}
		else
		{
			if (isset($this->state->$property) && $this->state->$property != $value)
			{
				$changed = true;
			}

			$this->state->$property = $value;
		}

		if ($changed)
		{
			$limit = $this->state->limit;
			if ($limit)
			{
				$offset = $this->state->limitstart;
				$total  = $this->getTotal();

				// If the offset is higher than the total recalculate the offset
				if ($offset !== 0 && $total !== 0)
				{
					if ($offset >= $total)
					{
						$offset                  = floor(($total - 1) / $limit) * $limit;
						$this->state->limitstart = $offset;
					}
				}
			}
			$this->data  = null;
			$this->total = null;
		}

		return $this;
	}

	/**
	 * Get the model state properties
	 *
	 * If no property name is given then the function will return an associative array of all properties.
	 *
	 * @param string $property The name of the property
	 *
	 * @param string $default  The default value
	 *
	 * @return mixed <string, OSModelState>
	 */
	public function get($property = null, $default = null)
	{
		$result = $default;

		if (is_null($property))
		{
			$result = $this->state;
		}
		else
		{
			if (isset($this->state->$property))
			{
				$result = $this->state->$property;
			}
		}

		return $result;
	}

	/**
	 * Reset all cached data and reset the model state to it's default
	 *
	 * @param boolean If TRUE use defaults when resetting. Default is TRUE
	 *
	 * @return OSModel
	 */
	public function reset($default = true)
	{
		$this->data  = null;
		$this->total = null;
		$this->state->reset($default);
		$this->query = $this->db->getQuery(true);

		return $this;
	}

	/**
	 * Method to get state object
	 *
	 * @return OSModelState The state object
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get the dbo
	 *
	 * @return JDatabaseDriver
	 */
	public function getDbo()
	{
		return $this->db;
	}

	/**
	 * Clean the cache
	 *
	 * @param   string  $group     The cache group
	 * @param   integer $client_id The ID of the client
	 *
	 * @return  void
	 *
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
        $app     = JFactory::getApplication();
		$conf    = JFactory::getConfig();
		$options = array(
			'defaultgroup' => ($group) ? $group : $this->option,
			'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();
		// Trigger the onContentCleanCache event.
		if (!empty($this->eventCleanCache))
		{
			//$dispatcher = JDispatcher::getInstance();;
			$app->triggerEvent($this->event_clean_cache, $options);
		}
	}


	/**
	 * Get the most recent error message.
	 *
	 * @param   integer $i        Option error index.
	 * @param   boolean $toString Indicates if JError objects should return their error message.
	 *
	 * @return  string   Error message
	 *
	 */
	public function getError($i = null, $toString = true)
	{
		// Find the error
		if ($i === null)
		{
			// Default, return the last message
			$error = end($this->errors);
		}
		elseif (!array_key_exists($i, $this->errors))
		{
			// If $i has been specified but does not exist, return false
			return false;
		}
		else
		{
			$error = $this->errors[$i];
		}

		// Check if only the string is requested
		if ($error instanceof Exception && $toString)
		{
			return (string) $error;
		}

		return $error;
	}

	/**
	 * Return all errors, if any.
	 *
	 * @return  array  Array of error messages or JErrors.
	 *
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Add an error message.
	 *
	 * @param   string $error Error message.
	 *
	 * @return  void
	 */
	public function setError($error)
	{
		array_push($this->errors, $error);
	}

	/**
	 * Get a model state by name
	 *
	 * @param string The key name.
	 *
	 * @return string The corresponding value.
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Set a model state by name
	 *
	 * @param string The key name.
	 *
	 * @param mixed  The value for the key
	 *
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Supports a simple form Fluent Interfaces.
	 * Allows you to set states by
	 * using the state name as the method name.
	 *
	 * For example : $model->filter_order('name')->filter_order_Dir('DESC')->limit(10)->getData();
	 *
	 * @param string Method name
	 *
	 * @param array  Array containing all the arguments for the original call
	 *
	 * @return OSModel
	 */
	public function __call($method, $args)
	{
		if (isset($this->state->$method))
		{
			return $this->set($method, $args[0]);
		}

		return null;
	}
}