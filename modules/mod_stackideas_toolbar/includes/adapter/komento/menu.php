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

class ToolbarMenuKomento extends ToolbarAdapterMenu 
{
	protected $component = 'com_komento';
	protected $view = 'dashboard';

	public function __construct()
	{
		$this->my = KT::user(JFactory::getUser()->id);
		$this->config = FDT::getAdapter('com_komento')->config();

		JFactory::getLanguage()->load('com_komento', JPATH_ROOT);
	}
	
	/**
	 * Provide the home menu.
	 *
	 * @since	1.0.3
	 * @access	public
	 */
	public function getHomeMenu()
	{
		$menu = new stdClass();
		$menu->id = $this->view;
		$menu->permalink = KT::router()->_('index.php?option=com_komento&view=dashboard');

		return $menu;
	}

	/**
	 * Building the dropdown menu.
	 *
	 * @since	1.0.3
	 * @access	public
	 */
	public function getDropdownMenu()
	{
		if (!$this->my->id) {
			return [];
		}

		$menus = [
			'MOD_SI_TOOLBAR_DASHBOARD' => [
				'icon' => 'fdi fas fa-tachometer-alt', 
				'link' => KT::router()->_('index.php?option=com_komento&view=dashboard', false)
			]
		];

		if ($this->config->get('enable_gdpr_download')) {
			$menus['MOD_SI_TOOLBAR_DOWNLOAD_DATA'] = [
				'icon' => 'fdi fa fa-user-shield', 
				'link' => KT::router()->_('index.php?option=com_komento&view=dashboard&layout=download', false)
			];
		}

		return ['MOD_SI_TOOLBAR_KOMENTO' => [
			'icon' => 'fdi fas fa-comment-dots',
			'menus' => $menus
		]];
	}

	/**
	 * Retrieving the default toolbar menus.
	 *
	 * @since	1.0.3
	 * @access	public
	 */
	public function getDefaultMenuItems()
	{
		$defaultMenus = [];

		if ($this->my->id) {
			$defaultMenus[] = [
				'id' => 'dashboard',
				'view' => 'dashboard',
				'permalink' => KT::router()->_('index.php?option=com_komento&view=dashboard'),
				'title' => 'MOD_SI_TOOLBAR_DASHBOARD'
			];

			if ($this->config->get('enable_gdpr_download')) {
				$defaultMenus[] = [
					'id' => 'download',
					'view' => 'download',
					'permalink' => KT::router()->_('index.php?option=com_komento&view=dashboard&layout=download'),
					'title' => 'MOD_SI_TOOLBAR_DOWNLOAD_DATA'
				];
			}
		}

		return $defaultMenus;
	}

	/**
	 * Show componets home page menu
	 *
	 * @since	1.0.3
	 * @access	public
	 */
	public function showHome() 
	{
		return FDT::config()->get('kt_layout_home', true);
	}

	/**
	 * Responsible to retrieve the active menu for the component.
	 *
	 * @since	1.0.3
	 * @access	public
	 */
	public function getActiveMenu()
	{
		$input = JFactory::getApplication()->input;

		$view = $input->get('view', '', 'string');
		$layout = $input->get('layout', '', 'string');

		if ($layout !== '') {
			return $layout;
		}

		return $view;
	}
}