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

class ToolbarMenuEasysocial extends ToolbarAdapterMenu
{
	protected $component = 'com_easysocial';
	protected $view = 'dashboard';

	public function __construct()
	{
		$this->my = ES::user();
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
		$menu->permalink = ESR::dashboard();

		return $menu;
	}

	/**
	 * Determine if the component version is compatible with the function or not
	 *
	 * @since	1.0.7
	 * @access	public
	 */
	public function isVersionCompatible($requiredVersion)
	{
		static $exists = null;

		if (is_null($exists)) {
			$version = ES::getLocalVersion();
			$exist = false;
			
			if (version_compare($version, $requiredVersion, '>=')) {
				$exists = true;
			}
		}

		return $exists;
	}	

	/**
	 * Building the dropdown menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDropdownMenu()
	{
		if (!$this->my->isLoggedIn()) {
			return [];
		}

		$social = [];
		$items = [];

		if ($this->my->hasCommunityAccess()) {
			$items = [
				'MOD_SI_TOOLBAR_ACCOUNT' => [
					'icon' => 'fdi fas fa-user-circle', 
					'menus' => $this->account()
				],
				'MOD_SI_TOOLBAR_DISCOVER' => [
					'icon' => 'fdi far fa-compass', 
					'menus' => $this->discover()
				],
				'MOD_SI_TOOLBAR_MANAGE' => [
					'icon' => 'fdi fa fa-cog', 
					'menus' => $this->manage()
				],
			];

			if ($this->isVersionCompatible('3.3.0')) {
				if ($this->my->canCreateAds()) {
					$items['MOD_SI_TOOLBAR_ADVERTISE'] = [
						'icon' => 'fdi fa fa-ad', 
						'menus' => $this->advertise()
					];
				}
			}

			$app = JFactory::getApplication();
			$input = $app->input;
			$option = $input->get('option', '', 'string');

			if ($option !== $this->component) {
				$include = true;

				if (!in_array($option, FDT::getExtensions()) && FDT::getMainComponent() === $this->component) {
					$include = false;
				}

				if ($include) {
					$social = $this->social();
				}
			}
		}

		return ['MOD_SI_TOOLBAR_EASYSOCIAL' => [
				'icon' => 'fdi fas fa-users', 
				'menus' => array_merge($social, $items),
			]
		];
	}

	public function social()
	{
		$menus = $this->getDefaultMenuItems();

		$social = [];
		$social['MOD_SI_TOOLBAR_DASHBOARD'] = [
			'icon' => 'fdi fa fa-home',
			'link' => ESR::dashboard(),
			'config' => 'es_layout_home'
		];

		foreach ($menus as $menu) {
			$social[$menu['title']] = [
				'icon' => $menu['icon'],
				'link' => $menu['permalink'],
			];
		}

		return $social;
	}

	public function account()
	{
		$account = [];
		$config = ES::config();

		if ($this->my->hasCommunityAccess()) {
			$account['MOD_SI_TOOLBAR_PROFILE_VIEW_YOUR_PROFILE'] = [
				'icon' => 'fdi fas fa-user-circle', 
				'link' => $this->my->getPermalink(),
			];

			$account['MOD_SI_TOOLBAR_ACCOUNT_SETTINGS'] = [
				'icon' => 'fdi far fa-edit', 
				'link' => ESR::profile(['layout' => 'edit']),
			];

			if ($config->get('friends.enabled')) {
				$account['MOD_SI_TOOLBAR_MY_FRIENDS'] = [
					'icon' => 'fdi fa fa-user-friends', 
					'link' => ESR::friends()
				];
			}

			if ($config->get('followers.enabled')) {
				$account['MOD_SI_TOOLBAR_MY_FOLLOWERS'] = [
					'icon' => 'fdi fa fa-users',
					'link' => ESR::followers()
				];
			}

			$verification = ES::verification();

			if ($this->my->id && ($verification->canRequest($this->my->id, SOCIAL_TYPE_USER))) {
				$account['MOD_SI_TOOLBAR_SUBMIT_VERIFICATION'] = [
					'icon' => 'fdi fa fa-user-check',
					'link' => ESR::verifications(['layout' => 'request'])
				];
			}

			if ($config->get('friends.invites.enabled')) {
				$account['MOD_SI_TOOLBAR_INVITE_FRIENDS'] = [
					'icon' => 'fdi far fa-envelope',
					'link' => ESR::friends(['layout' => 'invite'])
				];
			}

			if ($config->get('badges.enabled')) {
				$account['MOD_SI_TOOLBAR_PROFILE_ACHIEVEMENTS'] = [
					'icon' => 'fdi fa fa-trophy',
					'link' => ESR::badges(['layout' => 'achievements'])
				];
			}

			if ($config->get('points.enabled')) {
				$account['MOD_SI_TOOLBAR_PROFILE_POINTS_HISTORY'] = [
					'icon' => 'fdi fa fa-star',
					'link' => ESR::points(['layout' => 'history' , 'userid' => $this->my->getAlias()])
				];
			}

			if ($config->get('conversations.enabled')) {
				$account['MOD_SI_TOOLBAR_PROFILE_CONVERSATIONS'] = [
					'icon' => 'fdi fa fa-comments',
					'link' => ESR::conversations()
				];
			}
		}

		return $account;
	}

	public function discover()
	{
		$discover = [];
		$config = ES::config();

		if (FDT::config()->get('es_dropdown_discover_people', true)) {
			$discover['MOD_SI_TOOLBAR_PEOPLE'] = [
				'icon' => 'fdi fa fa-user-friends',
				'link' => ESR::users()
			];
		}

		$discover['MOD_SI_TOOLBAR_ADVANCED_SEARCH'] = [
			'icon' => 'fdi fa fa-search',
			'link' => ESR::search(array('layout' => 'advanced'))
		];

		if ($config->get('points.enabled')) {
			$discover['MOD_SI_TOOLBAR_LEADERBOARD'] = [
				'icon' => 'fdi fa fa-chart-line',
				'link' => ESR::leaderboard()
			];
		}

		if ($config->get('apps.browser')) {
			$discover['MOD_SI_TOOLBAR_APPS'] = [
				'icon' => 'fdi fa fa-box-open',
				'link' => ESR::apps()
			];
		}

		return $discover;
	}

	public function advertise()
	{
		$advertise = [];

		if ($this->my->hasAdvertiserAccount()) {
			$advertise['MOD_SI_TOOLBAR_MANAGE_AD_ACCOUNT'] = [
				'icon' => 'fdi fa fa-ad', 
				'link' => ESR::advertiser(['layout' => 'form'])
			];

			$advertise['MOD_SI_TOOLBAR_MANAGE_ADS'] = [
				'icon' => 'fdi fa fa-address-card', 
				'link' => ESR::ads()
			];
		} else {
			$advertise['MOD_SI_TOOLBAR_CREATE_ADVERTISER_ACCOUNT'] = [
				'icon' => 'fdi fa fa-address-card', 
				'link' => ESR::advertiser(['layout' => 'form'])
			];
		}

		return $advertise;
	}

	public function manage()
	{
		$preference = [];
		$config = ES::config();

		if ($config->get('privacy.enabled')) {
			$preference['MOD_SI_TOOLBAR_MANAGE_PRIVACY'] = [
				'icon' => 'fdi fa fa-shield-alt', 
				'link' => ESR::profile(['layout' => 'editPrivacy'])
			];
		}

		$preference['MOD_SI_TOOLBAR_MANAGE_ALERTS'] = [
			'icon' => 'fdi fa fa-bell', 
			'link' => ESR::profile(['layout' => 'editNotifications'])
		];

		if ($config->get('activity.logs.enabled')) {
			$preference['MOD_SI_TOOLBAR_PROFILE_ACTIVITIES'] = [
				'icon' => 'fdi fa fa-list', 
				'link' => ESR::activities()
			];
		}

		if ($this->my->isSiteAdmin() || $this->my->getAccess()->get('pendings.manage')) {
			if ($config->get('groups.enabled')) {
				$preference['MOD_SI_TOOLBAR_PENDING_GROUPS'] = [
					'icon' => 'fdi fa fa-users', 
					'link' => ESR::manage(['layout' => 'clusters', 'filter' => 'group'])
				];
			}

			if ($config->get('events.enabled')) {
				$preference['MOD_SI_TOOLBAR_PENDING_EVENTS'] = [
					'icon' => 'fdi far fa-calendar-alt', 
					'link' => ESR::manage(['layout' => 'clusters', 'filter' => 'event'])
				];
			}

			if ($config->get('pages.enabled')) {
				$preference['MOD_SI_TOOLBAR_PENDING_PAGES'] = [
					'icon' => 'fdi fa fa-briefcase', 
					'link' => ESR::manage(['layout' => 'clusters', 'filter' => 'page'])
				];
			}
		}

		return $preference;
	}

	public function getDefaultMenuItems()
	{
		$availableMenus = [
			[
				'id' => 'pages',
				'view' => 'pages',
				'icon' => 'fdi fa fa-briefcase',
				'config' => 'pages.enabled',
				'title' => 'MOD_SI_TOOLBAR_PROFILE_PAGES',
				'permalink' => ESR::pages(),
			],
			[
				'id' => 'groups',
				'view' => 'groups',
				'icon' => 'fdi fa fa-users',
				'config' => 'groups.enabled',
				'title' => 'MOD_SI_TOOLBAR_PROFILE_GROUPS',
				'permalink' => ESR::groups(),
			],
			[
				'id' => 'events',
				'view' => 'events',
				'icon' => 'fdi far fa-calendar-alt',
				'config' => 'events.enabled',
				'title' => 'MOD_SI_TOOLBAR_PROFILE_EVENTS',
				'permalink' => ESR::events(),
			],
			[
				'id' => 'videos',
				'view' => 'videos',
				'icon' => 'fdi fab fa-youtube',
				'config' => 'video.enabled',
				'title' => 'MOD_SI_TOOLBAR_VIDEOS',
				'permalink' => ESR::videos(),
			],
			[
				'id' => 'audio',
				'view' => 'audios',
				'icon' => 'fdi fa fa-music',
				'config' => 'audio.enabled',
				'title' => 'MOD_SI_TOOLBAR_AUDIOS',
				'permalink' => ESR::audios(),
			],
			[
				'id' => 'photos',
				'view' => 'albums',
				'icon' => 'fdi fa fa-image',
				'config' => 'photos.enabled',
				'title' => 'MOD_SI_TOOLBAR_PROFILE_PHOTOS',
				'permalink' => ESR::albums(),
			],
			[
				'id' => 'polls',
				'view' => 'polls',
				'icon' => 'fdi fa fa-chart-pie',
				'config' => 'polls.enabled',
				'title' => 'MOD_SI_TOOLBAR_POLLS',
				'permalink' => ESR::polls(),
			]
		];

		if ($this->isVersionCompatible('4.0.0')) {
			$availableMenus[] = [
				'id' => 'marketplace',
				'view' => 'marketplaces',
				'icon' => 'fdi fa fa-store',
				'config' => 'marketplaces.enabled',
				'title' => 'MOD_SI_TOOLBAR_MARKETPLACE',
				'permalink' => ESR::marketplaces()
			];
		}

		$fdConfig = FDT::config();
		$config = ES::config();
		$defaultMenus = [];

		foreach ($availableMenus as $menu) {
			if (!$fdConfig->get('es_layout_' . $menu['id'], true) || !$config->get($menu['config'])) {
				continue;
			}

			$defaultMenus[] = $menu;
		}

		return $defaultMenus;
	}

	public function showHome()
	{
		return FDT::config()->get('es_layout_home', true);
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
		return ESR::profile(['layout' => 'edit']);
	}
}
