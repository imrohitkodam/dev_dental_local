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
 * Class for getting cities
 *
 * @package  Tjfields
 *
 * @since    _DEPLOY_VERSION_
 */
class TjfieldsApiResourceCities extends ApiResource
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
		$id              = $input->getInt('country_id', 0);
		$result          = new stdClass;
		$result->results = array();

		if (empty($id))
		{
			ApiError::raiseError(400, Text::_("PLG_API_TJFIELDS_INVALID_COUNTRY_ID"));
		}

		$limitstart = $input->get('limitstart', 0, 'INT');
		$limit      = $input->get('limit', 0, 'INT');

		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/models');
		$cities          = BaseDatabaseModel::getInstance('Cities', 'TjfieldsModel', array('ignore_request' => true));
		$cities->setState('filter.country', $id);
		$cities->setState('filter.search', $input->get('search', '', 'STRING'));
		$cities->setState('list.start', $limitstart);
		$cities->setState('list.limit', $limit);
		$this->items     = $cities->getItems();

		if (empty($this->items))
		{
			$result->empty_message	= Text::_('PLG_API_TJFIELDS_NO_DATA_FOUND');
			$this->plugin->setResponse($result);

			return;
		}

		$result->total   = $cities->getTotal();
		$result->results = $this->items;
		$this->plugin->setResponse($result);
	}
}
