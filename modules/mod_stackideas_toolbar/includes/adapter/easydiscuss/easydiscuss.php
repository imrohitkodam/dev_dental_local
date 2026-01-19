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

class ToolbarAdapterEasydiscuss extends ToolbarAdapter
{
	public $component = 'com_easydiscuss';
	public $shortName = 'ed';
	public $jsName = 'EasyDiscuss';

	public $my = null;
	public $app = null;
	public $input = null;

	public function __construct()
	{
		$this->my = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;

		// Ensure that EasyDiscuss is loaded in the page
		require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
	}

	public function getSearchRoute()
	{
		return EDR::getItemId('search');
	}

	public function showSubscription()
	{
		if (!FDT::config()->get('ed_layout_subscribe')) {
			return false;
		}
		
		if ($this->config()->get('main_sitesubscription') && ($this->config()->get('main_rss') || $this->config()->get('main_sitesubscription'))) {
			return true;
		}

		return false;
	}

	public function config()
	{
		return $config = ED::config();
	}

	public function getSubscriptions()
	{
		$subscribeModel = ED::model('Subscribe');
		$isSubscribed = $subscribeModel->isSiteSubscribed('site', $this->my->email, 0);

		return $isSubscribed;
	}

	public function showConversations($new = false)
	{
		if (!$this->config()->get('main_conversations') || !FDT::config()->get('ed_layout_conversation')) {
			return false;
		}

		return true;
	}

	public function getTotalNewConversations()
	{
		$model = ED::model('conversation');

		return $model->getCount($this->my->id, array('filter' => 'unread'));
	}

	public function showNotifications($new = false)
	{
		if (!$this->config()->get('main_notifications') || !FDT::config()->get('ed_layout_notification')) {
			return false;
		}

		return true;
	}

	public function getTotalNewNotifications()
	{
		$model = ED::model('Notification');
		
		return $model->getTotalNotifications($this->my->id);
	}

