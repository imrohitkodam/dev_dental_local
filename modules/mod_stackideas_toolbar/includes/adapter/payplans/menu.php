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

class ToolbarMenuPayplans extends ToolbarAdapterMenu 
{
	protected $component = 'com_payplans';
	protected $view = 'dashboard';

	public function __construct()
	{
		$this->my = PP::user(JFactory::getUser()->id);

		JFactory::getLanguage()->load('com_payplans', JPATH_ROOT);
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
		$menu->permalink = PPR::_('index.php?option=com_payplans');

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

		$menus = [];
		$account = $this->account();
		$manage = $this->manage();

		if (!empty($account)) {
			$menus['MOD_SI_TOOLBAR_ACCOUNT'] = [
				'icon' => 'fdi fas fa-user-circle', 
				'menus' => $account
			];
		}

		if (!empty($manage)) {
			$menus['MOD_SI_TOOLBAR_MANAGE'] = [
				'icon' => 'fdi fa fa-cog',
				'menus' => $manage
			];
		}

		if (empty($menus)) {
			return [];
		}

		return ['MOD_SI_TOOLBAR_PAYPLANS' => [
			'icon' => 'fdi fas fa-wallet',
			'menus' => $menus,
		]];
	}

	/**
	 * Get User manage menus
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function manage()
	{
		return ['MOD_SI_TOOLBAR_PP_SUBSCRIPTION' => [
			'icon' => 'fdi fa fa-shopping-cart',
			'link' => PPR::_('index.php?option=com_payplans&view=dashboard')
		]];
	}

	/**
	 * Get user account menus
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function account()
	{
		$config = PP::config();

		$account = [];

		// User Preferences menu
		if ($config->get('user_edit_preferences') || $config->get('user_edit_customdetails')) { 
			$account['MOD_SI_TOOLBAR_PP_PREFERENCES'] = [
				'icon' => 'fdi far fa-edit', 
				'link' => PPR::_('index.php?option=com_payplans&view=dashboard&layout=preferences')
			];
		}

		// download data menu
		if ($config->get('users_download')) {
			$account['MOD_SI_TOOLBAR_PP_DOWNLOADS'] = [
				'icon' => 'fdi fa fa-user-shield', 
				'link' => PPR::_('index.php?option=com_payplans&view=dashboard&layout=download')
			];
		}

		return $account;
	}

	/**
	 * Retrieving the default toolbar menus.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDefaultMenuItems()
	{
		$defaultMenus = [
			[
				'id' => 'plan',
				'view' => 'plan',
				'permalink' => PPR::_('index.php?option=com_payplans&view=plan'),
				'title' => 'MOD_SI_TOOLBAR_PP_PLAN'
			],
			[
				'id' => 'dashboard',
				'view' => 'dashboard',
				'permalink' => PPR::_('index.php?option=com_payplans&view=dashboard'),
				'title' => 'MOD_SI_TOOLBAR_PP_DASHBOARD'
			]
		];

		if ($this->my->id) {
			$ppConfig = PP::config();

			// User preference menu
			if ($ppConfig->get('user_edit_preferences') || $ppConfig->get('user_edit_customdetails')) { 
				$defaultMenus[] = [
					'id' => 'preferences',
					'view' => 'preferences',
					'permalink' => PPR::_('index.php?option=com_payplans&view=dashboard&layout=preferences'),
					'title' => 'MOD_SI_TOOLBAR_PP_PREFERENCES'
				];
			}

			// gdpr download data menu
			if ($ppConfig->get('users_download')) {

				$defaultMenus[] = [
					'id' => 'download',
					'view' => 'download',
					'permalink' => PPR::_('index.php?option=com_payplans&view=dashboard&layout=download'),
					'title' => 'MOD_SI_TOOLBAR_PP_DOWNLOADS'
				];
			}
		}

		return $defaultMenus;
	}

	/**
	 * Show componets home page menu
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showHome() 
	{
		return FDT::config()->get('pp_layout_home', true);
	}

	/**
	 * Responsible to retrieve the active menu for the component.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getActiveMenu()
	{
		$input = JFactory::getApplication()->input;
		
		$view = $input->get('view', '');
		$layout = $input->get('layout', '');

		if ($layout != '') {
			return $layout;
		}

		return $view;
	}
}
