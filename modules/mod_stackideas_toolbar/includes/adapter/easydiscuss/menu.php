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

class ToolbarMenuEasydiscuss extends ToolbarAdapterMenu
{
	protected $component = 'com_easydiscuss';
	protected $view = 'index';

	public function __construct()
	{
		$this->my = ED::user();

		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
	}

	/**
	 * Provide the home menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getHomeMenu()
	{
		$menu = new stdClass();
		$menu->id = $this->view;
		$menu->permalink = EDR::_('view=' . FDT::config()->get('ed_home_menu', 'index'));

		return $menu;
	}

	/**
	 * Building the dropdown menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDropdownMenu()
	{
		if (!$this->my->id) {
			return [];
		}

		$menus = [
			'MOD_SI_TOOLBAR_ACCOUNT' => [
				'icon' => 'fdi fas fa-user-circle', 
				'menus' => $this->account()
			],
			'MOD_SI_TOOLBAR_MANAGE' => [
				'icon' => 'fdi fa fa-cog', 
				'menus' => $this->manage()
			]
		];

		$discuss = [];
		$homeMenuLayout = FDT::config()->get('ed_home_menu', 'index');
		$firstKey = $homeMenuLayout === 'index' ? 'MOD_SI_TOOLBAR_RECENT' : 'MOD_SI_TOOLBAR_FORUMS';
		$firstKeyValue = FDT::config()->get('ed_home_menu', 'index') === 'index' ? [
			'icon' => 'fdi fa fa-ticket-alt',
			'link' => EDR::_('view=index'),
			'config' => 'ed_layout_home'
		] : [
			'icon' => 'fdi far fa-comment-dots',
			'link' => EDR::_('view=forums'),
			'config' => 'ed_layout_home'
		];

		$discuss[$firstKey] = $firstKeyValue;

		$app = JFactory::getApplication();
		$input = $app->input;
		$option = $input->get('option', '', 'string');

		if ($option !== $this->component) {
			$include = true;

			if (!in_array($option, FDT::getExtensions()) && FDT::getMainComponent() === $this->component) {
				$include = false;
			}

			if ($include) {
				$discuss = array_merge($discuss, $this->discuss());
			}
		}

		$config = ED::config();

		if ($config->get('main_favorite')) {
			$discuss['MOD_SI_TOOLBAR_MY_FAVOURITES'] = [
				'icon' => 'fdi far fa-heart', 
				'link' => EDR::_('view=favourites')
			];
		}

		$discuss['MOD_SI_TOOLBAR_MY_POSTS'] = [
			'icon' => 'fdi fas fa-file-alt',
			'link' => EDR::_('view=mypost')
		];

		if ($config->get('main_postassignment') && ED::isModerator()) {
			$discuss['MOD_SI_TOOLBAR_MY_ASSIGNED_POSTS'] = [
				'icon' => 'fdi fa fa-table',
				'link' => EDR::_('view=assigned')
			];
		}

		return ['MOD_SI_TOOLBAR_EASYDISCUSS' => [
				'icon' => 'fdi fas fa-comment-alt',
				'menus' => array_merge($discuss, $menus)
			]
		];
	}

	/**
	 * The account section for the user dropdown
	 *
	 * @since	1.0.2
	 * @access	public
	 */
	public function account()
	{
		$account = [
			'MOD_SI_TOOLBAR_MY_PROFILE' => [
				'icon' => 'fdi fas fa-user-circle', 
				'link' => $this->my->getPermalink()
			],
			'MOD_SI_TOOLBAR_EDIT_PROFILE' => [
				'icon' => 'fdi far fa-edit', 
				'link' => $this->my->getEditProfileLink()
			],
		];

		return $account;
	}

	public function discuss()
	{
		$defaultMenus = $this->getDefaultMenuItems();
		$discuss = [];

		foreach ($defaultMenus as $menu) {
			$discuss[$menu['title']] = [
				'icon' => $menu['icon'],
				'link' => $menu['permalink'],
			];
		}

		return $discuss;
	}

	public function manage()
	{
		$acl = ED::acl();
		$config = ED::config();

		$manage['MOD_SI_TOOLBAR_MY_SUBSCRIPTION'] = [
			'icon' => 'fdi fa fa-bell', 
			'link' => EDR::_('view=subscription')
		];

		if ($acl->allowed('manage_pending') || ED::isSiteAdmin()) {
			$manage['MOD_SI_TOOLBAR_MANAGE_SITE'] = [
				'icon' => 'fdi fa fa-cog', 
				'link' => EDR::_('view=dashboard')
			];
		}

		return $manage;
	}

