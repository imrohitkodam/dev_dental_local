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

class ToolbarMenuEasyblog extends ToolbarAdapterMenu
{
	protected $component = 'com_easyblog';
	protected $view = 'latest';

	public function __construct()
	{
		$this->my = EB::user(JFactory::getUser()->id);

		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
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
		$menu->permalink = EBR::_('index.php?option=com_easyblog');

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
			return [] ;
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

		$blog = [];
		$app = JFactory::getApplication();
		$input = $app->input;
		$option = $input->get('option', '', 'string');

		if ($option !== $this->component) {
			$include = true;

			if (!in_array($option, FDT::getExtensions()) && FDT::getMainComponent() === $this->component) {
				$include = false;
			}

			if ($include) {
				$blog = $this->blog();
			}
		}

		return ['MOD_SI_TOOLBAR_EASYBLOG' => [
				'icon' => 'fdi fas fa-newspaper',
				'menus' => array_merge($blog, $menus)
			]
		];
	}

	public function blog()
	{
		$menus = [];

		$fdConfig = FDT::config();

		$items = [
			'MOD_SI_TOOLBAR_LATEST_PAGE_TITLE' => [
				'icon' => 'fdi far fa-file-alt',
				'link' => EBR::_('index.php?option=com_easyblog'),
				'config' => 'eb_layout_home'
			],
			'MOD_SI_TOOLBAR_BLOGGERS' => [
				'icon' => 'fdi fa fa-users',
				'link' => EBR::_('index.php?option=com_easyblog&view=blogger'),
				'config' => 'eb_layout_bloggers'
			],
			'MOD_SI_TOOLBAR_CATEGORIES' => [
				'icon' => 'fdi fa fa-list-alt',
				'link' => EBR::_('index.php?option=com_easyblog&view=categories'),
				'config' => 'eb_layout_categories'
			],
			'MOD_SI_TOOLBAR_TAGS' => [
				'icon' => 'fdi fa fa-tags',
				'link' => EBR::_('index.php?option=com_easyblog&view=tags'),
				'config' => 'eb_layout_tags'
			],
			'MOD_SI_TOOLBAR_ARCHIVES' => [
				'icon' => 'fdi fa fa-archive',
				'link' => EBR::_('index.php?option=com_easyblog&view=archive'),
				'config' => 'eb_layout_archives'
			]
		];

		foreach ($items as $key => $item) {
			if (!$fdConfig->get($item['config'])) {
				continue;
			}

			$menus[$key] = $item;
		}
 
		return $menus;
	}

	public function manage()
	{
		$manage = [];
		$acl = EB::acl();
		$config = EB::config();

		if ($acl->get('add_entry'))  {
			$manage['MOD_SI_TOOLBAR_DASHBOARD_TOOLBAR_OVERVIEW'] = [
				'icon' => 'fdi fa fa-tachometer-alt',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard')
			];

			$manage['MOD_SI_TOOLBAR_TOOLBAR_DRAFTS'] = [
				'icon' => 'fdi fas fa-file-alt',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=drafts')
			];

			$manage['MOD_SI_TOOLBAR_MANAGE_POSTS'] = [
				'icon' => 'fdi far fa-file-alt',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries')
			];
		}

		if ($acl->get('create_post_templates')) {
			$manage['MOD_SI_TOOLBAR_DASHBOARD_HEADING_POST_TEMPLATES'] = [
				'icon' => 'fdi fa fa-file',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates')
			];
		}

		if (FH::isSiteAdmin() || ($acl->get('moderate_entry') || ($acl->get('manage_pending') && $acl->get('publish_entry')))) {
			$manage['MOD_SI_TOOLBAR_MANAGE_PENDING'] = [
				'icon' => 'fdi fa fa-share-square',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate')
			];
		}

		if (FH::isSiteAdmin() || ($acl->get('moderate_entry') || ($acl->get('manage_pending') && $acl->get('publish_entry')))) {
			$manage['MOD_SI_TOOLBAR_REPORT_POSTS'] = [
				'icon' => 'fdi fa fa-exclamation-triangle',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=reports')
			];
		}

		if ($acl->get('manage_comment') && EB::comment()->isBuiltin()) {
			$manage['MOD_SI_TOOLBAR_MANAGE_COMMENTS'] = [
				'icon' => 'fdi fa fa-comments',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=comments')
			];
		}

		if ($acl->get('polls_manage')) {
			$manage['MOD_SI_TOOLBAR_TOOLBAR_MANAGE_POLLS'] = [
				'icon' => 'fdi fas fa-poll-h', 
				'link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=polls')
			];
		}

		if ($acl->get('create_category')) {
			$manage['MOD_SI_TOOLBAR_MANAGE_CATEGORIES'] = [
				'icon' => 'fdi fa fa-list-alt',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=categories')
			];
		}

		if ($acl->get('create_tag')) {
			$manage['MOD_SI_TOOLBAR_MANAGE_TAGS'] = [
				'icon' => 'fdi fa fa-tags',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags')
			];
		}

