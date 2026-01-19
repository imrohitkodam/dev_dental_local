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

class plgPayplansMenuAccess extends PPPlugins
{
	/**
	 * Triggered when PayPlans is rendered
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSystemStart()
	{
		$user = PP::user();

		if (PP::isFromAdmin() || $user->isAdmin()) {
			return true;
		}

		$path = __DIR__ . '/app/joomla';

		// JLoader::register('JMenu', $path . '/menu.php');
		// JLoader::register('JAbstractJ35Menu', $path . '/abstract/j35/menu.php');
		// JLoader::register('JAbstractJ35MenuSite', $path . '/abstract/j35/menu/site.php');
		JLoader::register('JMenuSite', $path . '/menu/site.php');
		
		// JLoader::registerAlias('JMenuSite', $path . '/menu/site.php', '5.0');

		// class_exists("JMenu", true);

		return true;
	}

	/**
	 * Triggered by Joomla system events
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterRoute()
	{
		$option = $this->input->get('option', '', 'default');
		$task = $this->input->get('task', '', 'cmd');
		$view = $this->input->get('view', '', 'cmd');

		// No restrictions on logins
		if ($option === 'com_users' && !empty($task) && ($task === 'user.logout' || $task === 'user.login')) {
			return true;
		}

		// no restriction on com users on login / password reset 
		if ($option === 'com_users' && !empty($view) && ($view === 'login' || $view === 'reset' || $view === 'remind')) {
			return true;
		}

		$user = PP::user();

		// Nothing to do on Admin-end or when user is super user.
		if (PP::isFromAdmin() || $user->isAdmin()) {
			return true;
		}
		
		$redirected = $this->input->get('redirected', '');

		// Already Redirected then bypass.
		if ($redirected == 1) {
			$this->input->set('redirected', false);
			return true;
		}
		
		// Get a list of menuaccess apps
		$apps = $this->getAvailableApps();

		// Skip this if there no any menuaccess app
		if (!$apps) {
			return true;
		}

		$config = PP::config();
		$helper = $this->getAppHelper();

		// parse the request url
		$uri = clone(JUri::getInstance());
		$router = $this->app->getRouter();

		$currentUrlQueryString = $router->parse($uri);

		// Format the URL query string if that is zoo pages
		$currentUrlQueryString = $helper->zoo($currentUrlQueryString);

		if (isset($currentUrlQueryString['format'])) {
			unset($currentUrlQueryString['format']);
		}

		$menu = $this->app->getMenu();
		
		// Get All Restricted Menus
		$appMenu = [];
		$menuAccessRestrictedPlans = [];
		$allowedMenus = [];
		$userPlans = $user->getPlans(PP_SUBSCRIPTION_ACTIVE);

		$userPlanIds = [];

		if ($userPlans) {
			foreach ($userPlans as $userPlan) {
				$userPlanIds[] = $userPlan->getId();
			}
		}

		// A list of menuaccess apps 
		foreach ($apps as $appId => $app) {

			$allowedMenus = $app->getAppParam('allowedMenus', []);
			$allowedMenus = is_array($allowedMenus) ? $allowedMenus : [$allowedMenus];

			// Load the restricted menu item from the app
			foreach ($allowedMenus as $menuId) {

				$menuItem = $menu->getItem($menuId);

				// Skip this if the menu doesn't exist
				if (!$menuItem) {
					continue;
				}
				
				// If there is no menus in the link, we need to generate it
				if (!strpos($menuItem->link, '&id')) {
					// $params = $menuItem->params;
					$params = $menuItem->getParams();

					if ($params->get('item_id')) {
						$menuItem->link = $menuItem->link . "&id=" . $params->get('item_id');
					}
				}

				$language = '';

				if ($menuItem->language != '*') {
					$language = explode('-', $menuItem->language);
					$language = '&lang=' . $language[0];

				} else {
					
					// If in the menu it is set to all,language filter plugin is enabled
					// and user's site is multilangual. Then remove the language parameter.
					if (isset($currentUrlQueryString['lang'])) {
						unset($currentUrlQueryString['lang']);
					}
				}

				// Separate out all the elements from the restricted menu query string
				$menuItemQueryString = JUri::getInstance($menuItem->link . $language)->getQuery(true);

				// Merge the menu item id into this menu query string
				$menuItemQueryString = array_merge(['Itemid' => $menuItem->id], $menuItemQueryString);

				// Count how many query string segment for the current URL and the menu
				$totalOfMenuItemQueryString = count($menuItemQueryString);
				$totalOfCurrentUrlQueryString = count($currentUrlQueryString);

				$isMatchedRestrictedMenuItemId = false;

				// System will restricted if both menu item id is the same
				if (isset($menuItemQueryString['Itemid']) && isset($currentUrlQueryString['Itemid']) && ($menuItemQueryString['Itemid'] == $currentUrlQueryString['Itemid'])) {
					$isMatchedRestrictedMenuItemId = true;
				}


				// Only process this if both segment count is the same 
				// And if both menu item id is matched		
				if (($totalOfMenuItemQueryString == $totalOfCurrentUrlQueryString) || ($isMatchedRestrictedMenuItemId)) {

					// it will return menu item key and value if the current URL query string does not match
					$hasDifferentURLStatement = array_diff_assoc($menuItemQueryString, $currentUrlQueryString);

					// Process this if computes the difference of arrays does not return anything mean it definitely matched
					// Or if both menu item id are matched
					if (!$hasDifferentURLStatement || ($isMatchedRestrictedMenuItemId)) {

						$applyAll = $app->getParam('applyAll', 0);
						$restrictedPlans = $app->getPlans();
	
						if ($applyAll) {
							$restrictedPlans = PPHelperPlan::getPlans(['published' => 1], false);
						}

						// Merge all the different menuaccess app restricted plans
						$menuAccessRestrictedPlans = array_merge($menuAccessRestrictedPlans, $restrictedPlans); 
					}
				}				
			}
		}

		// Check for the all the plans which are added with applicable menu(restricted menu)
		// and allow user only if he has one of those plan
		if ($menuAccessRestrictedPlans && !array_intersect($userPlanIds, $menuAccessRestrictedPlans)) {

			$renderAs404 = $config->get('show404error');

			if (!PP::isFromAdmin() && !$renderAs404) {
				PP::info()->set('COM_PAYPLANS_APP_MENUACCESS_SUBSCRIPTION_EXPIRATION_MESSAGE', 'error', 'PP_MENUACCESS');

				$redirect = PPR::_('index.php?option=com_payplans&view=plan', false);

				return PP::redirect($redirect);
			}

			// Throw a 404 error if set in setting
			throw new Exception(JText::_('COM_PAYPLANS_APP_MENUACCESS_PAGE_NOT_FOUND_MESSAGE'), '404');
		}
		
		return true;
	}

	/**
	 * Restrict menu items from being accessed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansMenusLoad(&$menus)
	{
		// Don't do anything at the back end
		if (PP::isFromAdmin()) {
			return true;
		}

		$apps = $this->getAvailableApps();

		if (!$apps) {
			return;
		}

		$user = PP::user();

		if ($user->isAdmin()) {
			return true;
		}

		// Get user plans
		$userPlans = $user->getPlans(PP_SUBSCRIPTION_ACTIVE);

		$userPlanIds = [];
		if ($userPlans) {
			foreach ($userPlans as $userPlan) {
				$userPlanIds[] = $userPlan->getId();
			}
		}

		$config = PP::config();

		//step 2:- select those menus which we want to display or hide according to app
		$display = [];
		$hidden = [];

		foreach ($apps as $appId => $app) {
			$applyAll = $app->getParam('applyAll', 0);

			$allowedMenus = $app->getAppParam('allowedMenus', []);
			$allowedMenus = is_array($allowedMenus) ? $allowedMenus : [$allowedMenus];
			$appPlans = $app->getPlans();

			if ($applyAll) {
				$appPlans = PPHelperPlan::getPlans(['published' => 1], false);
			}

			if (array_intersect($userPlanIds, $appPlans)) {
				$display = array_merge($display, $allowedMenus);
			} else {
				$hidden = array_merge($hidden, $allowedMenus);
			}
			
		}

		// Step 3:- remove allowed menus from hide list
		$hidden = array_diff($hidden, $display);

		//step 4:- remove menus which are in hide list 
		// check show menus to user or not
		$showMenu = $config->get('showOrhide');

		if (!$showMenu) {
			foreach ($hidden as $hiddenMenu) {
				if (isset($menus[$hiddenMenu])) {
					$menus[$hiddenMenu]->access = 0;
				}
			}
		}
	}
}
