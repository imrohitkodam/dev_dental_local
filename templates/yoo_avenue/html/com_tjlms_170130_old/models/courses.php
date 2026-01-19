<?php
/**
 * @package    LMS_Shika
 * @copyright  Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
jimport('techjoomla.common');

/**
 * Model for courses
 *
 * @since  1.0
 */

class TjlmsModelcourses extends JModelList
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
				'id',
				'a.id',
				'title',
				'a.title',
				'state',
				'a.state'
			);
		}

		$this->comtjlmsHelper = new comtjlmsHelper;
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
		// List state information.
		parent::populateState('a.id', 'desc');

		// Initialise variables.
		$app = JFactory::getApplication('site');

		// List state information.

		// Get from menu settings
		$limit = $app->getParams()->get('cat_all_courses_pagination_limit');
		$this->setState('list.limit', $limit);

		// If menu param is not set then get mainframe limit
		$limit = !empty($limit) ? $limit : $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);
		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);

		if ($limit == 0)
		{
			$this->setState('list.start', 0);
		}
		else
		{
			$this->setState('list.start', $limitstart);
		}

		// Set ordering.
		$orderCol = $app->getUserStateFromRequest($this->context . 'filter_order', 'filter_order');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.id';
		}

		$this->setState('list.ordering', $orderCol);

		// Set ordering direction.
		$listOrder = $app->getUserStateFromRequest($this->context . 'filter_order_Dir', 'filter_order_Dir');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '' )))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);

		// Load the filter state.
		$search = $app->getUserStateFromRequest('com_tjlms' . '.filter.filter_search', 'filter_search', '', 'string');
		$this->setState('com_tjlms' . '.filter.filter_search', $search);

		// Filter mod category.
		$input = $app->input;
		$courseCat = $input->get('course_cat', '', 'VARCHAR');

		if (!$courseCat)
		{
			$courseCat = $app->getUserStateFromRequest('com_tjlms' . '.filter.category_filter', 'category_filter', 0, 'INTEGER');
		}

		if ($courseCat)
		{
			if ($courseCat == 'all')
			{
				$courseCat = '';
			}

			$app->setUserState('com_tjlms' . '.filter.category_filter', $courseCat);
		}

		// Filter mod course type.
		$course_type = $app->getUserStateFromRequest('com_tjlms' . '.filter.course_type', 'course_type', -1, 'INTEGER');
		$this->setState('com_tjlms' . '.filter.course_type', $course_type);

		// Category menu
		$menu_category = $app->getParams()->get('defaultCatId');
		$this->setState('filter.menu_category', $menu_category);

		// Show subcategory prodcuts
		$show_subcat_courses = $app->getParams()->get('show_subcat_courses');
		$this->setState('filter.show_subcat_courses', $show_subcat_courses);

		// Filter store.

		// $store = $app->getUserStateFromRequest($this->context . '.filter.store', 'current_store', '', 'string');

		// $this->setState('filter.store', $store);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$menuParams = new JRegistry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);
		$this->setState('params', $mergedParams);

		// Load the parameters.

		// $params = JComponentHelper::getParams('com_quick2cart');
		// $this->setState('params', $params);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$app = JFactory::getApplication('site');
		$user = JFactory::getuser();
		$userId = $user->id;
		$input = JFactory::getApplication()->input;
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$allowedViewLevels = JAccess::getAuthorisedViewLevels($user->id);
		$implodedViewLevels = implode('","', $allowedViewLevels);
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get('layout', 'default', 'STRING');

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__tjlms_courses` AS a');
		$query->JOIN('INNER', '`#__categories` AS c ON c.id=a.cat_id');

		if ($layout == 'my')
		{
			$query->JOIN('INNER', '`#__tjlms_enrolled_users` AS eu ON eu.course_id=a.id');
			$query->where('eu.state=1');
		}
		elseif ($layout == 'liked')
		{
			$likedQ = $db->getQuery(true);
			$likedQ->select('element_id');
			$likedQ->from('#__jlike_content AS jc');
			$likedQ->JOIN('LEFT', '`#__jlike_likes` AS jl ON jc.id=jl.content_id');
			$likedQ->where("jl.like=1 AND jl.userid='" . $user->id . "'");
			$likedQ->where("jc.element ='com_tjlms.course'");
			$db->setQuery($likedQ);
			$likedCourses = $db->loadColumn();

			$query->where("a.id IN('" . implode(" ',' ", $likedCourses) . "')");
		}
		elseif ($layout == 'recommended')
		{
			$recquery = $db->getQuery(true);

			// Join over the content for content title & url
			$recquery->select('c.id');
			$recquery->from('#__tjlms_courses AS c');
			$recquery->join('LEFT', '#__jlike_content AS content ON content.element_id=c.id');
			$recquery->join('LEFT', '#__jlike_todos AS todo ON todo.content_id = content.id');

			// Join over the created by field 'created_by'
			$recquery->join('INNER', '#__users AS u ON u.id = todo.assigned_by');
			$recquery->where('todo.assigned_to = ' . $userId);
			$recquery->where('todo.type = "reco"');
			$recquery->where('content.element = "com_tjlms.course"');
			$db->setQuery($recquery);
			$recCourses = $db->loadColumn();
			$query->where("a.id IN('" . implode(" ',' ", $recCourses) . "')");
		}

		// Filter by search in title.
		$search = $this->getState('com_tjlms' . '.filter.filter_search');

		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('( a.title LIKE ' . $search . ' )');
		}

		// Filter by course_type .
		$course_type = $this->getState('com_tjlms' . '.filter.course_type');

		if (isset($course_type))
		{
			if ($course_type == 0) // Filter free courses
			{
				$query->where('(a.type = 0)');
			}
			elseif ($course_type == 1) // Filter paid courses
			{
				$query->where('(a.type = 1)');
			}
		}

		// Filter by published state.
		$query->where("a.state = 1");
		$query->where("c.published = 1");

		if ($layout == 'my')
		{
			$query->where('eu.user_id=' . $user->id);

			if ($input->get('assigned', '0', 'INT') == 1)
			{
				$query->where('eu.assigned=1');
			}
		}

		// Filter by category.
		$filter_menu_category = $this->state->get('filter.menu_category');

		$filter_mod_category = $app->getUserStateFromRequest('com_tjlms' . '.filter.category_filter', 'category_filter', 0, 'INTEGER');

		$urlCatId = $input->get('course_cat', '', 'INT');

		// Get courses with repect to access level
		$query->where('a.access IN ("' . $implodedViewLevels . '")');
		$query->where('c.access IN ("' . $implodedViewLevels . '")');

		$catId = '';

		if ($filter_mod_category)
		{
			$catId = $filter_mod_category;
		}
		elseif ($urlCatId)
		{
			$catId = $urlCatId;
		}
		elseif ($filter_menu_category)
		{
			$catId = $filter_menu_category;
		}

		$filter_show_subcat_courses = $this->state->get('filter.show_subcat_courses');

		if ($catId)
		{
			if ($filter_show_subcat_courses)
			{
				$catWhere = $this->getWhereCategory($catId);

				if ($catWhere)
				{
					foreach ($catWhere as $cw)
					{
						$query->where($cw);
					}
				}
			}
			else
			{
				$query->where("a.cat_id = " . $db->escape($catId));
			}
		}

		// Date check
		// Define null and now dates
		$nullDate	= $db->quote($db->getNullDate());
		$nowDate	= $db->quote(JFactory::getDate()->toSql());

		// $query->where("a.start_date < " . $db->quote($this->techjoomlacommon->getDateInLocal(JFactory::getDate())));
		$query->where('(a.start_date = ' . $nullDate . ' OR a.start_date <= ' . $nowDate . ')');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		else
		{
			// Get courses by ordering.
			//$query->order($db->escape('a.ordering') . ' ASC');
			$query->order($db->escape('a.title') . ' ASC');
		}

		$inprogress = $jinput->get('inprogress', 0, 'INT');

		if ($inprogress && $userId)
		{
			$query1 = $db->getQuery(true);
			$query1->select('c.id');
			$query1->from($db->quoteName('#__tjlms_course_track') . ' as ct');
			$query1->join('INNER', $db->quoteName('#__tjlms_courses') . ' as c ON c.id=ct.course_id');
			$query1->where($db->quoteName('ct.user_id') . ' = ' . $userId);
			$query1->where($db->quoteName('ct.status') . '= "C"');
			$query1->where($db->quoteName('c.state') . ' = 1');
			$db->setQuery($query1);
			$totalCompletedCourses = $db->loadcolumn();

			if ($inprogress == -1)
			{
				// Array to string
				$cid_list = '"' . implode('","', $totalCompletedCourses) . '"';
			}
			else
			{
				// Get enrolled course
				$query1 = $db->getQuery(true);
				$query1->select('c.id');
				$query1->from($db->quoteName('#__tjlms_enrolled_users') . ' as u');
				$query1->join('INNER', $db->quoteName('#__tjlms_courses') . ' as c ON c.id=u.course_id');
				$query1->where($db->quoteName('u.user_id') . ' = ' . $userId);
				$query1->where($db->quoteName('u.state') . ' = 1');
				$query1->where($db->quoteName('c.state') . ' = 1');
				$db->setQuery($query1);
				$totalEnrolledCourses = $db->loadcolumn();

				// Get inprogress course
				$inprogress = array_diff($totalEnrolledCourses, $totalCompletedCourses);

				// Array to string
				$cid_list = '"' . implode('","', $inprogress) . '"';
			}

			$query->where('(a.id  IN (' . $cid_list . '))');
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
		$db = $this->getDbo();

		if (!empty($items))
		{
			foreach ($items as $ind => $obj)
			{
				$tjlmsCoursesHelper = new TjlmsCoursesHelper;
				$items[$ind]->cat = $tjlmsCoursesHelper->getCourseCat($obj, 'title');

				$comtjlmsHelper = new comtjlmsHelper;

				if ($obj->type != 0)
				{
					$items[$ind]->price = $tjlmsCoursesHelper->getCourseLowestPrice($obj);
				}

				$enrolled_users_cnt = count($comtjlmsHelper->getCourseEnrolledUsers($obj->id));
				$items[$ind]->enrolled_users_cnt = $enrolled_users_cnt;

				if ($enrolled_users_cnt > 1000)
				{
					$items[$ind]->enrolled_users_cnt = $this->custom_number_format($enrolled_users_cnt);
				}

				$items[$ind]->likesforCourse = $comtjlmsHelper->getLikesForItem($obj->id, 'com_tjlms.course');
			}
		}

		return $items;
	}

	/**
	 * Function to get where conditions with categories
	 *
	 * @param   int  $categoryId  category id
	 *
	 * @return  array where conditions
	 *
	 * @since   1.0
	 */
	public function getWhereCategory($categoryId)
	{
		$db = JFactory::getDBO();
		$where = '';

		if (JVERSION >= '3.0')
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$where[] = 'c.lft >= ' . (int) $lft;
			$where[] = 'c.rgt <= ' . (int) $rgt;
		}
		else
		{
			// Create a subquery for the subcategory list
			$subQuery = $db->getQuery(true);
			$subQuery->select('sub.id');
			$subQuery->from('#__categories as sub');
			$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
			$subQuery->where('this.id = ' . (int) $categoryId);

			/* if ($levels >= 0)
			{
			$subQuery->where('sub.level <= this.level + '.$levels);
			}*/
			$db->setQuery($subQuery);
			$result = $db->loadColumn();

			if ($result)
			{
				$result = implode(',', $result);
				$where[] = ' c.id IN (' . $result . ',' . $categoryId . ')';
			}
			else
			{
				$where[] = ' c.id = ' . $categoryId;
			}
		}

		return $where;
	}

	/**
	 * Function of format number
	 *
	 * @param   int      $n          number
	 * @param   boolean  $precision  precision number to format
	 *
	 * @return  object  list of category
	 *
	 * @since   1.0
	 */
	public function custom_number_format($n, $precision = 1)
	{
		if ($n < 1000000)
		{
			// Anything less than a million
			$n_format = number_format($n / 1000, $precision) . 'K';
		}
		elseif ($n < 1000000000)
		{
			// Anything less than a billion
			$n_format = number_format($n / 1000000, $precision) . 'M';
		}
		else
		{
			// At least a billion
			$n_format = number_format($n / 1000000000, $precision) . 'B';
		}

		return $n_format;
	}

	/**
	 * Fetch list of all categories including child ones
	 *
	 * @param   int      $catid               category id
	 * @param   boolean  $onchangeSubmitForm  option to specify options only
	 * @param   string   $name                name of the extension for which to fetch categories
	 * @param   string   $class               class
	 * @param   boolean  $getOptionsOnly      category id
	 *
	 * @return  object  list of category
	 *
	 * @since   1.0
	 */
	public function getTjlmsCats($catid = '', $onchangeSubmitForm = 1, $name = 'course_cat', $class = '', $getOptionsOnly = 0)
	{
		$filter_menu_category = $this->state->get('filter.menu_category');
		$userId = JFactory::getUser()->id;
		$allowedViewLevels = JAccess::getAuthorisedViewLevels($userId);

		if ($filter_menu_category)
		{
			$implodedViewLevels = implode('","', $allowedViewLevels);

			$catWhere = $this->getWhereCategory($filter_menu_category);
			$db = $this->getDbo();
			$input = JFactory::getApplication()->input;
			$query = $db->getQuery(true);
			$query->select('c.*');
			$query->from('#__categories as c');

			if ($catWhere)
			{
				foreach ($catWhere as $cw)
				{
					$query->where($cw);
				}
			}

			$query->where('c.access IN ("' . $implodedViewLevels . '")');

			$db->setQuery($query);
			$cats_filter = $db->loadobjectList('id');
		}

		$options = array();

		// Static function options($extension, $config = array('filter.published' => array(0,1)))
		$lang = JFactory::getLanguage();
		$tag  = $lang->gettag();

		$lms_cat_options = JHtml::_('category.options',
									'com_tjlms',
									$config = array('filter.published' => array(1), 'filter.language' => array('*', $tag),'filter.access' => $allowedViewLevels)
									);

		$final_cats = array();

		if ($getOptionsOnly == 1)
		{
			return $lms_cat_options;
		}

		$cats = array_merge($options, $lms_cat_options);

		if (!empty($cats))
		{
			/* Durgesh Added */
			$cid = '';

			// Prepare cat id string
			foreach ($cats as $cat)
			{
				$cid .= $cat->value . ',';
			}

			$cat_list = rtrim($cid, ",");

			// Get categories of specific access levels
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('a.id');
			$query->from('#__categories AS a');
			$query->where('a.id IN (' . $cat_list . ')');
			$query->where('a.access IN (' . implode(',', $allowedViewLevels) . ')');
			$db->setQuery($query);
			$result = $db->loadColumn();

			foreach ($cats as $index => $cat)
			{
				if (!in_array($cat->value, $result))
				{
					unset($cats[$index]);
				}
			}

			// If menu option is not set then return all categories.
			if (!isset($cats_filter) && empty($cats_filter))
			{
				/* return $cats*/
				return $cats;
			}
			/* Durgesh Added */

			// If menu option is set then get only those category which are selected
			foreach ($cats_filter as $cat_filter)
			{
				foreach ($cats as $cat)
				{
					if ($cat_filter->id == $cat->value)
					{
						$final_cats[] = $cat;
					}
				}
			}
		}

		return $final_cats;
	}

	/**
	 * Function to get status of categories
	 *
	 * @param   int  $categoryId  category id
	 *
	 * @return  INT
	 *
	 * @since   1.0
	 */
	public function getStatusCategory($categoryId)
	{
		if ($categoryId)
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('published');
			$query->from('#__categories');
			$query->where('id = ' . $categoryId);
			$db->setQuery($query);

			return $givedata = $db->loadResult();
		}
	}
}
