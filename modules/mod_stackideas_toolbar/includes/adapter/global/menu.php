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

require_once(dirname(__DIR__) . '/menu.php');

class ToolbarMenuGlobal extends ToolbarAdapterMenu
{
	protected $component = 'global';

	// Based on params.
	protected $menuType = 'globalMenu';

	protected $home = '';

	/**
	 * Provide the home menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getHomeMenu()
	{
		$app = JFactory::getApplication();
		$defaultMenuitem = $app->getMenu()->getDefault();

		$menu = new stdClass();
		$menu->permalink = $defaultMenuitem->flink;
		$menu->id = $defaultMenuitem->id;

		return $menu;
	}

	public function showHome()
	{
		return FDT::config()->get('showHome', true);
	}

	/**
	 * Retrieve the link of the current user's profile
	 *
	 * NOTE: For global, just point back to Joomla's user profile page
	 * 
	 * @since	1.0.0
	 * @access	public
	 */
	public function getProfileLink()
	{
		$isEnabled = JComponentHelper::isEnabled('com_users');

		if (!$isEnabled) {
			return '';
		}

		return JRoute::_('index.php?option=com_users&view=profile');
	}

	/**
	 * Retrieve the link of the current user's edit profile
	 *
	 * NOTE: For global, just point back to Joomla's user edit profile page
	 * 
	 * @since	1.0.0
	 * @access	public
	 */
	public function getEditProfileLink()
	{
		$isEnabled = JComponentHelper::isEnabled('com_users');

		if (!$isEnabled) {
			return '';
		}

		return JRoute::_('index.php?option=com_users&view=profile&layout=edit');
	}
}