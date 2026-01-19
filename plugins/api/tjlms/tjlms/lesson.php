<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_trading
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * User Api.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_api
 *
 * @since       1.0
 */
class TjlmsApiResourceLesson extends ApiResource
{
	/**
	 * Function get for users record.
	 *
	 * @return void
	 */
	public function get()
	{
		$input = JFactory::getApplication()->input;

		// If we have an id try to fetch the user
		if ($id = $input->get('id'))
		{
			$user = JUser::getInstance($id);

			if (!$user->id)
			{
				$this->plugin->setResponse($this->getErrorResponse(JText::_('PLG_API_USERS_USER_NOT_FOUND_MESSAGE')));

				return;
			}

			$this->plugin->setResponse($user);
		}
		else
		{
			$model = new UsersModelUsers;
			$users = $model->getItems();

			foreach ($users as $k => $v)
			{
				unset($users[$k]->password);
			}

			$this->plugin->setResponse($users);
		}
	}
}
