<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansK2item extends PPPlugins
{
	public function onK2PrepareContent($item, $params, $limitstart)
	{
		$args = [&$item, &$params, $limitstart];
		return PPHelperApp::trigger('onK2PrepareContent', $args);
	}

	/**
	 * Redirect user on login
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onUserLogin($user, $options = [])
	{
		$my = JUser::getInstance();
		$id = intval(JUserHelper::getUserId($user['username']));
		
		if ($id) {
			$session = JFactory::getSession();
			$url = $session->get('k2_item_redirect_url', "");

			if ($url != "") {
				$session->clear('k2_item_redirect_url');
				JFactory::getApplication()->setUserState('users.login.form.return', $url);
				return true;
			}
		}
		
	}
}

