<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Tjlms records.
 *
 * @since  1.0.0
 */
class TjlmsModelEnrolment extends JModelList
{
	protected $errorCode;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'u.id',
				'name', 'u.name',
				'status', 'u.block',
				'username', 'u.username',
				'groupfilter', 'uum.group_id'
			);
		}

		JLoader::register('TjlmsCoursesHelper', JPATH_SITE . '/components/com_tjlms/helpers/courses.php');
		$this->tjlmsCoursesHelper = new TjlmsCoursesHelper;

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
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		// List state information.
		parent::populateState('uc.username', 'asc');

		$app = JFactory::getApplication();

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		$limitstart = $app->input->get('limitstart', 0, 'uint');

		$this->setState('list.start', $limitstart);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Filtering type
		$this->setState('filter.accesslevel', $app->getUserStateFromRequest($this->context . '.filter.accesslevel', 'accesslevel', '', 'STRING'));

		// Filtering group
		$groupfilter = $app->getUserStateFromRequest($this->context . '.filter.groupfilter', 'groupfilter', '', 'INT');
		$this->setState('filter.groupfilter', $groupfilter);

		// Filter for selected courses
		$selectedcourse = $app->getUserStateFromRequest($this->context . '.filter.selectedcourse', 'selectedcourse', '', 'ARRAY');
		$this->setState('filter.selectedcourse', $selectedcourse);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_tjlms');
		$this->setState('params', $params);

		// List state information.
		parent::populateState();
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 *
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select', 'distinct(uc.id), uc.name, uc.username, uc.block'
				)
		);

		$query->from('`#__users` AS uc');
		$query->join('INNER', '`#__user_usergroup_map` as uum ON uc.id = uum.user_id');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('uc.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(( uc.name LIKE ' . $search . ' ) OR ( uc.username LIKE ' . $search . ' ) OR ( uc.id LIKE ' . $search . ' ))');
			}
		}

		$input    = JFactory::getApplication()->input;
		$groupfilter = $this->getState('filter.groupfilter');

		// Get user ID if view called from course list view to view enrolled users.
		if (!$groupfilter)
		{
			$coursefilter = $this->getState('filter.coursefilter');
		}

		// Filtering type
		if ($groupfilter != '')
		{
			$query->where('uum.group_id = ' . $groupfilter);
		}

		// Filtering groups
		$accessLevel = $this->state->get('filter.accesslevel');

		if ($accessLevel != '')
		{
			$query->where('uum.group_id=' . $accessLevel);
		}

		$where = $this->_buildContentWhere();

		if ($where)
		{
			$query->where(' ' . $where);
		}

		$subUsers = $this->getState('list.subuserfilter', 0);

		if ($subUsers == 1)
		{
			JLoader::import('administrator.components.com_tjlms.helpers.tjlms', JPATH_SITE);
			$hasUsers = TjlmsHelper::getSubusers();

			if (!$hasUsers)
			{
				$hasUsers = array(0);
			}

			$query->where('uc.id IN(' . implode(',', $hasUsers) . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * To get where condition for query.
	 *
	 * @return  query
	 *
	 * @since  1.0.0
	 */
	public function _buildContentWhere()
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDBO();
		$c_id = $this->getState('filter.selectedcourse');

		if (count($c_id) == 1)
		{
			$enroled_users = $this->getCourseEnrolledUsers($c_id[0]);
		}

		$where = '';

		if (!empty($enroled_users))
		{
			$where[] = "uc.id NOT IN(" . implode(',', $enroled_users) . ")";
		}

		$c_al = $input->get('course_al', '', 'STRING');
		$u_groups = '';

		// If course has access level get groups applicable for that access level

		if ($c_al && $c_al != 1)
		{
			$u_groups = $this->getGroups($c_al);
		}

		if (!empty($u_groups['0']))
		{
			$where[] = "uum.group_id IN(" . implode(',', $u_groups) . ")";
		}

		if (!empty($where))
		{
			$where = (count($where) ?  implode(' AND ', $where) : '');
		}

		return $where;
	}

	/**
	 * To plublish and unpublish enrolledment.
	 *
	 * @param   JRegistry  $items  The item to update.
	 * @param   JRegistry  $state  The state for the item.
	 *
	 * @return  true or false
	 *
	 * @since  1.0.0
	 */
	public function setItemState($items, $state)
	{
		$db = JFactory::getDBO();

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('state') . ' = ' . $state
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('id') . ' = ' . $id
				);

				$query->update($db->quoteName('#__tjlms_enrolled_users'))->set($fields)->where($conditions);

				$db->setQuery($query);

				if (!$db->execute())
				{
					$this->setError($this->_db->getErrorMsg());

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * To get user already enrolled in the course.
	 *
	 * @param   INT  $c_id  The course ID.
	 *
	 * @return  result
	 *
	 * @since  1.0.0
	 */
	public function getCourseEnrolledUsers($c_id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('eu.user_id');
		$query->from('#__tjlms_enrolled_users as eu');
		$query->where('eu.course_id="' . (int) $c_id . '"');
		$query->where('eu.state=1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results
		return $db->loadColumn();
	}

	/**
	 * To get groups for the repected access level.
	 *
	 * @param   INT  $c_al  The access level ID.
	 *
	 * @return  Object
	 *
	 * @since  1.0.0
	 */
	public function getGroups($c_al)
	{
		$al_groups = array();

		$db	= JFactory::getDBO();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('rules');
		$query->from('`#__viewlevels`');

		// If public get all access levels
		if ($c_al == 1)
		{
			$db->setQuery($query);
			$tempLevels = $db->loadObjectlist();

			foreach ($tempLevels as $tempLevel)
			{
				$temp = json_decode($tempLevel->rules);

				$al_groups = array_merge($al_groups, $temp);
				$al_groups = array_unique($al_groups);
			}
		}
		else
		{
			$query->where('id="' . (int) $c_al . '"');
			$db->setQuery($query);
			$temp = json_decode($db->loadResult());

			if (isset($temp))
			{
				$al_groups = array_merge($al_groups, $temp);
			}
		}

		return $al_groups;
	}

	/**
	 * To get the records
	 *
	 * @return  Object
	 *
	 * @since  1.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		// Get a db connection.
		$db	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('title','id')));
		$query->from($db->quoteName('#__usergroups'));
		$db->setQuery($query);
		$groups = $db->loadAssocList('id', 'title');

		foreach ($items as $k => $obj)
		{
			$userGroups = JAccess::getGroupsByUser($obj->id, false);
			$userGroups = array_flip($userGroups);
			$group 		= array_intersect_key($groups, $userGroups);
			$items[$k]->groups = implode('<br />', $group);
		}

		return $items;
	}

	/**
	 * To get user already enrolled in the course.
	 *
	 * @param   INT  $courseId  course ID.
	 *
	 * @param   INT  $userId    user ID.
	 *
	 * @return  result
	 *
	 * @since  1.0.0
	 */
	public function getEnrolledUserParams($courseId, $userId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->quoteName('#__tjlms_enrolled_users'));
		$query->where($db->quoteName('course_id') . " = " . $db->quote($courseId));
		$query->where($db->quoteName('user_id') . " = " . $db->quote($userId));
		$db->setQuery($query);
		$result = $db->loadObject();

		return json_decode($result->params, true);
	}

	/**
	 * To get user already enrolled in the course.
	 *
	 * @param   INT    $courseId  course ID.
	 * @param   INT    $userId    user ID.
	 * @param   ARRAY  $col       column array.
	 *
	 * @return  result
	 *
	 * @since  1.0.0
	 */
	public function getEnrolledUserColumn($courseId, $userId, $col)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName($col));
		$query->from($db->quoteName('#__tjlms_enrolled_users'));
		$query->where($db->quoteName('course_id') . " = " . $db->quote($courseId));
		$query->where($db->quoteName('user_id') . " = " . $db->quote($userId));
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Check user is enrolled to the course or not
	 *
	 * @param   INT  $courseId  course ID.
	 * @param   INT  $userId    user ID.
	 *
	 * @return  enrolled user id on success
	 *
	 * @since  1.1.3
	 */
	public function checkUserEnrollment($courseId, $userId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('`#__tjlms_enrolled_users` AS a');
		$query->where('a.course_id = ' . (int) $courseId);
		$query->where('a.user_id = ' . (int) $userId);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function used to enroll user to course
	 *
	 * @param   array  $data  array of data
	 *
	 * @return enrollment id
	 *
	 * @since 1.0.0
	 */
	public function save($data)
	{
		$userId = empty($data['user_id']) ? JFactory::getUser()->id : $data['user_id'];
		$courseId = $data['course_id'];
		$state = isset($data['state']) ? $data['state'] : 0;
		$notify_user = $data['notify_user'];
		$loggedInUser = JFactory::getUser()->id;
		$courseInfo = $this->tjlmsCoursesHelper->getcourseInfo($courseId);

		$path = JPATH_ADMINISTRATOR . '/components/com_tjlms/helpers/tjlms.php';
		JLoader::register('TjlmsHelper', $path);
		JLoader::load('TjlmsHelper');
		$canEnroll = TjlmsHelper::canSelfEnrollCourse($courseId, $userId);

		if (!$canEnroll && $loggedInUser == $userId)
		{
			$msg = JText::sprintf('COM_TJLMS_COURSE_ENROLL_NOT_ALLOWED', $courseInfo->title);
			$this->setError($msg);

			return false;
		}

		// If enrolling user is different
		if ($loggedInUser != $userId)
		{
			$canManageEnrollment = TjlmsHelper::canManageCourseEnrollment($courseId, $loggedInUser);

			if (!$canManageEnrollment)
			{
				$msg = JText::sprintf('COM_TJLMS_COURSE_MANAGE_ENROLL_NOT_ALLOWED', $courseInfo->title);

				$this->setError($msg);

				return false;
			}
		}

		try
		{
			// Check user is valid or not
			if (!JFactory::getUser($userId)->id)
			{
				throw new Exception(JText::sprintf("COM_TJLMS_INVALID_USER", $userId), 404);
			}
		}
		catch (Exception $e)
		{
			$this->errorCode = $e->getCode();
			$this->setError($e->getMessage());

			return false;
		}

		$db = JFactory::getDBO();

		JLoader::import('components.com_tjlms.models.course', JPATH_SITE);
		$courseModel = JModelLegacy::getInstance('Course', 'TjlmsModel');
		$courseObj = $courseModel->getcourseInfo($courseId);

		try
		{
			// Check course is present or not
			if (empty($courseObj))
			{
				throw new Exception(JText::_("COM_TJLMS_COURSE_NOT_EXISTS"), 404);
			}
		}
		catch (Exception $e)
		{
			$this->errorCode = $e->getCode();
			$this->setError($e->getMessage());

			return false;
		}

		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
		$enrollTableObj = JTable::getInstance('Enrolledusers', 'TjlmsTable', array('dbo', $db));
		$enrollTableObj->load(array('user_id' => (int) $userId, 'course_id' => (int) $courseId));
		$now = JFactory::getDate()->toSql(true);

		if (!$enrollTableObj->id)
		{
			$enrollTableObj->user_id = $userId;
			$enrollTableObj->course_id = $courseId;
			$enrollTableObj->enrolled_on_time = $now;
			$enrollTableObj->modified_time = $now;
		}
		else
		{
			$enrollTableObj->modified_time = $now;
		}

		$enrollTableObj->unlimited_plan = empty($data['unlimited_plan']) ? 0 : $data['unlimited_plan'];
		$enrollTableObj->state = $state;
		$enrolledBy = $enrollTableObj->enrolled_by = JFactory::getUser()->id;

		try
		{
			$enrollTableObj->store();
		}
		catch (RuntimeException $e)
		{
			$this->errorCode = $e->getCode();
			$this->setError($e->getMessage());

			return false;
		}

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');

		// Trigger all "onAfterCourseEnrol" plugins method
		$dispatcher->trigger('onAfterCourseEnrol', array(
										$userId,
										$enrollTableObj->state,
										$courseId,
										$enrollTableObj->enrolled_by,
										$notify_user
									)
							);

		return $enrollTableObj->id;
	}

	/**
	 * used to get the error code set in exception
	 *
	 * @return  error code
	 *
	 * @since  1.1.3
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * To get non enrolled users for a course
	 *
	 * @param   ARRAY  $userIds   selected users array
	 * @param   INT    $courseId  course id
	 *
	 * @return  Object
	 *
	 * @since  1.0.0
	 */
	public function getNonEnrolledUsers($userIds, $courseId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('user_id'));
		$query->from($db->quoteName('#__tjlms_enrolled_users'));
		$query->where($db->quoteName('course_id') . " = " . $db->quote($courseId));
		$query->where($db->quoteName('user_id') . "IN (" . implode(',', $db->quote($userIds)) . " ) ");
		$db->setQuery($query);
		$enrolledUsers = $db->loadColumn();
		$result = array_unique(array_diff($userIds, $enrolledUsers));

		return  $result;
	}

	/**
	 * Function used to enroll user to course
	 *
	 * @param   array  $data  array of data
	 *
	 * @return enrollment id
	 *
	 * @since 1.0.0
	 */
	public function userEnrollment($data)
	{
		if (empty($data['course_id']))
		{
			return false;
		}

		$params = JComponentHelper::getParams('com_tjlms');

		$courseInfo = $this->tjlmsCoursesHelper->getcourseInfo($data['course_id']);
		$data['course_type'] = $courseInfo->type;

		if (isset($data['due_date']))
		{
			$this->userAssignment($data);
		}
		elseif (isset($courseInfo->type) && $courseInfo->type == 1)
		{
			$data['state'] = !$params->get('paid_course_admin_approval', '0', 'INT');
			$this->paidEnrollment($data);
		}
		else
		{
			$data['state'] = !$params->get('admin_approval', '0', 'INT');
			$this->save($data);
		}
	}

	/**
	 * Function used to user assignment to course
	 *
	 * @param   array  $data  array of data
	 *
	 * @return enrollment id
	 *
	 * @since 1.0.0
	 */
	public function userAssignment($data)
	{
		JLoader::import('comtjlmsHelper', '/components/com_tjlms/helpers/main.php');
		$comtjlmsHelper = new comtjlmsHelper;
		$flag = 1;

		if ($data['course_type'] == 1)
		{
			JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjlms/models');
			$ordersModel = JModelLegacy::getInstance('Orders', 'TjlmsModel');
			$successfulOrdered = $ordersModel->placeOrder($data['course_id'], $data['user_id']);

			if (!$successfulOrdered)
			{
				$flag = 0;
				$msg = JText::_('COM_TJLMS_COURSE_ENROLL_ORDER_FAIL');
			}
		}

		if ($flag == 1)
		{
			$data['element']    = 'com_tjlms.course';
			$data['element_id'] = $data['course_id'];
			$data['type'] = 'assign';
			$course_url         = 'index.php?option=com_tjlms&view=course&id=' . $data['course_id'];

			$itemId = $comtjlmsHelper->getitemid($course_url);
			$data['url'] = $course_url . '&Itemid=' . $itemId;

			$JlikeModelContentForm = JModelLegacy::getInstance('ContentForm', 'JlikeModel');
			$content_id = $JlikeModelContentForm->getConentId($data);

			$jlikeModelRecommendations = JModelLegacy::getInstance('Recommendations', 'JlikeModel');

			if ($contentId)
			{
				$jlikeModelRecommendations->setState("content_id", $contentId);
			}

			$jlikeModelRecommendations->setState("type", "myassign");
			$jlikeModelRecommendations->setState("user_id", $data['user_id']);
			$assigndetails     = $jlikeModelRecommendations->getItems();
			$data['recommend_friends'] = array($data['user_id']);
			$data['todo_id'] = 0;

			if (isset($assigndetails[0]->id))
			{
				$data['todo_id'] = $assigndetails[0]->id;
			}

			$manageenrollmentsModel = JModelLegacy::getInstance('Manageenrollments', 'TjlmsModel');

			return $manageenrollmentsModel->updateAssignmentDate($data);
		}
	}

	/**
	 * Function used to enroll user to paid course
	 *
	 * @param   array  $data  array of data
	 *
	 * @return enrollment id
	 *
	 * @since 1.0.0
	 */
	public function paidEnrollment($data)
	{
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjlms/models');
		$ordersModel = JModelLegacy::getInstance('Orders', 'TjlmsModel');
		$orderedData = $ordersModel->placeOrder($data['course_id'], $data['user_id']);

		if ($orderedData)
		{
			$session    = JFactory::getSession();
			$session->set('lms_orderid', 0);

			if ($orderedData['time_measure'] == 'unlimited')
			{
				$data['unlimited_plan'] = 1;
			}

			$enrollmentId = $this->save($data);

			if ($enrollmentId)
			{
				$orderInfo['enrollment_id'] = $enrollmentId;
				$buymodel = JModelLegacy::getInstance('buy', 'TjlmsModel');
				$buymodel->updateOrderDetails($orderedData['order_id'], $orderInfo);
				$this->tjlmsCoursesHelper->updateEndTimeForCourse($orderedData['plan_id'], $enrollmentId);
			}
		}
	}
}