	/**
	 * Retrieve the composer buttons
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComposeButtons()
	{
		if (!FDT::config()->get('ed_layout_compose') || !ED::acl()->allowed('add_question')) {
			return false;
		}

		return [
			[
				'title' => JText::_('MOD_SI_EASYDISCUSS_COMPOSE_BUTTON_TITLE'),
				'link' => EDR::_('view=ask'),
				'icon' => 'fdi far fa-comment-dots'
			]
		];
	}

	public function getUsernamePlaceholder()
	{
		return 'COM_EASYDISCUSS_TOOLBAR_USERNAME';
	}

	public function getRegistrationLink()
	{
		return ED::getRegistrationLink();
	}

	public function getReturnUrl()
	{
		$loginReturn = EDR::getLoginRedirect();

		$url = ED::getCallback('', false);

		if ($url) {
			$loginReturn = base64_encode($url);
		}

		return $loginReturn;
	}

	public function getRemindUsernameLink()
	{
		return ED::getRemindUsernameLink();
	}

	public function getResetPasswordLink()
	{
		return ED::getResetPasswordLink();
	}

	public function jfbconnect()
	{
		if (!ED::jfbconnect()->exists()) {
			return false;
		}
		
		return '{JFBCLogin}';
	}
	
	public function getUser($id = null)
	{
		$user = ED::user($id);

		return $user;
	}

	public function logoutRedirect()
	{
		return EDR::getLogoutRedirect();
	}

	public function getAvatar($userId = null, $size = 'large')
	{
		// We'll ignore the size since ED doesn't request for it.
		return $this->getUser($userId)->getAvatar();
	}

	public function getAvatarStyle()
	{
		return 'rounded';
	}

	public function getNotifications()
	{
		FH::checkToken();

		$model = ED::model('Notification');
		$notifications = $model->getNotifications($this->my->id, true, $this->config()->get('main_notifications_limit'));

		// Let ED format the notification first.
		ED::notifications()->format($notifications);

		// Re-format for Toolbar.
		$this->formatNotification($notifications);

		return $notifications;
	}

	public function getNotificationPermalink($notification)
	{
		return $notification->permalink;
	}

	public function getNotificationContent($notification)
	{
		// ED notification item is set on title property. We'll just return empty.
		return '';
	}

	public function getNotificationUser($notification)
	{
		return $notification->authorProfile;
	}
	
	public function getLapsedDate($date)
	{
		return ED::Date()->toLapsed($date);
	}

	public function getViewAllNotificationLink()
	{
		return EDR::_('view=notifications');
	}

	public function getConversations()
	{
		FH::checkToken();

		$model = ED::model('Conversation');
		$items = $model->getConversations($this->my->id, ['limit' => $this->config()->get('main_conversations_notification_items'), 'filter' => 'unread']);

		$this->formatConversations($items);

		return $items;
	}

	public function canCreateConversation()
	{
		return ED::acl()->allowed('allow_privatemessage', false);
	}

	public function getConversationLink()
	{
		return EDR::_('view=conversation&layout=compose');
	}

	public function getViewAllConversationsLink()
	{
		return EDR::_('view=conversation');
	}

	/**
	 * Get notification counter for system notifications
	 * Requested from ajax/poll
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getSystemNotifications(&$data)
	{
		FH::checkToken();

		$model = ED::model('Notification');
		$total = $model->getTotalNotifications($this->my->id);

		$data->system = $this->createInfo();
		$data->system->total = $total;
	}

	/**
	 * Get notification counter for friends notifications
	 * Requested from ajax/poll
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getFriendNotifications(&$data)
	{
		// ED do not has friend request.
		return false;
	}

	/**
	 * Get conversations counter
	 * Requested from ajax/poll
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getConversationNotifications(&$data)
	{
		FH::checkToken();

		$model = ED::model('Conversation');
		$total = $model->getCount($this->my->id, ['filter' => 'unread']);

		$data->conversation = $this->createInfo();
		$data->conversation->total = $total;
	}

	/**
	 * Retrieve the conversation's title.
	 * Requested by ajax/conversation
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getConversationsTitle($conversation)
	{
		return $conversation->getLastReplier()->getName();
	}

	/**
	 * Retrieve the conversation's message type.
	 * Requested by ajax/conversation
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getConversationsLastMessageType($conversation)
	{
		return 'message';
	}

	/**
	 * Retrieve the conversation's message.
	 * Requested by ajax/conversation
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getConversationsMessage($conversation)
	{
		return substr(strip_tags($conversation->getLastMessage($this->my->id, false)), 0, 150) . JText::_('COM_EASYDISCUSS_ELLIPSES');
	}

	/**
	 * Retrieve the conversation's lapsed time.
	 * Requested by ajax/conversation
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getConversationsElaped($conversation)
	{
		return $conversation->getElapsedTime();
	}

	/**
	 * Retrieve the conversation's participants.
	 * Requested by ajax/conversation
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getConversationsParticipant($conversation)
	{
		return $conversation->getParticipant();
	}

	/**
	 * Allows caller to set all notification items as read
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setallreadajax()
	{
		FH::checkToken();

		$response = new stdClass();

		$model = ED::model('Notification');
		$response->state = $model->markAllRead();

		if (!$response->state) {
			$response->notice = 'COM_EASYDISCUSS_ALL_NOTIFICATIONS_FAILED_MARKED_AS_READ';
		}

		return $response;
	}

	/**
	 * Determine whether should render search on this component's toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showSearch()
	{
		if (!FDT::config()->get('ed_layout_search')) {
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
		return $this->config()->get('layout_user_online');
	}

	public function showUserDropdown()
	{
		if ($this->my->id && !FDT::config()->get('ed_layout_user_dropdown', true)) {
			return false;
		}

		return true;
	}

	public function searchKeyword()
	{
		FH::checkToken();

		$query = $this->input->get('query', '', 'string');
		$category = $this->input->get('category_id', 0, 'int');

		$query = FH::escape($query);

		// Get the pagination limit
		$options = [
			'category' => $category,
			'sort' => 'latest',
			'filter' => 'all',
			'postTypes' => [],
			'postLabels' => [],
			'postPriorities' => [],
			'search' => $query,
			'searchIncludeReplies' => true,
			'includeChilds' => false,
		];

		$model = ED::model('Posts');
		$posts = $model->getDiscussions($options);

		return $this->formatSearchResult($posts, $query);
	}

	public function showCategoriesFilter()
	{
		return true;
	}

	public function getCategories()
	{
		$model = ED::model('categories');

		$categories = $model->getParentCategories('', 'all', true, true);

		foreach ($categories as $category) {
			$category->childs = $model->getChildCount($category->id);
		}

		return $categories;
	}

	public function getChildCategories($id)
	{
		$model = ED::model('categories');
		$categories = $model->getChildCategories($id, true, true);

		foreach ($categories as $category) {
			$category->childs = $model->getChildCount($category->id);
		}

		return $categories;
	}

	public function showUserLogin()
	{
		return FDT::config()->get('ed_layout_login', true);
	}

	public function getPollingInterval()
	{
		return $this->config()->get('system_polling_interval');
	}
}