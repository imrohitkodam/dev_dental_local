<?php
/**
 * @package     Joomla.API.Plugin
 * @subpackage  com_tjlms-API
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.user.helper');

/**
 * API Plugin
 *
 * @package     Joomla_API_Plugin
 * @subpackage  com_tjlms-API-EnrollUser
 * @since       1.0
 */
class TjLmsApiResourceEnroll extends ApiResource
{
	/**
	 * API Plugin for post method
	 *
	 * @return  avoid.
	 */
	public function post()
	{
		$input = JFactory::getApplication()->input;
		$data = array();
		$result = new stdClass;
		$result->err_code = '';
		$result->err_message = '';
		$result->data = new stdClass;

		$data['course_id'] = $input->get('course_id', '0', 'int');
		$data['user_id'] = $input->get('user_id', '', 'int');
		$data['state'] = $input->get('state', '1', 'int');
		$data['due_date'] = $input->get('due_date', '0', 'string');
		$data['notify_user'] = $input->get('notify_user', '0', 'int');

		JLoader::import('components.com_tjlms.models.enrolment', JPATH_SITE);
		$model  = JModelLegacy::getInstance('enrolment', 'TjlmsModel');

		if ($model->userEnrollment($data))
		{
			$result->data->results = $data;
			$this->plugin->setResponse($result);

			return $result;
		}

		$result->err_code = $model->getErrorCode();
		$result->err_message = $model->getError();
		$result->data->results = '';
		$this->plugin->setResponse($result);

		return $result;
	}
}
