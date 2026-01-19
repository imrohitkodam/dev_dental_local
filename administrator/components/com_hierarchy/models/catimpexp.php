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
class HierarchyModelCatimpexp extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * 
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			// #Nothing
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
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Filtering user_id
		$this->setState('filter.user_id', $app->getUserStateFromRequest($this->context . '.filter.user_id', 'filter_user_id', '', 'string'));

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hierarchy');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
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
	 * @since   1.6
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
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.id AS subuserId, a.name'
			)
		);
		$query->from('`#__users` AS a');

		// Join over the user field 'user_id'
		$query->select('hu.user_id AS bossId, hu.subuser_id AS empId');
		$query->join('LEFT', '#__hierarchy_users AS hu ON hu.subuser_id = a.id');

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
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.name LIKE ' . $search . ' )');
			}
		}

		// Filtering user_id
		$filter_user_id = $this->state->get("filter.user_id");

		if ($filter_user_id)
		{
			$query->where("a.id = '" . $db->escape($filter_user_id) . "'");
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
	 * Method to get a list.
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
	 * Import csv data for category save in database.
	 *
	 * @param   array  $eachCategory  csv file data.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function importCategoryCSVdata($categoryData)
	{
		// #load categories model file as it is
		
		//~ print_r($categoryData);
		//~ die;

		// JLoader::import( 'category', JPATH_ADMINISTRATOR .'/components/com_categories/models' );
		require_once JPATH_ADMINISTRATOR . '/components/com_categories/models/category.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_categories/tables/category.php';
		$categoryModel = new CategoriesModelCategory;

		$db = $this->getDbo();
		$catId = 0;
		$i = 0;

		foreach ($categoryData as $eachCategory => $val)
		{
			foreach ($val as $key => $value)
			{
				if ($key == 'Id')
				{
					$catId = $value;
				}

				if ($key == 'Category Name')
				{
					$catNM = $value;
				}

				if ($key == 'ParentId')
				{
					$ParentId = $value;
				}
				
				if ($ParentId == '')
				{
					$ParentId = 1;
				}

				if ($catNM != '' && $ParentId != '')
				{
					$core = array();
					$core['core.create'] = array (6 => '1', 3 => '1');
					$core['core.delete'] = array (6 => '1');
					$core['core.edit'] = array (6 => '1', 4 => '1');
					$core['core.edit.state'] = array (6 => '1', 5 => '1');
					$core['core.edit.own'] = array (6 => '1', 3 => '1');

					$insert_obj['id'] = $catId;
					$insert_obj['parent_id'] = $ParentId;
					$insert_obj['extension'] = 'com_jticketing';
					$insert_obj['title'] = $catNM;
					$insert_obj['published'] = 1;
					$insert_obj['access'] = 1;
					$insert_obj['language'] = '*';

					$insert_obj['hits'] = 0;

					$insert_obj['alias'] = '';
					$insert_obj['version_note'] = ''; 
					$insert_obj['note'] = ''; 
					$insert_obj['description'] = ''; 

					$insert_obj['metadesc'] = '';
					$insert_obj['metakey'] = '';
					$insert_obj['created_user_id'] = '';
					$insert_obj['created_time'] = '';
					$insert_obj['modified_user_id'] = '';
					$insert_obj['modified_time'] = '';

					$insert_obj['rules'] = $core;
					$insert_obj['params'] = array ('category_layout' => '', 'image' =>'', 'image_alt' =>'');
					$insert_obj['metadata'] = array ('author' =>'', 'robots' =>'');
					$insert_obj['tags'] = '';

					// $categoryImport           = $categoryModel->save($insert_obj);
				}
			}
		}
		die;
	}

	/**
	 * categoryexit.
	 *
	 * @param   integer  $id  id
	 * 
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function categoryexit($id)
	{
		$catId = '';

		if ($id)
		{
			$db = $this->getDbo();
			$query = "SELECT id FROM #__categories WHERE id =" . $id;
			$db->setQuery($query);
			$catId = $db->loadResult();
		}

		return $catId;
	}
}
