<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EdocmanModelcategories extends OSModelList
{
	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        OSModelList
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$app     = JFactory::getApplication();
		$context = $this->option . '.' . $this->name . '.';
		$this->state->insert('filter_category_id', 'int', $app->getUserStateFromRequest($context . 'filter_category_id', 'filter_category_id', 0, 'int'))
			->insert('filter_level', 'int', $app->getUserStateFromRequest($context . 'filter_level', 'filter_level', 0, 'int'))
			->insert('filter_full_ordering', 'word');
	}


	/**
	 * Get a list of items
	 *
	 * @return array
	 */
	public function getData()
	{
		if (empty($this->data))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$this->_buildQueryColumns($query)
				->_buildQueryFrom($query)
				->_buildQueryJoins($query)
				->_buildQueryWhere($query)
				->_buildQueryGroup($query)
				->_buildQueryOrder($query);

			$db->setQuery($query);
			$rows     = $db->loadObjectList();
			$children = array();
			// first pass - collect children
			if (count($rows))
			{
				foreach ($rows as $v)
				{
					$pt   = $v->parent_id;
					$list = @$children[$pt] ? $children[$pt] : array();
					array_push($list, $v);
					$children[$pt] = $list;
				}
			}
			if($this->state->filter_category_id > 0){
				$list  = JHtml::_('menu.treerecurse', $this->state->filter_category_id, '', array(), $children, 9999);
			}elseif($this->state->filter_category_id == 0 && $this->state->filter_search == ''){
				$list  = JHtml::_('menu.treerecurse', $this->state->filter_category_id, '', array(), $children, 9999);
			}else{
				$keys  = array_keys($children);
				$count = count($children);
				$temp  = array();
				$list  = array();
				$prgarr= array();
				foreach($keys as $key){
					$temp = EdocmanHelper::treerecurse($key, '', array() , $children , $prgarr , 9999);
					$list = array_merge($list,$temp);
				}
			}
			$total = count($list);
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($total, $this->state->limitstart, $this->state->limit);
			// slice out elements based on limits
			$this->data = array_slice($list, $this->pagination->limitstart, $this->pagination->limit);

		}

		return $this->data;
	}

	/**
	 * Builds SELECT columns list for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function _buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*, uc.name AS editor, vl.title AS access_level, COUNT(b.id) AS total_documents');

		return $this;
	}

	/**
	 * Builds JOIN clauses for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function _buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__users AS uc ON uc.id=tbl.checked_out')
			->leftJoin('#__viewlevels AS vl ON tbl.access = vl.id')
			->leftJoin('#__edocman_document_category AS b ON (tbl.id = b.category_id AND b.is_main_category = 1)');

		return $this;
	}

	/**
	 * Builds WHERE clauses for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function _buildQueryWhere(JDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if ($this->state->filter_category_id)
		{
			$query->where('tbl.parent_id =' . (int) $this->state->filter_category_id);
		}

		if ($this->state->filter_level)
		{
			$query->where('tbl.level <=' . (int) $this->state->filter_level);
		}

		return $this;
	}

	/**
	 * Builds GROUP BY clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function _buildQueryGroup(JDatabaseQuery $query)
	{
		$query->group('tbl.id');

		return $this;
	}

}