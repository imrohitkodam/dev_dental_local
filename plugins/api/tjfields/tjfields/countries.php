<?php
/**
 * @package     Tjfields
 * @subpackage  com_tjfields
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class for getting Countries
 *
 * @package  Tjfields
 *
 * @since    _DEPLOY_VERSION_
 */
class TjfieldsApiResourceCountries extends ApiResource
{
	/**
	 *  API Plugin for get method
	 *
	 * @return  void
	 *
	 * @since   _DEPLOY_VERSION_
	 */
	public function post()
	{
		$input           = Factory::getApplication()->input;
		$result          = new stdClass;
		$result->results = array();

		$limitstart = $input->get('limitstart', 0, 'INT');
		$limit      = $input->get('limit', 0, 'INT');

		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/models');
		$countriesModel  = BaseDatabaseModel::getInstance('Countries', 'TjfieldsModel', array('ignore_request' => true));
		$countriesModel->setState('filter.search', $input->get('search', '', 'STRING'));
		$countriesModel->setState('list.start', $limitstart);
		$countriesModel->setState('list.limit', $limit);
		$this->items     = $countriesModel->getItems();
		$result->total   = $countriesModel->getTotal();

		if (empty($this->items))
		{
			$result->empty_message	= Text::_('PLG_API_TJFIELDS_NO_DATA_FOUND');
			$this->plugin->setResponse($result);

			return;
		}

		$result->results = $this->items;
		$this->plugin->setResponse($result);
	}
}