	public function getDefaultMenuItems()
	{
		$defaultMenus = [];
		$fdConfig = FDT::config();
		$config = ED::config();

		$availableViews = [
			'categories' => [
				'id' => 'categories',
				'view' => 'categories',
				'icon' => 'fdi fa fa-list-alt',
				'config' => 'ed_layout_categories',
				'title' => 'MOD_SI_TOOLBAR_CATEGORIES',
				'permalink' => EDR::_('view=categories')
			],
			'tags' => [
				'id' => 'tags',
				'view' => 'tags',
				'icon' => 'fdi fa fa-tags',
				'config' => 'ed_layout_tags',
				'title' => 'MOD_SI_TOOLBAR_TAGS',
				'permalink' => EDR::_('view=tags')
			],
			'badges' => [
				'id' => 'badges',
				'view' => 'badges',
				'icon' => 'fdi fa fa-certificate',
				'config' => 'ed_layout_badges',
				'title' => 'MOD_SI_TOOLBAR_BADGES',
				'permalink' => EDR::_('view=badges')
			]
		];

		$edConfigs = [
			'tags' => 'main_master_tags',
			'badges' => 'main_badges'
		];

		foreach ($availableViews as $view => $menu) {
			if (!$fdConfig->get($menu['config']) || (in_array($view, array_keys($edConfigs)) && !$config->get($edConfigs[$view]))) {
				continue;
			}

			$defaultMenus[] = $menu;
		}

		$esLib = ED::easysocial();

		if ($fdConfig->get('ed_layout_users') && $this->showUserMenu()) {
			$menu = [
				'id' => 'users',
				'view' => 'users',
				'icon' => 'fdi fa fa-users',
				'permalink' => EDR::_('view=users'),
				'title' => 'MOD_SI_TOOLBAR_USERS'
			];

			if ($esLib->exists() && ED::config()->get('integration_easysocial_members')) {
				$menu['permalink'] = ESR::users();
			}

			$defaultMenus[] = $menu;
		}

		if (method_exists($esLib, 'isClusterAppExists')) {
			$clusters = ['group', 'page', 'event'];
			$icons = [
				'group' => 'fa fa-users',
				'page' => 'fa fa-briefcase',
				'event' => 'fa far fa-calendar-alt'
			];

			$currentView = JFactory::getApplication()->input->get('view');
			$activeCluster = JFactory::getApplication()->input->get('cluster_type');

			$isClusterActive = $currentView === 'clusters' && $activeCluster;

			foreach ($clusters as $cluster) {
				$exist = $esLib->isClusterAppExists($cluster);

				$isActive = $isClusterActive && $activeCluster === $cluster;

				if ($exist) {
					$menu = [
						'id' => $isActive ? 'clusters' : 'clusters_inactive',
						'view' => 'clusters',
						'icon' => 'fdi ' . $icons[$cluster],
						'permalink' => EDR::_('view=clusters&cluster_type=' . $cluster),
						'title' => 'MOD_SI_TOOLBAR_' . strtoupper($cluster) . 'S'
					];

					$defaultMenus[] = $menu;
				}
			}
		}

		return $defaultMenus;
	}


	public function showUserMenu()
	{
		$edConfig = ED::config();

		$hasEnabledMainUserListing = $edConfig->get('main_user_listings');
		$hasEnabledAccessProflePublic = $edConfig->get('main_profile_public');

		// Do not render this if main user listing setting disabled
		if (!$hasEnabledMainUserListing) {
			return false;
		}

		// Do not render this if public user unable to access user profile page
		if (!$this->my->id && !$hasEnabledAccessProflePublic) {
			return false;
		}

		return true;
	}

	public function showHome()
	{
		return FDT::config()->get('ed_layout_home', true);
	}

	/**
	 * Retrieve the link of the current user's profile
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getProfileLink()
	{
		return $this->my->getPermalink();
	}

	/**
	 * Retrieve the link of the current user's edit profile
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getEditProfileLink()
	{
		return $this->my->getEditProfileLink();
	}
}
