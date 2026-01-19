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

class ToolbarAdapterMenu
{
	protected $menuType = null;
	public $my = null;
	
	/**
	 * Retrieve the home menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getHomeMenu()
	{

	}

	/**
	 * Preparing the default menu items for the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDefaultMenu()
	{
		$menus = $this->getDefaultMenuItems();

		// do nothing if no default menu 
		if (!$menus) {
			return $menus;
		}

		foreach ($menus as &$item) {
			$menu = new stdClass();
			$menu->id = $item['id'];
			$menu->view = $item['view'];
			$menu->permalink = $item['permalink'];
			$menu->title = $item['title'];

			$item = $menu;
		}

		return $menus;
	}

	/**
	 * Retrieving the default component's toolbar menus.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDefaultMenuItems()
	{

	}

	/**
	 * Building the dropdown menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDropdownMenu()
	{
		return [];
	}

	public function adaptiveEnabled()
	{
		return FDT::config()->get('adaptiveMenu', true);
	}

	public function showHomeButton()
	{
		if (!$this->adaptiveEnabled()) {
			return FDT::config()->get('showHome', true);
		}

		return $this->showHome();
	}
	
	/**
	 * Retrieve the menu type.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getMenuType()
	{
		// If the adaptive menu setting is turned off, we'll use the default menu the entire toolbar.
		if (!$this->adaptiveEnabled()) {
			return 'globalMenu';
		}

		if (is_null($this->menuType)) {
			$this->menuType = str_replace('com_', '', $this->component);
		}

		return $this->menuType;
	}

	/**
	 * Retrieve the component's home.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getHome()
	{
		return $this->home;
	}

	/**
	 * Retrieve the component's name.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComponent()
	{
		return $this->component;
	}

	/**
	 * Preparing dropdown menus.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAvailableDropdownMenu()
	{
		$extensions = FDT::getAvailableExtensions();
		$option = str_replace('com_', '', JFactory::getApplication()->input->get('option'));
		$adapter = FDT::getAdapter($option);

		$current = str_replace('toolbardefault-', '', FDT::config()->get($adapter->getMenu()->getMenuType()));

		$menus = [];

		// Get the current extension's items to show first
		$currentAdapter = FDT::getAdapter($current);
		$menus = array_merge($menus, $currentAdapter->getMenu()->getDropdownMenu());

		foreach ($extensions as $extension) {
			if ($extension === $current) {
				continue;
			}

			$adapter = FDT::getAdapter($extension);

			$menus = array_merge($menus, $adapter->getMenu()->getDropdownMenu());
		}

		if (empty($menus)) {
			return [];
		}

		// Format the first item.
		$first = array_shift($menus);
		$menus = array_merge($first['menus'], $menus);

		return $menus;
	}

	/**
	 * Responsible to retrieve the active menu for the adapter.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getActiveMenu()
	{
		$view = JFactory::getApplication()->input->get('view', '');

		return $view;
	}
}