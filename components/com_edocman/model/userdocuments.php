<?php
/**
 * @version        1.11.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class EDocmanModelUserdocuments extends OSModelList
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
		$query->where('tbl.created_user_id = ' . (int) JFactory::getUser()->get('id'));

		return $this;
	}
}