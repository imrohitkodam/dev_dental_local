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
class HierarchyModelNominates extends JModelList
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
				'title', 'c.title',
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

		if (isset($list['ordering']))
		{
			$this->setState('list.ordering', $list['ordering']);
		}

		if (isset($list['direction']))
		{
			$this->setState('list.direction', $list['direction']);
		}
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
		$search = $app->input->get('location');

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'te.id AS ticketEventId'
				)
			);
		$query->from('`#__jticketing_events` AS te');
		$query->select('c.id AS catId, c.title As catName');
		$query->join('LEFT', '`#__categories` AS c ON c.id = te.catid');
		$query->where('c.extension = "com_jticketing"');
		$query->where('c.published = "1"');
		$query->where('te.startdate >= ' . $db->Quote(JHtml::date($input = 'now', 'Y-m-d h:i:s', false)));

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( te.location LIKE ' . $search . ' )');
			}
		}

		$query->group('c.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));

			// $query->order($db->escape('u.name ASC'));
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
		$query->where('te.startdate >= ' . $db->Quote(JHtml::date($input = 'now', 'Y-m-d h:i:s', false)));
		$query->where('te.state = 1');

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

	/**
	 * applyEvent
	 *
	 * @param   integer  $nomineeId  User Id
	 * @param   array    $eventList  event list array
	 *
	 * @return  void.
	 *
	 * @since   1.6.1
	 */
	public function applyEvent($nomineeId, $eventList)
	{
		$db = JFactory::getDBO();
		require_once JPATH_SITE . '/components/com_jticketing/controllers/orders.php';
		$JticketingControllerorders = new JticketingControllerorders;
		$nomineeEmail = JFactory::getUser($nomineeId)->email;

		foreach ($eventList as $key => $value)
		{
			if ($value)
			{
				$orderID = 0;
				// Check if already nominated for events
				$query = $db->getQuery(true);
				$query->select('jix.eventid');
				$query->from('`#__jticketing_order` AS jo');
				$query->join('LEFT', '`#__jticketing_integration_xref` AS jix ON jix.id = jo.event_details_id');
				$query->where('jo.user_id = ' . $db->Quote($nomineeId));
				$query->where("jo.status = 'C'");
				// $query->where('jix.eventid = ' . $db->Quote($value));
				$db->setQuery($query);
				$orderID = $db->loadResult();

				if ($orderID == $value)
				{
					continue;
				}

				$orders  = $JticketingControllerorders->bookTicket($nomineeId, $nomineeEmail, $value);
			}
		}
	}

	public function alreadynominated($nomineeId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('jix.eventid');
		$query->from('`#__jticketing_order` AS jo');
		$query->join('LEFT', '`#__jticketing_integration_xref` AS jix ON jix.id = jo.event_details_id');
		$query->where('jo.user_id = ' . $db->Quote($nomineeId));
		$query->where("jo.status = 'C'");
		$db->setQuery($query);
		return $eventid = $db->loadResult();
	}

	public function NomineeLocation($nomineeId)
	{
		if (!empty($nomineeId))
		{
			$db                       = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('training_location');
			$query->from('`#__tjlms_user_xref` AS te');
			$query->where('user_id =' . $nomineeId);
			$db->setQuery($query);
			return $training_location = $db->loadResult();
		}
	}

	/**
	 * list of Events
	 *
	 * @param   string  $location  location
	 *
	 * @return  dropdown
	 *
	 * @since   1.0
	 */
	public function getEvents($location='')
	{
		$path                     = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';
		$disabled_category = 0;

		if (!class_exists('jticketingmainhelper'))
		{
			JLoader::register('jticketingmainhelper', $path);
			JLoader::load('jticketingmainhelper');
		}

		$jticketingmainhelper = new jticketingmainhelper;
		$jinput        = JFactory::getApplication()->input;
		$catId         = $jinput->get('catId', '', 'integer');
		$nomineeId      = $jinput->get('nomineeId', '', 'INT');
		$msg = array();

		if (!empty($catId))
		{
			$select_options = "";
			$db                       = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('te.id AS eventId,te.access,te.title,te.short_description,te.startdate');
			$query->from('`#__jticketing_events` AS te');
			$query->where('te.catid = ' . $db->Quote($catId));
			$query->where('te.startdate >= ' . $db->Quote(JHtml::date($input = 'now', 'Y-m-d h:i:s', false)));
			$query->where('te.state = 1');
			$user = JFactory::getUser($nomineeId);
			$allowedViewLevels = JAccess::getAuthorisedViewLevels($user->id);
			$implodedViewLevels = implode('","', $allowedViewLevels);

			// Get events with repect to access level
			$query->where('te.access IN ("' . $implodedViewLevels . '")');


			$query->group('te.id');
			$db->setQuery($query);
			// echo $query;
			$eventList = $db->loadObjectList();
			$listv_arr = $listv = '';
			$disabled_count = 0;

			foreach ($eventList as $value)
			{
				if ($value)
				{
					$respectDateofjoining = 0;



					$query = $db->getQuery(true);
					$query->select('jix.eventid');
					$query->from('`#__jticketing_order` AS jo');
					$query->join('LEFT', '`#__jticketing_integration_xref` AS jix ON jix.id = jo.event_details_id');
					$query->where('jo.user_id = ' . $db->Quote($nomineeId));
					$query->where("jo.status = 'C'");
					$query->where('jix.eventid = ' . $db->Quote($value->eventId));
					$db->setQuery($query);
					$orderID = $db->loadResult();
					$sel = '';
					$showEvent = 0;
					$showEvent = $jticketingmainhelper->showbuybutton($value->eventId, $nomineeId);

					if ($showEvent)
					{
						if ($value->eventId == $orderID)
						{
							$sel = "disabled='disabled'";
							$disabled_count++;
						}

						$listv_arr .= "<option value='" . $value->eventId . "' {$sel}>";
						$startdate = JFactory::getDate($value->startdate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
						$listv_arr .= $value->short_description." " . $value->title . '</option>';
					}
				}
			}

			$listv .= '<select name="eventlist[]" class="selected_events" id="eventlist' . $catId . '"
			class="chzn-done form-control required"  data-chosen="com_hiearchy">';

			// If all events are disabled show empty event box
			if ($disabled_count == count($eventList))
			{
				$listv .= '<option value="">' . JText::_('COM_HIERARCHY_NOMINATION_CHOOSE_BATCH_EMPTY') . '</option>';
				$disabled_category = 1;
			}
			elseif (!$eventList or !$listv_arr)
			{
				$listv .= '<option value="">' . JText::_('COM_HIERARCHY_NOMINATION_CHOOSE_BATCH_EMPTY') . '</option>';
				$disabled_category = 1;

			}
			else
			{
				$listv .= '<option value="">' . JText::_('COM_HIERARCHY_NOMINATION_CHOOSE_BATCH_SELECT') . '</option>';
			}

			$listv .= $listv_arr;
			$listv .= '</select>';

			$listv_final['disabled'] = $disabled_category;
			$listv_final['options'] = $listv;
			$listv_final['msg'] = $msg;
			return $listv_final;
		}
	}



	/**
	 * Method to get a list of reporting users.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function ReportToList($nomineeId,$userid)
	{
		if (!$nomineeId)
		return 0;

		$db = $this->getDbo();
		$query = "SELECT user_id
		FROM #__users AS a, #__hierarchy_users AS hu
		WHERE a.id = hu.user_id AND a.block=0 AND 	hu.subuser_id=
		".$nomineeId. " AND hu.user_id=" . $userid;

		$db->setQuery($query);
		return $res = $db->loadResult();
	}
}
