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

class EDocmanModelDownloadlogs extends OSModelList
{
	/**
	 * Constructor method, initialize config data for the model and set default model state
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$config['search_fields'] = array('b.title', 'c.username');
		$config['table']         = '#__edocman_statistics';
		parent::__construct($config);

		$this->state->setDefault('filter_order_Dir', 'DESC');
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
		$query->select('tbl.*, b.title, c.username AS downloader_username, c.email AS downloader_email');

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
		$query->join('LEFT', '#__edocman_documents AS b ON tbl.document_id=b.id')
			->join('LEFT', '#__users AS c ON tbl.user_id=c.id');

		return $this;
	}

	protected function _buildQueryGroup(JDatabaseQuery $query)
	{
		$query->group('tbl.download_time');

		return $this;
	}
}
