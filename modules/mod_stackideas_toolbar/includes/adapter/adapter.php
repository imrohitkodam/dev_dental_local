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

class ToolbarAdapter
{
	public $component = null;
	public $shortName = null;
	public $jsName = null;

	/**
	 * Retrieves the module and component's config.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function config()
	{
	}

	/**
	 * Retrieve user library based on the extensions.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getUser() 
	{
	}

	/**
	 * Determines if there is cover support in the toolbar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function hasCover()
	{
		// If needed, can be extended by child to alter its behavior
		return false;
	}

	/**
	 * Determines if there is verified support in the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showVerified()
	{
		// If needed, can be extended by child to alter its behavior
		return false;
	}

	/**
	 * Retrive the badges of the user for the toolbar
	 *
	 * @since	1.0.14
	 * @access	public
	 */
	public function getBadges()
	{
		// If needed, can be extended by child to alter its behavior
		return false;
	}

	/**
	 * Determines if there is profile meta support in the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showProfileMeta()
	{
		// If needed, can be extended by child to alter its behavior
		return false;
	}

	/**
	 * Retrieve the profile meta for the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getProfileMeta()
	{
		// If needed, can be extended by child to alter its behavior
		return [];
	}

	/**
	 * Determines if there is logout redirect.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function logoutRedirect()
	{
		return false;
	}

	public function isCKEnabled()
	{
		// CK only available for EasySocial.
		return false;
	}
	
	/**
	 * Determines the registration link for the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getRegistrationLink()
	{
		// If needed, implement on child to get user registration link
		return JURI::base() . 'index.php?option=com_users&view=registration';
	}

	/**
	 * Determines the username placeholder used for login. 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getUsernamePlaceholder()
	{
		return 'MOD_SI_TOOLBAR_USERNAME';
	}

	/**
	 * Determines the return url used for login. 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getReturnUrl()
	{
		return base64_encode(JURI::getInstance()->toString());
	}

	/**
	 * Determines the remind username link used for login. 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getRemindUsernameLink()
	{
		// If needed, implement on child to get forgot username link
		return JURI::base() . 'index.php?option=com_users&view=remind';
	}

	/**
	 * Determines the reset password link used for login. 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getResetPasswordLink()
	{
		// If needed, implement on child to get forgot password link
		return JURI::base() . 'index.php?option=com_users&view=reset';
	}

	/**
	 * Retrieving the jfbconnect library based on the engine.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function jfbconnect()
	{
		// If needed, implement on child
		return false;
	}

	/**
	 * Retrieve the query name for the search.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getQueryName()
	{
		// If needed, can be implemented by child
		return 'query';
	}

	/**
	 * Retrieve the search query's task.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getTask()
	{
		// If needed, can be implemented by child
		return 'query';
	}

	/**
	 * Retrieve the search route.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getSearchRoute()
	{
		// If needed, implement on child
		return false;
	}

	/**
	 * Determine whether should render subscription button.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showSubscription()
	{
		return false;
	}

	/**
	 * Determine whether should render dropdown menu.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showUserDropdown()
	{
		return true;
	}

	/**
	 * Retrieve the component's avatar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAvatar()
	{
		return FH::getDefaultAvatar();
	}
	
	/**
	 * Determine whether to render search form.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showSearch()
	{
		// If needed, implement on child
		return false;
	}

	/**
	 * Retrieve the composer buttons
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComposeButtons()
	{
		// Implemented in child
	}

	/**
	 * Determine whether to show friend request notifications.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showFriendRequests()
	{

	}

	/**
	 * Retrieve the total friend requests.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getTotalFriendRequests()
	{

	}

	/**
	 * Determine whether to show conversation notifications.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showConversations()
	{

	}

	/**
	 * Retrieve the total new conversations.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getTotalNewConversations()
	{

	}

	/**
	 * Determine whether to show new notifications.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showNotifications()
	{

	}

	/**
	 * Retrieve the total new notifications.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getTotalNewNotifications()
	{

	}

	/**
	 * Return the adapter's name.
	 *
	 * @since	1.0.0
	 * @return  string
	 */
	public function getComponent($withComponentPrefix = true)
	{
		static $cache = [];

		$key = $withComponentPrefix ? 1 : 0;

		if (!isset($cache[$key])) {
			$value = $this->component;

			if (!$withComponentPrefix) {
				$value = str_replace('com_', '', $value);
			}
			
			$cache[$key] = $value;
		}

		return $cache[$key];
	}

	/**
	 * Return the adapter's name.
	 *
	 * @since	1.0.0
	 * @return  string
	 */
	public function getShortName()
	{
		return $this->shortName;
	}

	/**
	 * Return the adapter's name.
	 *
	 * @since	1.0.0
	 * @return  string
	 */
	public function getJSName()
	{
		return $this->jsName;
	}

