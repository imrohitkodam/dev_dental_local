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

class ToolbarAdapterEasyblog extends ToolbarAdapter
{
	public $component = 'com_easyblog';
	public $shortName = 'eb';
	public $jsName = 'EasyBlog';

	public $my = null;
	public $app = null;
	public $input = null;

	public function __construct()
	{
		$this->my = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;

		// Ensure that EasyBlog is loaded in the page
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');
	}

	public function getTask()
	{
		return 'search.query';
	}

	public function getSearchRoute()
	{
		return EBR::getItemId('search');
	}

	public function showSubscription()
	{
		if (!FDT::config()->get('eb_layout_subscribe')) {
			return false;
		}

		if (!$this->config()->get('main_sitesubscription') || !$this->acl()->get('allow_subscription')) {
			return false;
		}

		return true;
	}

	public function showUserDropdown()
	{
		if (!FDT::config()->get('eb_layout_user_dropdown', true)) {
			return false;
		}

		return true;
	}

	public function config()
	{
		return EB::config();
	}

	public function acl()
	{
		return EB::acl();
	}

	public function getSubscriptions()
	{
		// Load up the subscription record for the current user.
		$subscription = EB::table('Subscriptions');

		if (!$this->my->guest) {
			$subscription->load(['email' => $this->my->email, 'utype' => 'site']);
		}
		
		return $subscription;
	}

	/**
	 * Retrieve the composer buttons
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComposeButtons()
	{
		if ($this->my->guest || !FDT::config()->get('eb_layout_compose') || !EB::acl()->get('add_entry')) {
			return false;
		}

		$buttons = [
			[
				'title' => JText::_('MOD_SI_EASYBLOG_COMPOSE_BUTTON_TITLE'),
				'link' => EB::composer()->getComposeUrl(),
				'icon' => 'fdi far fa-newspaper'
			]
		];

		if (FDT::config()->get('eb_layout_quickpost') && $this->config()->get('main_microblog')) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_EASYBLOG_QUICKPOST_BUTTON_TITLE'),
				'link' => EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost'),
				'icon' => 'fdi fab fa-microblog'
			];
		}

		return $buttons;
	}

	public function getUsernamePlaceholder()
	{
		return 'COM_EASYBLOG_USERNAME';
	}

	public function getRegistrationLink()
	{
		return EB::getRegistrationLink();
	}

	public function getRemindUsernameLink()
	{
		return EB::getRemindUsernameLink();
	}

	public function getResetPasswordLink()
	{
		return EB::getResetPasswordLink();
	}

	public function jfbconnect()
	{
		if (!EB::jfbconnect()->exists()) {
			return false;
		}

		return EB::jfbconnect()->getTag();
	}
	
	public function getUser($id = null)
	{
		$user = EB::user($id);

		return $user;
	}
	
	public function logoutRedirect()
	{
		return base64_encode(JURI::getInstance()->toString());
	}

	public function getAvatar($userId = null, $size = 'large')
	{
		// We'll ignore the size since EB doesn't request for it.
		return $this->getUser($userId)->getAvatar();
	}

	public function getAvatarStyle()
	{
		return $this->config()->get('layout_avatar_style');
	}

	/**
	 * Determine whether should render search on this component's toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showSearch()
	{
		if (!FDT::config()->get('eb_layout_search')) {
			return false;
		}
		
		return true;
	}

	/**
	 * Determine whether should render the online state for the component
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showOnlineState()
	{
		return false;
	}

	public function searchKeyword()
	{
		FH::checkToken();

		$query = $this->input->get('query', '', 'string');
		$query = FH::escape($query);

		$model = EB::model('Search');
		$result = $model->getData();

		return $this->formatSearchResult($result, $query);
	}

	public function showCategoriesFilter()
	{
		return true;
	}

	public function getCategories()
	{
		$model = EB::model('categories');

		$categories = $model->getParentCategories('', 'all', true, true);
		$categories = EB::formatter('categories', $categories);

		foreach ($categories as $category) {
			$category->childs = $category->getChildCount();
		}

		return $categories;
	}

	public function getChildCategories($id)
	{
		$db = JFactory::getDbo();
		$query = [];
		$query[] = 'SELECT a.`id`, a.`title`, a.`alias`, a.`private`, a.`parent_id`, a.`avatar`, a.`description`';
		$query[] = 'FROM `#__easyblog_category` as a';
		$query[] = 'WHERE a.`parent_id` = ' . $db->quote($id);
		$query[] = 'AND a.`published` = 1';

		$catLib = EB::category();
		$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`', CATEGORY_ACL_ACTION_SELECT);
		$query[] = 'AND (' . $catAccess . ')';

		// @task: Append language.
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();

		if ($filterLanguage && $this->config()->get('layout_composer_category_language', 0)) {
			$query[] = EBR::getLanguageQuery('AND', 'a.language');
		}

		$query = implode(' ', $query);

		// debug
		// echo $query;exit;

		$db->setQuery($query);
		$categories = $db->loadObjectList();
		$categories = EB::formatter('categories', $categories);

		foreach ($categories as $category) {
			$category->childs = $category->getChildCount();
		}

		return $categories;
	}

	public function showUserLogin()
	{
		return FDT::config()->get('eb_layout_login', true);
	}
}