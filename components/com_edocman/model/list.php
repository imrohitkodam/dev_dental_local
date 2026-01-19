<?php
/**
 * @version        1.9.13
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class EDocmanModelList extends OSModelList
{
	/**
	 * Include documents from children categories or not
	 *
	 * @var bool
	 */
	private $includeChildrenCategory = false;

	function __construct($config = array())
	{
		$config['table'] = '#__edocman_documents';
        $searchFields = array();
        $searchFields[] = 'tbl.title';
        $searchFields[] = 'tbl.alias';
        $searchFields[] = 'tbl.description';
        $searchFields[] = 'tbl.short_description';
       // $searchFields[] = 't.tag';
        $config['search_fields'] = $searchFields;
		parent::__construct($config);

		// Insert state variables
		$this->state->insert('id', 'int', 0)
			->insert('filter_category_ids', 'string', '')
			->insert('filter_categories_ids', 'string', '')
			->insert('filter_category_id', 'int', 0)
			->insert('filter_search', 'string', '')
			->insert('filter_filetype','string','')
			->insert('filter_datetype','string','created')
			->insert('filter_startdate','string','')
            ->insert('filter_created_user','int',0)
            ->insert('document_id','int',0)
            ->insert('filter_related_documents','int',0)
			->insert('filter_enddate','string','');

		// Set default value for model states in case it is set in Configuration of the component
		$componentConfig = EDocmanHelper::getConfig();
		if ($componentConfig->number_documents)
		{
			$this->state->setDefault('limit', $componentConfig->number_documents);
		}

		if ($componentConfig->default_sort_option)
		{
			$this->state->setDefault('filter_order', $componentConfig->default_sort_option);
		}

		if ($componentConfig->default_sort_direction)
		{
			$this->state->setDefault('filter_order_Dir', $componentConfig->default_sort_direction);
		}
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
        //$query->join('LEFT', '#__edocman_document_tags as td on td.document_id = tbl.id')
         //   ->join('LEFT', '#__edocman_tags as t on td.tag_id = t.id');
        return $this;
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
		$now = JFactory::getDate()->toSql();
		$query->select(array('tbl.*', "DATEDIFF('$now', created_time) AS number_created_days", "DATEDIFF('$now', modified_time) AS number_updated_days"));

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
		$config             = EDocmanHelper::getConfig();
		$db                 = JFactory::getDbo();
		$user               = JFactory::getUser();
        $usergroup          = $user->groups;
        $usergroupArr       = array();
        $usergroupSql       = "";
        if(count($usergroup) > 0){
            foreach ($usergroup as $group){
                $usergroupArr[] = " (`groups`='$group' OR `groups` LIKE '$group,%' OR `groups` LIKE '%,$group,%' OR `groups` LIKE '%,$group') AND `data_type` = '1'";
            }
            $usergroupSql = implode(" OR ",$usergroupArr);
            $usergroupSql = " tbl.id in (Select item_id from #__edocman_levels where $usergroupSql) ";
            $usergroupSql = " OR (tbl.user_ids = '' AND tbl.accesspicker = '1' AND $usergroupSql ) ";
        }

		$userId = $user->get('id');

		//Users with super admin permission should be able to see all documents
		if (!$user->authorise('core.admin', 'com_edocman'))
		{
			//update with Group IDs
			if($config->user_group_ids){
				$groupQuery = EdocmanHelper::buildUserGroupQuery('tbl.owner_group_ids');
				$query->where('tbl.published=1')
					->where("((tbl.user_ids = '' AND tbl.owner_group_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql ".$groupQuery." OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
			}else{
				$query->where('tbl.published=1')
					->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
			}
		}

		//$categoryId = $this->state->id ? $this->state->id : $this->state->filter_category_id;
		if ($this->state->filter_category_id)
		{
            $categoryId = $this->state->filter_category_id;
            $categoryIds = array();
            $categoryIds[] = $categoryId;
            $categoryIds = EDocmanHelper::getAllChildrenCategories($categoryIds);
            if($config->search_with_sub_cats) {
                $query->where('tbl.id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id IN(' . implode(',', $categoryIds) . '))');
            }else{
                $query->where('tbl.id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id = ' . $categoryId . ' )');
            }
		}

		if($this->state->filter_created_user){
		    $created_user = $this->filter->created_user;
		    if($created_user > 0){
		        $query->where('tbl.created_user_id = "'.$created_user.'"');
            }
        }

        if($this->state->filter_related_documents == 1 && $this->state->document_id > 0){
		    $query->where('tbl.id <> "'.$this->state->document_id.'"');
        }

        if($this->state->id){
            $query->where('tbl.id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id = ' . $this->state->id . ' )');
        }

		if ($this->state->filter_category_ids)
		{
			$categoryIds = explode(',', $this->state->filter_category_ids);
            \Joomla\Utilities\ArrayHelper::toInteger($categoryIds);
			if ($this->includeChildrenCategory)
			{
				$categoryIds = EDocmanHelper::getAllChildrenCategories($categoryIds);
			}
			$query->where('tbl.id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id IN(' . implode(',', $categoryIds) . '))');
		}

		if ($this->state->filter_categories_ids)
		{
			$categoryIds = explode(',', $this->state->filter_categories_ids);
            \Joomla\Utilities\ArrayHelper::toInteger($categoryIds);
			$query->where('tbl.id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id IN(' . implode(',', $categoryIds) . ') group by document_id having count(document_id) = "'.count($categoryIds).'")');
		}

		if ($this->state->filter_search)
		{
			$search		= $db->quote('%' . $this->state->filter_search . '%');

			$search1	= strtolower(trim($this->state->filter_search));
			$subQuery	= $db->getQuery(true);
			$subQuery->select('id')
				->from('#__edocman_tags')
				->where('LOWER(`tag`) like ' . $db->quote($search1));
			$db->setQuery($subQuery);
			$tagId = (int) $db->loadResult();
			if($tagId > 0){
				$subSql = ' OR ( tbl.id IN (SELECT document_id FROM #__edocman_document_tags WHERE tag_id=' . $tagId . '))';
			}else{
				$subSql = '';
			}


			$query->where(" (tbl.title LIKE $search OR tbl.description LIKE $search OR tbl.short_description LIKE $search OR tbl.indexed_content LIKE $search) ".$subSql);
		}

		if($this->state->filter_datetype){
			switch($this->state->filter_datetype){
				case "created":
					if($this->state->filter_startdate){
						$this->state->filter_startdate = strtolower(trim($this->state->filter_startdate));
						$query->where(" tbl.created_time >= '".$this->state->filter_startdate."'");
					}

					if($this->state->filter_enddate){
						$this->state->filter_enddate = strtolower(trim($this->state->filter_enddate));
						$query->where(" tbl.created_time <= '".$this->state->filter_enddate."'");
					}
				break;
				case "modified":
					if($this->state->filter_startdate){
						$this->state->filter_startdate = strtolower(trim($this->state->filter_startdate));
						$query->where(" tbl.modified_time >= '".$this->state->filter_startdate."'");
					}

					if($this->state->filter_enddate){
						$this->state->filter_enddate = strtolower(trim($this->state->filter_enddate));
						$query->where(" tbl.modified_time <= '".$this->state->filter_enddate."'");
					}
				break;
				case "publish":
					if($this->state->filter_startdate){
						$this->state->filter_startdate = strtolower(trim($this->state->filter_startdate));
						$query->where(" tbl.publish_up >= '".$this->state->filter_startdate."'");
					}

					if($this->state->filter_enddate){
						$this->state->filter_enddate = strtolower(trim($this->state->filter_enddate));
						$query->where(" tbl.publish_down <= '".$this->state->filter_enddate."'");
					}
				break;
			}
		}
		if ($this->state->filter_tag)
		{
			$tag      = strtolower(trim($this->state->filter_tag));
			$subQuery = $db->getQuery(true);
			$subQuery->select('id')
				->from('#__edocman_tags')
				->where('LOWER(`tag`) like ' . $db->quote($tag));
			$db->setQuery($subQuery);
			$tagId = (int) $db->loadResult();
			if($tagId > 0){
				$query->where(' tbl.id IN (SELECT document_id FROM #__edocman_document_tags WHERE tag_id=' . $tagId . ')');
			}
		}

		if($this->state->filter_filetype){
			$filter_filetypes = $this->state->filter_filetype;
			$filter_filetypes = explode("-",$filter_filetypes);
			$tempSql = array();
			foreach($filter_filetypes  as $filetype){
				$tempSql[] = "(tbl.original_filename like '%.".$filetype."')";
			}
			$tempSql = implode(" or ",$tempSql);
			$query->where($tempSql);
		}

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_edocman')) && (!$user->authorise('core.edit', 'com_edocman')))
		{
			$nullDate = $db->quote($db->getNullDate());
			$nowDate  = $db->quote(JFactory::getDate()->toSql());
			$query->where('(tbl.publish_up = ' . $nullDate . ' OR tbl.publish_up <= ' . $nowDate . ')')
				->where('(tbl.publish_down = ' . $nullDate . ' OR tbl.publish_down >= ' . $nowDate . ')');
		}

		if ($config->activate_multilingual_feature && JFactory::getApplication()->getLanguageFilter())
		{
			$query->where('tbl.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ', "")');
		}
	//	echo $query->__toString();die();
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


	public function setIncludeChildren($includeChildrenCategory)
	{
		$this->includeChildrenCategory = $includeChildrenCategory;
	}

    /**
     * Builds a generic ORDER BY clause based on the model's state
     *
     * @param JDatabaseQuery $query
     *
     * @return $this
     */
    public function _buildQueryOrder(JDatabaseQuery $query)
    {
        $sort      = $this->state->filter_order;
        $direction = strtoupper($this->state->filter_order_Dir);
        if ($this->state->id) {
            $categoryId     = $this->state->id;
            $category       = EDocmanHelper::getCategory($categoryId);
            $sort_option    = $category->sort_option;
            $sort_direction = $category->sort_direction;
            if($sort_option != "")
            {
                $sort       = $sort_option;
            }
            if($sort_direction != "")
            {
                $direction  = strtoupper($sort_direction);
            }
        }
        if ($sort)
        {
            $query->order($sort . ' ' . $direction);
        }

        return $this;
    }
}