<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ToolbarDropdown
{
	public $adapter = null;

	public function __construct()
	{
		$this->adapter = FDT::getAdapter(JFactory::getApplication()->input->get('option'));
	}

	/**
	 * Responsible to render user dropdown menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function user($args = [])
	{
		$my = JFactory::getUser();

		if ($my->guest) {
			return $this->guest();
		}

		if (!$this->adapter->showUserDropdown()) {
			return;
		}

		if (FH::responsive()->isMobile()) {
			return;
		}

		// Get adapter for main component to render user avatar and all
		$adapter = FDT::getAdapter(FDT::getMainComponent());

		$user = $adapter->getUser($my->id);

		$options = [
			'user' => $user,
			'hasCover' => $adapter->hasCover(),
			'badges' => $adapter->getBadges(),
			'showVerified' => $adapter->showVerified(),
			'showProfileMeta' => $adapter->showProfileMeta(),
			'profileMeta' => $adapter->getProfileMeta(),
			'permaLink' => method_exists($user, 'getPermalink') ? $user->getPermalink() : ''
		];

		$themes = FDT::themes();
		$output = $themes->output('dropdown/user', $options);

		return $output;
	}

	/**
	 * Responsible to render guest login form.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function guest()
	{
		if (JFactory::getUser()->guest && !$this->adapter->showUserLogin()) {
			return false;
		}

		// The login functionality will be inherited from the main component.
		$adapter = FDT::getAdapter(FDT::getMainComponent());

		$options = [
			'hasTwoFactor' => FH::hasTwoFactor(),
			'registrationLink' => $adapter->getRegistrationLink(),
			'isRegistrationEnabled' => $adapter->isRegistrationEnabled(),
			'usernameField' => $adapter->getUsernamePlaceholder(),
			'returnUrl' => $adapter->getReturnUrl(),
			'remindUsernameLink' => $adapter->getRemindUsernameLink(),
			'resetPasswordLink' => $adapter->getResetPasswordLink(),
			'jfbconnect' => $adapter->jfbconnect(),
		];

		$themes = FDT::themes();
		$output = $themes->output('dropdown/guest', $options);

		return $output;
	}

	/**
	 * Responsible to render menus for mobile view.
	 *
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function responsive()
	{
		if (!FH::responsive()->isMobile() && !FH::responsive()->isTablet()) {
			return '';
		}

		if (!$this->adapter->showUserDropdown()) {
			return '';
		}

		$menuLib = FDT::menu();

		$args = [
			'user' => JFactory::getUser(),
			'home' => $menuLib->getHome(),
			'menus' => $menuLib->getMenus(),
			'sections' => $this->adapter->getMenu()->getAvailableDropdownMenu()
		];

		$themes = FDT::themes();
		$output = $themes->output('dropdown/responsive', $args);

		return $output;
	}

	/**
	 * IMPORTANT: This function is call recursively.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function menu($args = [], $debug = false)
	{
		$args['menus'] = FH::normalize($args, 'menus', $this->adapter->getMenu()->getAvailableDropdownMenu());

		$themes = FDT::themes();
		$output = $themes->output('dropdown/menu', $args);

		return $output;
	}
}