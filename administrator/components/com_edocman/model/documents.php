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

class EDocmanModelDocuments extends OSModelList
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
        $searchFields = array();
        $searchFields[] = 'tbl.title';
        $searchFields[] = 'tbl.alias';
        $searchFields[] = 'tbl.description';
        $searchFields[] = 'tbl.short_description';
        //$searchFields[] = 't.tag';
        $config['search_fields'] = $searchFields;
		parent::__construct($config);
		$app     = JFactory::getApplication();
		$context = $this->option . '.' . $this->name . '.';
		$this->state->insert('filter_category_id', 'int', $app->getUserStateFromRequest($context . 'filter_category_id', 'filter_category_id', 0, 'int'))
			->insert('function', 'string', 'jSelectEdocman')
			->insert('filter_published', 'string', $app->getUserStateFromRequest($context . 'filter_published', 'filter_published', '', 'string'))
			->insert('filter_full_ordering', 'word')
            ->insert('filter_orphan_state', 'int',$app->getUserStateFromRequest($context . 'filter_orphan_state', 'filter_orphan_state', '', 'int'))
            ->insert('creator', 'int',$app->getUserStateFromRequest($context . 'creator', 'creator', 0, 'int'));
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
		$query->select('tbl.*, ag.title AS access_level, uc.name AS editor, u.username, cat.title AS category_title ');

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
		$query->join('LEFT', '#__users AS u ON u.id=tbl.created_user_id')
			->join('LEFT', '#__users AS uc ON uc.id=tbl.checked_out')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = tbl.access')
			->join('LEFT', '#__edocman_document_category AS dc ON (tbl.id = dc.document_id AND dc.is_main_category=1)')
			->join('LEFT', '#__edocman_categories AS cat ON dc.category_id = cat.id');
            //->join('LEFT', '#__edocman_document_tags as td on td.document_id = tbl.id');
            //->join('LEFT', '#__edocman_tags as t on td.tag_id = t.id');

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
			$query->where('dc.category_id=' . (int) $this->state->filter_category_id);
		}

		if (is_numeric($this->state->filter_published))
		{
			$query->where('tbl.published=' . (int) $this->state->filter_published);
		}
        if ($this->state->filter_orphan_state > 0)
        {
            if($this->state->filter_orphan_state == 1)
            {
                $query->where('tbl.id not in (Select document_id from #__edocman_document_category) or tbl.id in (Select document_id from #__edocman_document_category where category_id not in (Select id from #__edocman_categories))');
            }
        }
		$user = JFactory::getUser();
		$userId = $user->id;
		$app = JFactory::getApplication();
		if(!$app->isAdmin())
		{
			$query->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id='".$userId."' AND tbl.created_user_id > 0))");
		}

		if($this->state->filter_search != "")
		{
			//$query->orWhere("tbl.id in (Select a.document_id from #__edocman_document_tags as a inner join #__edocman_tags as b on a.tag_id = b.id where b.tag like '%".$this->state->filter_search."%')","or");
			$db		  = JFactory::getDbo();
			$tag      = strtolower(trim($this->state->filter_tag));
			$subQuery = $db->getQuery(true);
			$subQuery->select('id')
				->from('#__edocman_tags')
				->where('LOWER(`tag`) = ' . $db->quote($tag));
			$db->setQuery($subQuery);
			$tagId = (int) $db->loadResult();
			if($tagId > 0){
				$query->where(' tbl.id IN (SELECT document_id FROM #__edocman_document_tags WHERE tag_id=' . $tagId . ')');
			}
		}

		if($this->state->creator > 0)
        {
            $query->where('tbl.created_user_id = '.$this->state->creator);
        }

		return $this;
	}
}