	/**
	 * Retrieve the query search.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getSearchQuery()
	{
		$query = $this->input->get($this->getQueryName(), '', 'string');

		// Format query to UTF-8.
		$query = FH::escape($query);

		return $query;
	}

	/**
	 * Retrieve the avatar style
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAvatarStyle()
	{
		return '';
	}

	/**
	 * Retrieve SSO for the component.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getSSO()
	{
		// If needed, can be implemented on child
		return false;
	}

	/**
	 * Determine whether QRCode should be rendered on the site.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showQRCode()
	{
		if (!FDT::config()->get('show_qrcode_mobileapp', true) || !$this->getUser()->id) {
			return false;
		}

		return true;
	}

	public function getMobileQrcodeURL()
	{
		return '';
	}

	/**
	 * Formatting the notification output.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function formatNotification(&$notifications)
	{
		if (!$notifications) {
			return false;
		}

		// Format notifications to standardize the output.
		foreach ($notifications as &$notification) {
			$notification->title = strip_tags($notification->title, '<b>');
			$notification->permalink = $this->getNotificationPermalink($notification);
			$notification->content = $this->getNotificationContent($notification);
			$notification->user = $this->getNotificationUser($notification);
			$notification->lapsed = $this->getLapsedDate($notification->created);
			$notification->image = FH::normalize($notification, 'image', false);
		}
	}

	/**
	 * Formatting the conversations output.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function formatConversations(&$conversations)
	{
		if (!$conversations) {
			return false;
		}

		// Format conversations to standardize the output.
		foreach ($conversations as &$item) {
			$item->title = $this->getConversationsTitle($item);
			$item->lastMessageType = $this->getConversationsLastMessageType($item);
			$item->message = $this->getConversationsMessage($item);
			$item->elaped = $this->getConversationsElaped($item);
			$item->participant = $this->getConversationsParticipant($item);
		}
	}

	/**
	 * Create standard info object
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function createInfo()
	{
		$info = new stdClass();
		$info->total = -1;
		$info->data = '';

		return $info;
	}

	public function getMenu() 
	{
		static $cache = [];
		$component = str_replace('com_', '', $this->component);

		if (!isset($cache[$component])) {
			require_once(__DIR__ . '/' . $component . '/menu.php');

			$className = 'ToolbarMenu' . ucfirst($component);

			$cache[$component] = new $className();
		}
		
		return $cache[$component];
	}

	/**
	 * Determine the default polling interval for the notification.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getPollingInterval()
	{
		return '30';
	}

	/**
	 * Determine if toolbar should be enabled.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function toolbarEnabled()
	{
		$component = $this->getComponent(false);

		return FDT::config()->get($component) !== 'disabled';
	}

	/**
	 * Responsible to return the component ajax url.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAjaxUrl()
	{
		return JURI::base() . 'index.php?option=com_ajax&module=stackideas_toolbar&format=json';
	}

	/**
	 * Responsible to return the component polling ajax url.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAjaxPollingUrl()
	{
		return JURI::base() . 'index.php?option=com_ajax&module=stackideas_toolbar&format=json&method=polling';
	}

	/**
	 * Responsible to retrieve the keyword suggestions for search.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function searchKeyword()
	{
		return '';
	}

	/**
	 * Responsible to format the search suggested keywords
	 *
	 * @since	1.0.2
	 * @access	public
	 */
	public function formatSearchResult($data, $query)
	{
		$items = [];

		if ($data) {
			foreach ($data as $row) {
				$row->title = preg_replace('/(' . $query .')/ims', '<strong>$1</strong>', $row->title);
				$row->title = preg_replace('/\s/', "&nbsp;", $row->title);

				$obj = new stdClass();
				$obj->value = strip_tags($row->title);
				$obj->text = $row->title;
				$obj->link = 'javascript:void(0);';
				$obj->attributes = implode(' ',[
					'data-search-suggestion',
					'data-search-suggestion-value="' . $query . '"'
				]);
				$obj->avatar = false;

				$items[] = $obj;
			}
		}

		return $items;
	}

	/**
	 * Determine whether to show categories filter on search.
	 *
	 * @since	1.0.2
	 * @access	public
	 */
	public function showCategoriesFilter()
	{
		return false;
	}

	/**
	 * Determine whether to show filter on search.
	 *
	 * @since	1.0.2
	 * @access	public
	 */
	public function showFilter()
	{
		return false;
	}

	/**
	 * Determine if the user registration is enabled
	 *
	 * @since	1.0.13
	 * @access	public
	 */
	public function isRegistrationEnabled()
	{
		return FH::isRegistrationEnabled();
	}

	/**
	 * Determine whether to show user login form for the extensions.
	 *
	 * @since	1.0.13
	 * @access	public
	 */
	public function showUserLogin()
	{
		return true;
	}

	/**
	 * Determine whether to show search suggestion on the toolbar
	 *
	 * @since	1.0.13
	 * @access	public
	 */
	public function getSuggestion()
	{
		return true;
	}

	/**
	 * Determine whether the minimum search characters.
	 *
	 * @since	1.0.13
	 * @access	public
	 */
	public function getMinSearch()
	{
		return 3;
	}

	/**
	 * Getting the profile styling.
	 *
	 * @since	1.0.18
	 * @access	public
	 */
	public function getProfileStyling()
	{
		// If needed, can be extended by child to alter its behavior
		return [];
	}
}