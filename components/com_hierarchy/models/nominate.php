<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Hierarchy records.
 *
 * @since  1.6
 */
class HierarchyModelNominate extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
			'user_id','u.id',
			'name', 'u.name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');

		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');
			$list['ordering'] = $app->input->get('filter_order');
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');

		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');
			$list['direction'] = $app->input->get('filter_order_Dir');
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');

		$this->setState('list.ordering', $list['ordering']);
		$this->setState('list.direction', $list['direction']);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$app = JFactory::getApplication();
		$search = $app->input->get('filter_search');

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT u.*'
				)
			);
		// $query->from('`#__hierarchy_users` AS a');
		$query->from('`#__users` AS u');
		$query->join('LEFT', '#__hierarchy_users AS a ON u.id = a.subuser_id');
		$query->join('LEFT', '#__tjlms_user_xref AS xref ON xref.user_id= a.subuser_id');
		// $query->where("xref.join_date<='2015-03-31'");
		$query->where('a.user_id = ' . JFactory::getUser()->id);
		$query->where('u.block=0');

		// Join over the created by field 'created_by'
		// $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Filter by search in title
		// $search = $this->getState('filter.search');
		//echo $query;die;
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( u.name LIKE ' . $search . ' )');
			}
		}

		// Filtering user_id
		$filter_user_id = $this->state->get("filter.user_id");

		if ($filter_user_id)
		{
			$query->where("a.user_id = '" . $db->escape($filter_user_id) . "'");
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get a list of users.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * Method to get a assign event to users.
	 *
	 * @return  category.
	 *
	 * @since   1.6.1
	 */
	public function getAssignEventToNominee()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$jinput     = JFactory::getApplication()->input;
		$location   = $jinput->post->get('location', '', 'string');

		$query->select('te.id AS ticketEventId');
		$query->from('`#__jticketing_events` AS te');
		$query->select('c.id AS catId, c.title As catName');
		$query->join('LEFT', '`#__categories` AS c ON c.id = te.catid');
		$query->where('c.extension = "com_jticketing"');
		$query->where('c.published = "1"');

		if ($location)
		{
			$location = $db->Quote('%' . $db->escape($location, true) . '%');
			$query->where('( te.location LIKE ' . $location . ' )');
		}

		$query->group('c.id');
		$db->setQuery($query);

		return $ticket_cat = $db->loadObjectList();
	}

	/**
	 * loadFormData.
	 *
	 * @return  void.
	 *
	 * @since   1.6.1
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && !$this->isValidDate($value))
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_HIERARCHY_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in an specified format (YYYY-MM-DD)
	 *
	 * @param   date  $date  Contains the date to be checked
	 *
	 * @return  void.
	 *
	 * @since   1.6.1
	 */
	private function isValidDate($date)
	{
		return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
	}
}
