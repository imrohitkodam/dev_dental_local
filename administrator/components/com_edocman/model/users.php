<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class EDocmanModelUsers extends OSModelList
{

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     OSModelList
	 */
	public function __construct($config = array())
	{
		$config['table']         = '#__users';
		$config['search_fields'] = array('tbl.username', 'tbl.name', 'tbl.email');
		parent::__construct($config);
		$this->state->insert('filter_group_id', 'int', 0)
			->insert('filter_order', 'cmd', 'tbl.name')
			->insert('field', 'cmd', 'user_id');
	}

	/**
	 * Method to get list of users
	 *
	 * @return array
	 */
	public function getData()
	{
		if (empty($this->data))
		{
			$rows = parent::getData();
			if (count($rows))
			{
				foreach ($rows as $row)
				{
					$row->group_names = $this->_getUserDisplayedGroups($row->id);
				}
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
		parent::_buildQueryWhere($query);

		if ($this->state->filter_group_id)
		{
			$query->where('tbl.id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id=' . (int) $this->state->filter_group_id . ')');
		}

		$query->where('tbl.block = 0');

		return $this;
	}

	/**
	 * Get name of the group which users belong to
	 *
	 * @param int $userId
	 *
	 * @return string
	 */
	function _getUserDisplayedGroups($userId)
	{
		$db    = $this->getDbo();
		$query = "SELECT title FROM " . $db->quoteName('#__usergroups') . " ug left join " .
			$db->quoteName('#__user_usergroup_map') . " map on (ug.id = map.group_id)" .
			" WHERE map.user_id=" . $userId;

		$db->setQuery($query);
		$result = $db->loadColumn();

		return implode("\n", $result);
	}
}
