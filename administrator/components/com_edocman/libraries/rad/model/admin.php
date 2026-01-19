<?php
/**
 * Admin Model Class, implement basic crud methods
 *
 * @author      Ossolution Team
 * @package     OS
 * @subpackage  ModelAdmin
 */
defined('_JEXEC') or die();

/**
 * Prototype admin model.
 *
 * @package    OS
 * @subpackage Model
 */
class OSModelAdmin extends OSModelForm
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var string
	 */
	protected $languagePrefix = null;

	/**
	 * This model trigger events or not. Child class can set it to false if no event processing is needed to improve performance
	 *
	 * @var boolean
	 */
	protected $triggerEvents = true;

	/**
	 * The event to trigger after deleting the data.
	 *
	 * @var string
	 */
	protected $eventAfterDelete = null;

	/**
	 * The event to trigger after saving the data.
	 *
	 * @var string
	 */
	protected $eventAfterSave = null;

	/**
	 * The event to trigger before deleting the data.
	 *
	 * @var string
	 */
	protected $eventBeforeDelete = null;

	/**
	 * The event to trigger before saving the data.
	 *
	 * @var string
	 */
	protected $eventBeforeSave = null;

	/**
	 * The event to trigger after changing the published state of the data.
	 *
	 * @var string
	 */
	protected $eventChangeState = null;

	/**
	 * Name of plugin group which will be loaded to process the triggered event. Default is component name
	 *
	 * @var string
	 */
	protected $pluginGroup = null;

	/**
	 * Model context, used to store session data
	 * @var string
	 */
	protected $context;

	/**
	 * Constructor.
	 *
	 * @param array $config An optional associative array of configuration settings.
	 *
	 * @see OSModel
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if ($this->triggerEvents)
		{
			$name = ucfirst($this->name);
			if (isset($config['plugin_group']))
			{
				$this->pluginGroup = $config['plugin_group'];
			}
			elseif (empty($this->pluginGroup))
			{
				//Plugin group should default to component name
				$this->pluginGroup = substr($this->option, 4);
			}

			//Initialize the events
			if (isset($config['event_after_delete']))
			{
				$this->eventAfterDelete = $config['event_after_delete'];
			}
			elseif (empty($this->eventAfterDelete))
			{
				$this->eventAfterDelete = 'on' . $name . 'AfterDelete';
			}

			if (isset($config['event_after_save']))
			{
				$this->eventAfterSave = $config['event_after_save'];
			}
			elseif (empty($this->eventAfterSave))
			{
				$this->eventAfterSave = 'on' . $name . 'AfterSave';
			}

			if (isset($config['event_before_delete']))
			{
				$this->eventBeforeDelete = $config['event_before_delete'];
			}
			elseif (empty($this->eventBeforeDelete))
			{
				$this->eventBeforeDelete = 'on' . $name . 'BeforeDelete';
			}

			if (isset($config['event_before_save']))
			{
				$this->eventBeforeSave = $config['event_before_save'];
			}
			elseif (empty($this->eventBeforeSave))
			{
				$this->eventBeforeSave = 'on' . $name . 'BeforeSave';
			}

			if (isset($config['event_change_state']))
			{
				$this->eventChangeState = $config['event_change_state'];
			}
			elseif (empty($this->eventChangeState))
			{
				$this->eventChangeState = 'on' . $name . 'ChangeState';
			}
		}

		// JText message prefix. Defaults to the name of component.
		if (isset($config['language_prefix']))
		{
			$this->languagePrefix = strtoupper($config['language_prefix']);
		}
		elseif (empty($this->languagePrefix))
		{
			$this->languagePrefix = strtoupper(substr($this->option, 4));
		}

		$this->context = $this->option . '.' . $this->name;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param $input
	 *
	 * @throws Exception
	 */
	public function save($input)
	{
        $app = JFactory::getApplication();
		if ($this->triggerEvents)
		{
			JPluginHelper::importPlugin($this->pluginGroup);
		}

		$data  = $input->get('jform', array(), 'array'); //No need for filtering as it is filtered in controller already
		$row   = $this->getTable();
		$id    = $data['id'];
		$isNew = true;

		// Load the row if saving an existing record.
		if ($id > 0)
		{
			$row->load($id);
			$isNew = false;
		}
		// Bind the data.
		if (!$row->bind($data))
		{
			throw new Exception ($row->getError());
		}
		// Prepare the row for saving
		$this->prepareTable($row, $input->get('task'), $data);

		// Check the data.
		if (!$row->check())
		{
			throw new Exception ($row->getError());
		}

		//Trigger before save event
		if ($this->triggerEvents)
		{
			$result = $app->triggerEvent($this->eventBeforeSave, array($this->context, $row, $isNew));
			if (in_array(false, $result, true))
			{
				throw new Exception ($row->getError());
			}
		}
		// Store the data.
		if (!$row->store())
		{
			throw new Exception ($row->getError());
		}
		// Clean the component cache.
		$this->cleanCache();

		//Trigger after save event
		if ($this->triggerEvents)
		{
            $app->triggerEvent($this->eventAfterSave, array($this->context, $row, $isNew));
		}

		//Store ID of the record back to input for using in controller
		$input->set('id', $row->id);
	}

	/**
	 * Method override to check-in a record or an array of record
	 *
	 * @param mixed $pks The ID of the primary key or an array of IDs
	 *
	 * @return mixed Boolean false if there is an error, otherwise the count of records checked in.
	 */
	public function checkin($pks = array())
	{
		$pks   = (array) $pks;
		$row   = $this->getTable();
		$count = 0;

		// Check in all items.
		foreach ($pks as $pk)
		{
			if ($row->load($pk))
			{
				if ($row->checked_out > 0)
				{
					if (!parent::checkin($pk))
					{
						return false;
					}
					$count++;
				}
			}
			else
			{
				$this->setError($row->getError());

				return false;
			}
		}

		return $count;
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param array &$pks An array of record primary keys.
	 *
	 * @return boolean True if successful, false if an error occurs.
	 *
	 */
	public function delete($pks)
	{
		$pks = (array) $pks;
		$row = $this->getTable();

        $app = JFactory::getApplication();
		if ($this->triggerEvents)
		{
			// Include  plugins for the on delete events.
			//$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin($this->pluginGroup);
		}

		// Iterate the items to delete each one.
		foreach ($pks as $pk)
		{
			if ($row->load($pk))
			{
				if ($this->triggerEvents)
				{
					// Trigger the onBeforeDelete event.
					$result = $app->triggerEvent($this->eventBeforeDelete, array($this->context, $row));

					if (in_array(false, $result, true))
					{
						$this->setError($row->getError());

						return false;
					}
				}

				if (!$row->delete($pk))
				{
					$this->setError($row->getError());

					return false;
				}

				if ($this->triggerEvents)
				{
					// Trigger the onAfterDelete event.
                    $app->triggerEvent($this->eventAfterDelete, array($this->context, $row));
				}
			}
			else
			{
				$this->setError($row->getError());

				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param array   $pks   A list of the primary keys to change.
	 *
	 * @param integer $value The value of the published state.
	 *
	 * @return boolean True on success.
	 */
	public function publish($pks, $value = 1)
	{
        $app    = JFactory::getApplication();
		$userId = JFactory::getUser()->get('id');
		$row    = $this->getTable();
		$pks    = (array) $pks;

		// Attempt to change the state of the records.
		if (!$row->publish($pks, $value, $userId))
		{
			$this->setError($row->getError());

			return false;
		}

		if ($this->triggerEvents)
		{
			//Trigger onChangeState event
			JPluginHelper::importPlugin($this->pluginGroup);

			// Trigger the onContentChangeState event.
			$result = $app->triggerEvent($this->eventChangeState, array($this->context, $pks, $value));
			if (in_array(false, $result, true))
			{
				$this->setError($row->getError());

				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * Returns NULL if the user did not have edit
	 * privileges for any of the selected primary keys.
	 *
	 * @param integer $pks   The ID of the primary key to move.
	 *
	 * @param integer $delta Increment, usually +1 or -1
	 *
	 * @return mixed False on failure or error, true on success, null if the $pk is empty (no items selected).
	 *
	 */
	public function reorder($pks, $delta = 0)
	{
		$row     = $this->getTable();
		$pks     = (array) $pks;
		$result  = true;
		$allowed = true;

		foreach ($pks as $i => $pk)
		{
			$row->reset();

			if ($row->load($pk) && $this->checkout($pk))
			{
				$where = $this->getReorderConditions($row);
				if (!$row->move($delta, $where))
				{
					$this->setError($row->getError());
					unset($pks[$i]);
					$result = false;
				}

				$this->checkin($pk);
			}
			else
			{
				$this->setError($row->getError());
				unset($pks[$i]);
				$result = false;
			}
		}

		if ($allowed === false && empty($pks))
		{
			$result = null;
		}

		// Clear the component's cache
		if ($result == true)
		{
			$this->cleanCache();
		}

		return $result;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param array   $pks   An array of primary key ids.
	 *
	 * @param integer $order +1 or -1
	 *
	 * @return mixed
	 *
	 */
	public function saveorder($pks = null, $order = null)
	{
		$table      = $this->getTable();
		$conditions = array();

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);
			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];
				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}

				// Remember to reorder within position and client_id
				//$condition = $this->getReorderConditions($table);
				$found     = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$conditions[] = array($table->id, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the title & alias, usually used on save2copy method
	 *
	 * @param        $row   the object being saved
	 *
	 * @param string $alias The alias.
	 *
	 * @param string $title The title.
	 *
	 * @return array Contains the modified title and alias.
	 */
	protected function generateNewTitle($row, $alias, $title)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')->from($this->table);
		$conditions = $this->getReorderConditions($row);
		while (true)
		{
			$query->where('alias=' . $db->quote($alias));
			if (count($conditions))
			{
				$query->where($conditions);
			}
			$db->setQuery($query);
			$found = (int) $db->loadResult();
			if ($found)
			{
				$title = JString::increment($title);
				$alias = JString::increment($alias, 'dash');
				$query->clear('where');
			}
			else
			{
				break;
			}
		}

		return array($title, $alias);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param JTable $table A JTable object.
	 *
	 * @return array An array of conditions to add to ordering queries.
	 *
	 */
	protected function getReorderConditions($table)
	{
		$conditions = array();
		if (property_exists($table, 'catid'))
		{
			$conditions[] = 'catid = ' . (int) $table->catid;
		}

		return $conditions;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param JTable $row  A reference to a JTable object.
	 *
	 * @param string $task The task being performed
	 *
	 * @param array  $data Any extra data which the $row object might need to use
	 *
	 * @return void
	 *
	 */
	protected function prepareTable($row, $task, $data = array())
	{
		$user = JFactory::getUser();
		if (property_exists($row, 'title'))
		{
			$titleField = 'title';
		}
		elseif (property_exists($row, 'name'))
		{
			$titleField = 'name';
		}
		if (($task == 'save2copy') && $titleField)
		{
			if (property_exists($row, 'alias'))
			{
				//Need to generate new title and alias
				list ($title, $alias) = $this->generateNewTitle($row, $row->alias, $row->{$titleField});
				$row->{$titleField} = $title;
				$row->alias         = $alias;
			}
			else
			{
				$row->{$titleField} = JString::increment($row->{$titleField});
			}
		}

		if (property_exists($row, 'title'))
		{
			$row->title = htmlspecialchars_decode($row->title, ENT_QUOTES);
		}

		if (property_exists($row, 'name'))
		{
			$row->name = htmlspecialchars_decode($row->name, ENT_QUOTES);
		}

		if (property_exists($row, 'alias'))
		{
			if (empty($row->alias))
			{
				$row->alias = $row->{$titleField};
			}
			$row->alias = JApplicationHelper::stringURLSafe($row->alias);
		}
		if (empty($row->id))
		{
			// Set ordering to the last item if not set
			if (property_exists($row, 'ordering') && empty($row->ordering))
			{
				$db         = JFactory::getDbo();
				$query      = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName($this->table));
				$conditions = $this->getReorderConditions($row);
				if (count($conditions))
				{
					$query->where($conditions);
				}
				$db->setQuery($query);
				$max           = $db->loadResult();
				$row->ordering = $max + 1;
			}

			if (property_exists($row, 'created_time') && !$row->created_time)
			{
				$row->created_time = JFactory::getDate()->toSql();
			}

			if (property_exists($row, 'created_user_id') && !$row->created_user_id)
			{
				$row->created_user_id = $user->get('id');
			}
		}

        if (property_exists($row, 'modified_time') && !$row->modified_time)
		{
			$row->modified_time = JFactory::getDate()->toSql();
		}

		if (property_exists($row, 'modified_user_id') && (!$row->modified_user_id or ($row->modified_user_id && ($row->modified_user_id != $user->get('id')))))
		{
			$row->modified_user_id = $user->get('id');
		}

		if (property_exists($row, 'params') && is_array($row->params))
		{
			$row->params = json_encode($row->params);
		}
	}
}