		if ($config->get('main_favourite_post')) {
			$manage['MOD_SI_TOOLBAR_FAVOURITE_POSTS'] = [
				'icon' => 'fdi fa fa-bookmark', 
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=favourites')
			];
		}

		if ($acl->get('create_team_blog')) {
			$manage['MOD_SI_TOOLBAR_TEAMBLOGS'] = [
				'icon' => 'fdi fa fa-users',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs')
			];
		}
		
		if ((EB::isTeamAdmin() || FH::isSiteAdmin()) && $acl->get('create_team_blog')) {
			$manage['MOD_SI_TOOLBAR_TEAM_REQUESTS'] = [
				'icon' => 'fdi fa fa-user-edit', 
				'link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests')
			];
		}

		return $manage;
	}

	public function account()
	{
		$account = [];
		$acl = EB::acl();
		$config = EB::config();

		$account['MOD_SI_TOOLBAR_EDIT_PROFILE'] = [
			'icon' => 'fdi far fa-edit', 
			'link' => EB::getEditProfileLink()
		];

		if ($acl->get('allow_subscription')) {
			$account['MOD_SI_TOOLBAR_MANAGE_SUBSCRIPTIONS'] = [
				'icon' => 'fdi fa fa-bell',
				'link' => EB::_('index.php?option=com_easyblog&view=subscription')
			];
		}

		if (($config->get('integrations_twitter') && $config->get('integrations_twitter_centralized_and_own')) || ($config->get('integrations_linkedin') && $config->get('integrations_linkedin_centralized_and_own'))) {
			$account['MOD_SI_TOOLBAR_AUTOPOSTING'] = [
				'icon' => 'fdi fa fa-share-square',
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting')
			];
		}

		if ($config->get('gdpr_enabled') && $config->get('integrations_easysocial_editprofile') && EB::easysocial()->exists()) {
			$account['MOD_SI_TOOLBAR_GDPR_DOWNLOAD_INFORMATION'] = [
				'icon' => 'fdi fa fa-user-shield', 
				'link' => 'javascript:void(0);',
				'attributes' => 'data-fd-toolbar-dropdown-item data-gdpr-download-link'
			];
		}

		return $account;
	}

	public function getDefaultMenuItems()
	{
		$defaultMenus = [];
		$fdConfig = FDT::config();

		$availableViews = [
			'categories' => [
				'id' => 'categories',
				'view' => 'categories',
				'config' => 'eb_layout_categories',
				'title' => 'MOD_SI_TOOLBAR_CATEGORIES',
				'permalink' => EBR::_('index.php?option=com_easyblog&view=categories')
			],
			'tags' => [
				'id' => 'tags',
				'view' => 'tags',
				'config' => 'eb_layout_tags',
				'title' => 'MOD_SI_TOOLBAR_TAGS',
				'permalink' => EBR::_('index.php?option=com_easyblog&view=tags')
			],
			'bloggers' => [
				'id' => 'bloggers',
				'view' => 'bloggers',
				'config' => 'eb_layout_bloggers',
				'title' => 'MOD_SI_TOOLBAR_BLOGGERS',
				'permalink' => EBR::_('index.php?option=com_easyblog&view=blogger')
			],
			'teamblogs' => [
				'id' => 'teamblogs',
				'view' => 'teamblogs',
				'config' => 'eb_layout_teamblogs',
				'title' => 'MOD_SI_TOOLBAR_TEAMBLOGS',
				'permalink' => EBR::_('index.php?option=com_easyblog&view=teamblog')
			],
			'archives' => [
				'id' => 'archives',
				'view' => 'archives',
				'config' => 'eb_layout_archives',
				'title' => 'MOD_SI_TOOLBAR_ARCHIVES',
				'permalink' => EBR::_('index.php?option=com_easyblog&view=archive')
			],
			'calendar' => [
				'id' => 'calendar',
				'view' => 'calendar',
				'config' => 'eb_layout_calendar',
				'title' => 'MOD_SI_TOOLBAR_CALENDAR',
				'permalink' => EBR::_('index.php?option=com_easyblog&view=calendar')
			]
		];

		foreach ($availableViews as $view) {
			if (!$fdConfig->get($view['config'])) {
				continue;
			}

			$defaultMenus[] = $view;
		}

		return $defaultMenus;
	}

	public function showHome()
	{
		return FDT::config()->get('eb_layout_home', true);
	}

	/**
	 * Responsible to retrieve the active menu for the component.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getActiveMenu()
	{
		$view = JFactory::getApplication()->input->get('view', '');

		$map = [
			'blogger' => 'bloggers',
			'teamblog' => 'teamblogs',
			'archive' => 'archives',
		];

		if (array_key_exists($view, $map)) {
			return $map[$view];
		}

		return $view;
	}

	/**
	 * Retrieve the link of the current user's profile
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getProfileLink()
	{
		return '';
	}

	/**
	 * Retrieve the link of the current user's edit profile
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getEditProfileLink()
	{
		return EB::getEditProfileLink();
	}
}
