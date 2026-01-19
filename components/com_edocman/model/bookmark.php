<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class EDocmanModelBookmark extends OSModelList
{

	function __construct($config = array())
	{
		$config['table'] = '#__edocman_documents';
		parent::__construct($config);

		$this->state->setDefault('filter_order', 'tbl.title')
			->setDefault('filter_order_Dir', 'asc');
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
        $bookmarkArr = $_COOKIE['bookmark'];
        if($bookmarkArr != "")
        {
            $query->where('tbl.id IN ('.$bookmarkArr.')');
        }
        else
        {
            $query->where('1 = 0');
        }
		return $this;
	}
}