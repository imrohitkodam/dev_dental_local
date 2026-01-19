<?php
 /**
 * @package     Shika
 * @subpackage  com_tjlms
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

jimport('joomla.application.component.modelform');

 /**
 * Methods for lesson.
 *
 * @since  1.0.0
 */
class TjlmsModelOwnassessment extends ListModel
{
	 /**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   2.2
	 */
    public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'lesson','l.title',
				'course','c.title',
				'attempt_starts','lt.timestart',
				'attempt_ends','lt.timeend',
				'status','lt.lesson_status',
				'score','lt.score',
				'name','u.name','l.format'
			);
		}

		$this->ComtjlmsHelper = new ComtjlmsHelper;

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
    public function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication('site');

		$lesson = $app->getUserStateFromRequest($this->context . '.filter.lesson', 'filter_lesson', '0', 'INT');
		$this->setState('filter.lesson', $lesson);

		$course = $app->getUserStateFromRequest($this->context . '.filter.course', 'filter_course', '0', 'INT');
		$this->setState('filter.course', $course);

		$status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'INT');
		$this->setState('filter.status', $status);

		$attempt_starts = $app->getUserStateFromRequest($this->context . '.filter.attempt_starts', 'filter_attempt_starts', '', 'INT');
		$this->setState('filter.attempt_starts', $attempt_starts);

		$attempt_ends = $app->getUserStateFromRequest($this->context . '.filter.attempt_ends', 'filter_attempt_ends', '', 'INT');
		$this->setState('filter.attempt_ends', $attempt_ends);

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$lessontype = $app->getUserStateFromRequest($this->context . '.filter.lessontype', 'filter_lessontype', '', 'INT');
		$this->setState('filter.lessontype', $lessontype);

		$limitstart = Factory::getApplication()->input->getInt('limitstart', 0);

		if ($limit == 0)
		{
			$this->setState('list.start', 0);
		}
		else
		{
			$this->setState('list.start', $limitstart);
		}

		$listOrder = $app->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);
		// List state information.
		parent::populateState('lt.last_accessed_on', 'DESC');
	}

	 /**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.0.0
	 */
	public function getListQuery()
	{
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
		$user_id   = Factory::getUser()->id;


		$query->select(
				'lt.id as lessonTrackId,lt.timestart,lt.timeend,
				lt.lesson_status,lt.attempt,lt.score,lt.user_id,l.id as lessonId,
				l.title,u.name as user_name,l.format,c.id as courseId, c.title as courseTitle'
		);
        $query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
		$query->join(
				'left', $db->quoteName('#__users', 'u') .
				'ON ' . $db->quoteName('lt.user_id') . '=' . $db->quoteName('u.id')
			);

		$query->join('left', $db->quoteName('#__tjlms_lessons', 'l') . 'ON ' . $db->quoteName('lt.lesson_id') . '=' . $db->quoteName('l.id'));

		$query->join(
				'left', $db->quoteName('#__tjlms_courses', 'c') .
				'ON ' . $db->quoteName('l.course_id') . '=' . $db->quoteName('c.id')
			);
		$query->where($db->quoteName('l.format') . ' IN(' . implode(',', $db->quote(array('quiz', 'exercise','feedback'))) . ')');

		$query->where($db->quoteName('lt.user_id') . ' = ' . $db->quote($user_id));


			// Filter by lesson name
			$filter_lesson = $this->getState("filter.lesson");

			if ($filter_lesson)
			{
				$query->where($db->quoteName('l.id') . ' = ' . $db->quote($filter_lesson));
			}

			// Filter by lesson status
			$filter_status = $this->getState("filter.status");

			if ($filter_status)
			{
				$query->where($db->quoteName('lt.lesson_status') . ' = ' . $db->quote($filter_status));
			}

			// Filter by attendee
			$filter_user = $this->getState("filter.name");

			if ($filter_user)
			{
				$query->where($db->quoteName('lt.user_id') . ' = ' . $db->quote($filter_user));
			}

			// Filter by course
			$filter_course = $this->getState("filter.course");

			if ($filter_course)
			{
				$query->where($db->quoteName('l.course_id') . ' = ' . $db->quote($filter_course));
			}

			// For the Date filter
			$attempt_starts = $this->getState('filter.attempt_starts');
			$attempt_starts_time = strtotime($attempt_starts);
			$attempt_starts_date = date("Y-m-d", $attempt_starts_time);

			$attempt_ends = $this->getState('filter.attempt_ends');
			$attempt_ends_time = strtotime($attempt_ends);
			$attempt_ends_date = date("Y-m-d", $attempt_ends_time);

			if (!empty($attempt_starts) and empty($attempt_ends))
			{
				$query->where('DATE(lt.timestart)>=' . "'" . $attempt_starts_date . "'");
			}

			if (!empty($attempt_ends) and empty($attempt_starts))
			{
				$query->where('DATE(lt.timeend)<=' . "'" . $attempt_ends_date . "'");
			}

			if (!empty($attempt_starts) and !empty($attempt_ends))
			{
				if ($attempt_starts == $attempt_ends)
				{
					$query->where('DATE(lt.timeend)=' . "'" . $attempt_ends_date . "'");
				}
				else
				{
					$query->where('DATE(lt.timeend)>=' . "'" . $attempt_starts_date . "'");
					$query->where('DATE(lt.timeend)<=' . "'" . $attempt_ends_date . "'");
				}
			}

			// Filter by lesson type
			$filter_lessontype = $this->getState("filter.lessontype");

			if ($filter_lessontype)
			{
				$query->where($db->quoteName('l.format') . ' = ' . $db->quote($filter_lessontype));
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
	 * Method to get a list of courses.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $ind => $obj)
		{
			if ($obj->format == 'quiz' || $obj->format == 'exercise' || $obj->format == 'feedback')
			{
				$testData = $this->ComtjlmsHelper->getTestData($obj->lessonId, $obj->user_id);
				$obj->test_id = $testData->test_id;
				$obj->type = (!empty($testData->type)?$testData->type:0);
			}
		}

		return $items;
	}

}
