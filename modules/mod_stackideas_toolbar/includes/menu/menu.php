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

class ToolbarMenu
{
	public $adapter = null;
	public $menutype = null;

	public function __construct()
	{
		$component = JFactory::getApplication()->input->get('option');
		$this->adapter = FDT::getAdapter($component);

		$this->menutype = $this->getMenuType();
	}

	/**
	 * Responsible to prepare the output.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function render()
	{
		$responsive = (FH::responsive()->isMobile() || FH::responsive()->isTablet()) ? true : false;

		$themes = FDT::themes();
		$options = [
			'menus' => $responsive ? [] : $this->getMenus(),
			'active' => $responsive ? 0 : $this->getActive(),
			'home' => $this->getHome(),
		];

		return $themes->output('menu/default', $options);
	}

	/**
	 * Retrieve menus that needs to be rendered in the toolbar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getMenus()
	{
		static $menus = [];

		if (!isset($menus[$this->menutype])) {
			if ($this->menutype === 'default') {
				$items = $this->adapter->getMenu()->getDefaultMenu();
			}

			if ($this->menutype !== 'default') {
				$app = JFactory::getApplication();
				$items = $app->getMenu()->getItems('menutype', $this->menutype);

				if ($items) {
					// Format the menu items from Joomla.
					foreach($items as $key => &$item) {
						$params = $item->getParams();

						// Exclude item with menu item option set to exclude from menu modules.
						if ($params->get('menu_show', 1) == 0) {
							unset($items[$key]);
							continue;
						}

						$item->flink  = $item->link;

						if ($item->type === 'url') {
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {

								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link . '&Itemid=' . $item->id;
							}
						}

						if ($item->type === 'alias') {
							$item->flink = 'index.php?Itemid=' . $params->get('aliasoptions');

							// Get the language of the target menu item when site is multilingual
							if (JLanguageMultilang::isEnabled()) {
								$newItem = $app->getMenu()->getItem((int) $params->get('aliasoptions'));

								// Use language code if not set to ALL
								if ($newItem != null && $newItem->language && $newItem->language !== '*') {
									$item->flink .= '&lang=' . $newItem->language;
								}
							}
						}

						if (!in_array($item->type, ['url', 'alias'])) {
							$item->flink = 'index.php?Itemid=' . $item->id;
						}

						$item->flink = JRoute::_($item->flink);

						if ((strpos($item->flink, 'index.php?') !== false) && strcasecmp(substr($item->flink, 0, 4), 'http')) {
							$item->flink = JRoute::_($item->flink, true, $params->get('secure'));
						}

						$menu = new stdClass();
						$menu->id = $item->id;
						$menu->title = $item->title;
						$menu->permalink = $item->type === 'separator' ? 'javascript:void(0);' : $item->flink;
						$item = $menu;
					}
				}
			}

			$menus[$this->menutype] = $this->format($items);
		}

		return $menus[$this->menutype];
	}

	/**
	 * Retrieve the component menu types with respect to the setting.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getMenuType()
	{
		// Retrieve the menu type for the current page.
		$menutype = FDT::config()->get($this->adapter->getMenu()->getMenuType());

		// Check whether the default component menu selected.
		// e.g:
		// toolbardefault-easyblog
		// toolbardefault-easydiscuss
		// toolbardefault-easysocial
		// toolbardefault-easysocial
		// Other would be Joomla menutype.
		if (stristr($menutype, 'toolbardefault') !== false) {
			$types = explode('-', $menutype);
			$component = $types[1];

			// Here we'll reset the adapter to respect the menu setting.
			$this->adapter = FDT::getAdapter($component);
			return 'default';
		}

		return $menutype;
	}

	/**
	 * Format menu items to 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function format($items)
	{
		// Set 0 for no truncation.
		$limit = (int) FDT::config()->get('truncateMenu', 5);

		// Nothing to be truncated
		if ($limit === 0) {
			return (object) [
				'visible' => $items,
				'hidden' => []
			];
		}

		$result = (object) [
			'visible' => [],
			'hidden' => []
		];

		// Do nothing if no menu items
		if (!$items) {
			return $result;
		}

		foreach ($items as $key => &$item) {
			if ($key < $limit) {
				$result->visible[] = $item;
				continue;
			}

			$result->hidden[] = $item;
		}

		return $result;
	}

	/**
	 * Retrieve the Joomla active menu object.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getActive()
	{
		// If default component menu is selected, we'll let the adapter to handle this.
		if ($this->menutype === 'default') {
			return $this->adapter->getMenu()->getActiveMenu();
		}

		$app = JFactory::getApplication();
		$activeMenu = $app->getMenu()->getActive();
		$activeId = 0;

		if (is_object($activeMenu)) {
			$activeId = $activeMenu->id;
		}

		return $activeId;
	}

	/**
	 * Retrieve the component's home button.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getHome()
	{
		if (!$this->adapter->getMenu()->showHomeButton()) {
			return false;
		}

		$home = $this->adapter->getMenu()->getHomeMenu();

		if (!is_object($home)) {
			throw new Exception('Unable to load home menu for ' . $this->adapter->getComponent() . ' for StackIdeas Toolbar');
		}

		return $home;
	}
}
