<?php
/**
 * @package     JLike
 * @subpackage  API Plugin
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Todo time track API class
 *
 * @since  __DEPLOY_VERSION__
 */
class JlikeApiResourceTodoTrack extends ApiResource
{
	/**
	 * Track time against todo
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function post()
	{
		$lang     = Factory::getLanguage();
		$lang->load('com_jlike', JPATH_SITE);

		$input = Factory::getApplication()->input;
		$data = array();

		$data['todo_id']    = $input->get('id', 0, 'INT');
		$data['timestart']  = $input->get('timestart', '', 'DATETIME');
		$data['timeend']    = $input->get('timeend', '', 'DATETIME');
		$data['session_id'] = $input->getString('session_id');
		$data['spent_time'] = $input->get('spent_time', 0, 'INT');

		if (!$data['todo_id'])
		{
			ApiError::raiseError(400, Text::_("PLG_API_JLIKE_TODO_ID_REQUIRED"));
		}

		if (!$data['session_id'])
		{
			ApiError::raiseError(400, Text::_("PLG_API_JLIKE_SESSION_ID_REQUIRED"));
		}

		if (!$data['spent_time'])
		{
			ApiError::raiseError(400, Text::_("PLG_API_JLIKE_SPENT_TIME_REQUIRED"));
		}

		if (!$data['timestart'] || !$data['timeend'])
		{
			ApiError::raiseError(400, Text::_("PLG_API_JLIKE_TIME_START_END_REQUIRED"));
		}

		$response = new stdClass;

		$todoTrackModel = JLike::model('todotrack', array('ignore_request' => true));
		$result = $todoTrackModel->save($data);

		if ($result)
		{
			$response->success = true;
		}
		else
		{
			ApiError::raiseError(500, Text::_("PLG_API_JLIKE_ERROR_STORE_FAILED"));
		}

		$this->plugin->setResponse($response);
	}
}
