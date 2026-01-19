<?php

/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');


/**
 * Methods supporting a list of Hierarchy records.
 */
class HierarchyModelReschedules extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				                              'id', 'a.id',
                'ticket_id', 'a.ticket_id',
                'batch_code', 'a.batch_code',
                'cost', 'a.cost',
                'user', 'a.user',
                'bh', 'a.bh',
                'hr', 'a.hr',
                'reason', 'a.reason',
                'title', 'a.title',
                'location', 'a.location',
                'created_on', 'a.created_on',
                'modified_on', 'a.modified_on'

			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{


		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', '5');
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
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

		if (empty($list['ordering']))
		{
			$list['ordering'] = 'id';
		}

		if (empty($list['direction']))
		{
			$list['direction'] = 'desc';
		}

		$this->setState('list.ordering', $list['ordering']);
		$this->setState('list.direction', $list['direction']);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*','c.name'
				)
			);

		$query->from('`#__hierarchy_reschedule` AS a');
		
		$query->join('LEFT', $db->quoteName('#__users', 'c') . ' ON (' . $db->quoteName('a.user_id') . ' = ' . $db->quoteName('c.id') . ')');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				//$search = $db->Quote('%' . $db->escape($search, true) . '%');
				  $query->where('a.ticket_id like "%' .$search . '%" or c.name like "%' . $search. '%" or a.title like "%' . $search. '%"');
			}
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

	public function getItems()
	{
		$items = parent::getItems();
		

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
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
	 * @param string Contains the date to be checked
	 *
	 */
	private function isValidDate($date)
	{
		return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
	}
	public function store($data)
    {
		$params = JComponentHelper::getParams('com_hierarchy');
		
		$db      = $this->getDbo();
		$insertObj             = new stdClass;
		$insertObj->ticket_id  = $data->get('ticket_id','','string');
		$insertObj->user_id    = $data->get('uid');
		$insertObj->batch_code = $data->get('bcode','','string');
		$insertObj->cost       = $data->get('cost');
		$insertObj->user       = 0;
		$insertObj->bh         = 0;
		$insertObj->hr         = 0;
		$insertObj->title      = $data->get('bname','','string');
		$insertObj->location   = $data->get('location','','string');
		$insertObj->created_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);
		$insertObj->modified_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);

		if($db->insertObject('#__hierarchy_reschedule', $insertObj, ''))
		{
			$insertId = $db->insertid();
			require_once JPATH_COMPONENT . '/helpers/hierarchy.php';
			$mainframe  = JFactory::getApplication();
			$mailfrom = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$params = JComponentHelper::getParams('com_hierarchy');
			$reschedule_perfix = $params->get('reschedule_perfix', '');
			$user	= JFactory::getUser($data->get('uid'));
			$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_EMAIL_SUBJECT');

			$rowBody = '
			<p style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Hi {user_name},</span></p><br>
<p style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">We have received a request to reschedule your batch for your Learning Lab programme. Please click on the link below to process your request.</span></p><br>

<p style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">PLEASE NOTE: YOU NEED TO ENSURE WE RECEIVE THE APPROVALS AT LEAST 3 WORKING DAYS BEFORE THE BATCH COMMENCEMENT DATE.</span></p><br>

<p style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Reschedule Request ID: {reschedule_no}</span></p>
<p style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><a href="'.JURI::root().'index.php?option=com_hierarchy&view=hierarchys&rid='.$insertId.'&tid='.base64_encode($reschedule_perfix.$insertId).'&tmpl=component">{confirmation_link}</a></p><br>

<p style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Regards,</span><br><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">L&amp;D</span><br>
</p>';

			$replaceArr                    = new stdClass;
			$replaceArr->user_name         = $user->name;
			$replaceArr->ticket_id         = $data->get('ticket_id') ;
			$replaceArr->confirmation_link = JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_LINK') ;
			$replaceArr->user_email = '' ;
			$replaceArr->batch_name = '' ;
			$replaceArr->cost = '' ;
			$replaceArr->reason = '' ;
			$replaceArr->bh_head_name = '' ;
			$replaceArr->hr_head_name = '' ;
			$replaceArr->status = '' ;
			$replaceArr->manager_name = '';
			$replaceArr->department = '';
			$replaceArr->sub_department = '';
			$replaceArr->batch_code = '';
			$replaceArr->batch_date_time = '';
			$replaceArr->batch_location = '';
			$replaceArr->reschedule_no = $reschedule_perfix.$insertId;
			$replaceArr->status_bh = '';
			$replaceArr->reason_bh = '';
			$replaceArr->status_hr = '';
			$replaceArr->reason_hr = '';

			$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
			$user_data = JFactory::getUser($data->get('uid'));
			$email = $user_data->email;
			
			$cc[] = $params->get('reschedule_manager_email');
			$cc[] = $data->get('manager_email','','RAW');
			
			$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $email, $emailSubject, $emailBody, $cc);

			return true;
		}
		else
		{
			
			return false;
		}
	}


}
