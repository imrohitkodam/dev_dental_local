<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KunenaLoginEasySocial
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Generates the login url
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getLoginURL()
	{
		return ESR::dashboard();
	}

	/**
	 * Generates the logout url
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getLogoutURL()
	{
		return ESR::dashboard();
	}

	/**
	 * Generates the registration url
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getRegistrationURL()
	{
		$usersConfig = \Joomla\CMS\Component\ComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration')) {
			return ESR::registration();
		}

		return;
	}
}