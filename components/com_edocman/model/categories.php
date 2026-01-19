<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011-2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class EDocmanModelCategories extends OSModelList
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0);

		//Set default value for states
		$listLength = EDocmanHelper::getConfigValue('number_categories');
		if (!$listLength)
		{
			$listLength = JFactory::getConfig()->get('list_limit');
		}
		$this->state->setDefault('limit', $listLength);
		$this->state->setDefault('filter_order', 'tbl.ordering');
	}

	/**
	 * Method to get categories data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$rows = parent::getData();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row                  = $rows[$i];
				$row->total_documents = EDocmanHelper::countDocuments($row->id);
			}
			$this->data = $rows;
		}

		return $this->data;
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
		$user               = JFactory::getUser();
		$usergroup          = $user->groups;
        $usergroupArr       = array();
        $usergroupSql       = "";
        if(count($usergroup) > 0){
            foreach ($usergroup as $group){
                $usergroupArr[] = " (`groups`='$group' OR `groups` LIKE '$group,%' OR `groups` LIKE '%,$group,%' OR `groups` LIKE '%,$group') AND `data_type` = 0";
            }
            $usergroupSql = implode(" OR ",$usergroupArr);
            $usergroupSql = " tbl.id in (Select item_id from #__edocman_levels where $usergroupSql) ";
            $usergroupSql = " OR (tbl.user_ids = '' AND tbl.accesspicker = '1' AND $usergroupSql ) ";
        }
		$userId = $user->get('id');
		$query->where('tbl.published=1')
			->where('tbl.parent_id=' . $this->state->id);

		//update from version 1.9.10
		if (!$user->authorise('core.admin'))
		{
			$query->where("((tbl.user_ids = '' AND tbl.accesspicker = '0' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
		}
		if ($config->activate_multilingual_feature && JFactory::getApplication()->getLanguageFilter())
		{
			$db = JFactory::getDbo();
			$query->where('tbl.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ', "")');
		}

		return $this;
	}
}