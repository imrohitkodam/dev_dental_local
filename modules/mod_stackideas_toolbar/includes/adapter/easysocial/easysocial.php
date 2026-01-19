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

class ToolbarAdapterEasysocial extends ToolbarAdapter
{
	public $component = 'com_easysocial';
	public $shortName = 'es';
	public $jsName = 'EasySocial';

	public $my = null;
	public $app = null;
	public $input = null;

	public function __construct()
	{
		$this->my = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;

		// Ensure that EasySocial is loaded in the page
		require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php');

		ES::language()->loadSite();
	}

	/**
	 * Determine if toolbar should be enabled.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function toolbarEnabled()
	{
		// if user is guest, need to respect the toolbar for guest setting
		if ($this->my->guest && !FDT::config()->get('es_layout_guests')) {
			return false;
		}

		return parent::toolbarEnabled();
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

	public function getQueryName()
	{		
		return 'q';
	}

	public function getSearchRoute()
	{
		return ESR::getItemId('search');
	}

	public function config()
	{
		return ES::config();
	}

	public function showFriendRequests()
	{
		if (!FDT::config()->get('es_layout_friends')) {
			return false;
		}

		if (!$this->config()->get('friends.enabled')) {
			return false;
		}

		return true;
	}

	public function getTotalFriendRequests()
	{
		if ($this->config()->get('friends.enabled')) {
			return ES::user()->getTotalFriendRequests();
		}
	}


	public function showConversations()
	{
		if (!FDT::config()->get('es_layout_conversations')) {
			return false;
		}

		if (!$this->config()->get('conversations.enabled')) {
			return false;
		}

		return true;
	}

	public function getTotalNewConversations()
	{
		return ES::user()->getTotalNewConversations();
	}

	public function showNotifications($new = false)
	{
		if (!FDT::config()->get('es_layout_notifications')) {
			return false;
		}

		return true;
	}

	public function getTotalNewNotifications()
	{
		return ES::user()->getTotalNewNotifications();
	}

	public function getUsernamePlaceholder()
	{
		return ES::getUsernamePlaceholder();
	}

	public function getRegistrationLink()
	{
		return ESR::registration();
	}

	public function getReturnUrl()
	{
		$loginMenu = $this->config()->get('general.site.login', null);
		$loginReturn = base64_encode(JURI::getInstance()->toString());

		$menu = $this->app->getMenu();
		$activeMenu = $menu->getActive();

		// Retrieve the current menu login redirection URL
		if (is_object($activeMenu) && stristr($activeMenu->link, 'view=login') !== false) {

			if (isset($activeMenu->query) && isset($activeMenu->query['loginredirection'])) {
				$loginMenu = $activeMenu->query['loginredirection'];
			}
		}

		if (!empty($loginMenu) && $loginMenu !== 'null') {
			$loginReturn = ESR::getMenuLink($loginMenu);
			$loginReturn = base64_encode($loginReturn);
		}

		return $loginReturn;
	}

	public function getRemindUsernameLink()
	{
		return ESR::account(array('layout' => 'forgetUsername'));
	}

	public function getResetPasswordLink()
	{
		return ESR::account(array('layout' => 'forgetPassword'));
	}

	public function jfbconnect()
	{
		if (!ES::jfbconnect()->isEnabled()) {
			return false;
		}

		return '{JFBCLogin}';
	}

	public function getUser($id = null)
	{
		$user = ES::user($id);

		return $user;
	}
	
	public function hasCover()
	{
		// Only EasySocial has support for user covers
		return $this->config()->get('users.layout.cover');
	}

	public function showVerified()
	{
		// Only EasySocial has support for user verified
		return $this->getUser()->isVerified();
	}

	public function getBadges()
	{
		return $this->getUser()->getBadges();
	}

	/**
	 * Determines if there is profile meta support in the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function showProfileMeta()
	{
		// Profile meta only applicable for ES. Without ES, just return false.
		return $this->config()->get('users.layout.profiletitle');
	}

	/**
	 * Retrieve the profile meta for the toolbar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getProfileMeta()
	{
		if (!$this->isVersionCompatible('3.3.0')) {
			return [];
		}

		$profile = $this->getUser()->getProfile();
		$params = $profile->getParams();
		$badgeType = $params->get('badgeType', 'icon');
		$item = $badgeType == 'icon' ? $profile->getBadgeIcon() : $profile->getBadgeImage();

		$meta = [];
		$meta['type'] = $badgeType;
		$meta['title'] = $profile->getTitle();
		$meta['item'] = $item;

		return $meta;
	}
	
	public function logoutRedirect()
	{
		$logoutMenu = $this->config()->get('general.site.logout');
		$logoutRedirect = base64_encode(JURI::getInstance()->toString());

		if ($logoutMenu != 'null') {
			$logoutRedirect = ESR::getMenuLink($logoutMenu);
			$logoutRedirect = base64_encode($logoutRedirect);
		}

		return $logoutRedirect;
	}

	public function getAvatarStyle()
	{
		return $this->config()->get('layout.avatar.style');
	}

	public function getAvatar($userId = null, $size = SOCIAL_AVATAR_MEDIUM)
	{
		return $this->getUser($userId)->getAvatar($size);
	}

	public function getNotifications()
	{
		ES::requireLogin();
		FH::checkToken();

		$args = [
			'target_id' => $this->my->id,
			'target_type' => SOCIAL_TYPE_USER,
			'unread' => true,
			'limit' => ES::getLimit('notification.general.pagination'),
		];

		$notification = ES::notification();
		$items = $notification->getItems($args);

		$this->formatNotification($items);

		if ($this->config()->get('notifications.system.autoread')) {
			$model = ES::model('Notifications');
			$result = $model->setAllState(SOCIAL_NOTIFICATION_STATE_READ);
		}

		return $items;
	}

	public function getNotificationPermalink($notification)
	{
		return ESR::notifications(['id' => $notification->id, 'layout' => 'route']);
	}

	public function getNotificationContent($notification)
	{
		return $notification->content;
	}

	public function getNotificationUser($notification)
	{
		if (isset($notification->userOverride)) {
			return $notification->userOverride;
		}

		return $notification->user;
	}
	
	public function getLapsedDate($date)
	{
		return ES::date($date)->toLapsed();
	}

	public function getViewAllNotificationLink()
	{
		return ESR::notifications();
	}
	
	public function getFriendsRequest()
	{
		ES::requireLogin();
		FH::checkToken();

		$model = ES::model('friends');
		$requests = $model->getPendingRequests($this->my->id);

		return $requests;
	}

	public function getViewAllFriendRequestLink()
	{
		return ESR::friends(array('filter' => 'pending'));
	}

	public function getConversations()
	{
		ES::requireLogin();
		FH::checkToken();

		// Get the conversations model
		$model = ES::model('Conversations');
		$options = array('sorting' => 'lastreplied', 'ordering' => 'desc', 'maxlimit' => 8);
		$conversations = $model->getConversations($this->my->id, $options);

		// Mark all items as read if auto read is enabled.
		if ($this->config()->get('notifications.conversation.autoread')) {
			foreach ($conversations as $item) {
				$model->markAsRead($item->id, $this->my->id);
			}
		}

		$this->formatConversations($conversations);

		return $conversations;
	}

	public function isCKEnabled()
	{
		$view = $this->input->get('view', '', 'string');

		return ES::conversekit()->exists($view);
	}

	public function canCreateConversation()
	{
		return ES::conversation()->canCreate();
	}

	public function getConversationLink()
	{
		return ESR::conversations(['layout' => 'compose']);
	}

	/**
	 * Retrieve the composer buttons
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComposeButtons()
	{
		if (!$this->my->id) {
			return;
		}

		$buttons = [];
		$user = ES::user();
		$tbConfig = FDT::config();

		if ($user->canCreateEvents() && $tbConfig->get('es_compose_event', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_EVENT_TITLE'),
				'link' => ESR::events(['layout' => 'create']),
				'icon' => 'fdi far fa-calendar-alt'
			];
		}

		if ($user->canCreateVideos() && $tbConfig->get('es_compose_video', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_VIDEO_TITLE'),
				'link' => ESR::videos(['layout' => 'form']),
				'icon' => 'fdi fa fa-video'
			];
		}

		if ($user->canCreateAudios() && $tbConfig->get('es_compose_audio', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_AUDIO_TITLE'),
				'link' => ESR::audios(['layout' => 'form']),
				'icon' => 'fdi fa fa-music'
			];
		}

		if ($this->isVersionCompatible('4.0.0')) {
			if ($user->canCreateListing() && $tbConfig->get('es_compose_marketplace', true)) {
				$buttons[] = [
					'title' => JText::_('MOD_SI_ES_NEW_MARKETPLACE_TITLE'),
					'link' => ESR::marketplaces(['layout' => 'create']),
					'icon' => 'fdi fa fa-store'
				];
			}
		}

		if ($user->canCreateGroups() && $tbConfig->get('es_compose_group', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_GROUP_TITLE'),
				'link' => ESR::groups(['layout' => 'create']),
				'icon' => 'fdi fa fa-user-friends'
			];
		}

		if ($user->canCreatePages() && $tbConfig->get('es_compose_page', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_PAGE_TITLE'),
				'link' => ESR::pages(['layout' => 'create']),
				'icon' => 'fdi fa fa-briefcase'
			];
		}

		if ($user->canCreateAlbums() && $tbConfig->get('es_compose_album', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_ALBUM_TITLE'),
				'link' => ESR::albums(['layout' => 'form']),
				'icon' => 'fdi fa fa-camera-retro'
			];
		}

		if ($user->canCreatePolls() && $tbConfig->get('es_compose_poll', true)) {
			$buttons[] = [
				'title' => JText::_('MOD_SI_ES_NEW_POLL_TITLE'),
				'link' => ESR::polls(['layout' => 'create']),
				'icon' => 'fdi fa fa-poll'
			];
		}

		return $buttons;
	}

	public function getViewAllConversationsLink()
	{
		return ESR::conversations();
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
		return $conversation->getTitle();
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
		return $conversation->getLastMessageType();
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
		return $conversation->getLastMessage()->getIntro(60);
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
		return $conversation->getLastRepliedDate(true);
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
		return $conversation->getParticipants();
	}

	/**
	 * Allows caller to set all notification items as read
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setallreadajax()
	{
		ES::requireLogin();
		FH::checkToken();

		$response = new stdClass();

		$model = ES::model('Notifications');
		$response->state = $model->setAllState(SOCIAL_NOTIFICATION_STATE_READ);

		if (!$response->state) {
			$response->notice = 'COM_EASYSOCIAL_NOTIFICATIONS_FAILED_TO_MARK_AS_READ';
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
		if (!FDT::config()->get('es_layout_search')) {
			return false;
		}

		// If the user is guest, need to respect the setting
		if ($this->my->guest && !FDT::config()->get('es_layout_searchguests')) {
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
		return $this->config()->get('users.online.state');
	}

	/**
	 * Retrieve SSO for the component.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getSSO()
	{
		$sso = ES::sso();

		if (!$sso->hasSocialButtons()) {
			return false;
		}

		return $sso->getSocialButtons();
	}
	
	/**
	 * Approving a friend request.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function friendAccept($id)
	{
		ES::requireLogin();
		FH::checkToken();

		if (!$this->config()->get('friends.enabled')) {
			return 'COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED';
		}

		// Load up the friends library
		$friends = ES::friends($this->my->id, $id);
		$state = $friends->approve();

		if (!$state) {
			throw new Exception($friends->getError());
			return false;
		}

		$user = $friends->getRequester();
		$message = JText::sprintf('COM_EASYSOCIAL_FRIENDS_USER_IS_NOW_YOUR_FRIEND', $user->getName());

		$response = new stdClass();
		$response->success = $state;
		$response->message = $message;

		return $response;
	}
	
	/**
	 * Rejecting a friend request.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function friendReject($id)
	{
		ES::requireLogin();
		FH::checkToken();

		if (!$this->config()->get('friends.enabled')) {
			return 'COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED';
		}

		// Load up our friends library
		$friends = ES::friends($this->my->id, $id);
		$state = $friends->cancel();

		if (!$state) {
			throw new Exception($friends->getError());
			return false;
		}

		$response = new stdClass();
		$response->success = $state;
		$response->message = JText::_('COM_EASYSOCIAL_FRIENDS_REQUEST_REJECTED');

		return $response;
	}

	public function getPollingInterval()
	{
		return $this->config()->get('notifications.polling.interval');
	}

	/**
	 * Responsible to return the component polling ajax url.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAjaxPollingUrl()
	{
		return JURI::base() . 'components/com_easysocial/polling.php?method=notifier';
	}

	public function showUserDropdown()
	{
		if (!FDT::config()->get('es_layout_user_dropdown', true)) {
			return false;
		}

		return true;
	}

	public function searchKeyword()
	{
		FH::checkToken();

		$query = $this->input->get('q', '', 'string');
		$query = FH::escape($query);
		$showSuggest = $this->input->get('showSuggest', false, 'bool');

		// Determines if we should search by specific filters
		$filters = $this->input->get('filtertypes', array(), 'array');

		$lib = ES::search();
		$data = $lib->search($query, 0, 20, $filters, false, $showSuggest);

		return $this->formatSearchResult($data, $query);
	}

	public function formatSearchResult($data, $query)
	{
		$items = [];

		if ($data->suggestion) {
			foreach ($data->suggestion as $item) {
				$text = preg_replace('/(' . $query .')/ims', '<strong>$1</strong>', $item);
				$item = preg_replace('/\s/', "&nbsp;", $item);

				$obj = new stdClass();
				$obj->element = false;
				$obj->text = $text;
				$obj->link = 'javascript:void(0);';
				$obj->attributes = implode(' ', [
					'data-search-suggestion',
					'data-search-suggestion-value="' . $item . '"'
				]);
				$obj->avatar = '';

				$items[] = $obj;
			}

			return $items;
		}

		if ($data->result) {
			foreach ($data->result as $key => $element) {
				foreach ($element->result as $item) {
					$obj = new stdClass();
					$obj->element = $element->namespace;
					$obj->link = $item->link;
					$obj->text = $item->title;
					$obj->attributes = implode(' ', [
						'data-search-item-typeid="' . $item->uid . '"',
						'data-search-item-type="' . $element->namespace . '"'
					]);
					$obj->avatar = $item->image;
					
					$items[] = $obj;
				}
			}
		}

		return $items;
	}

	public function showFilter()
	{
		return true;
	}

	public function getMobileQrcodeURL()
	{
		$my = $this->getUser();

		if (!$my->hasCommunityAccess()) {
			return false;
		}

		if (method_exists('ES', 'getMobileQRLoginUrl')) {
			return ES::getMobileQRLoginUrl();
		}

		return ESR::apps(['layout' => 'mobileAppQrcode', 'tmpl' => 'component', '1' => ES::date()->toUnix()]);
	}

	/**
	 * Determine if the user registration is enabled
	 *
	 * @since	1.0.13
	 * @access	public
	 */
	public function isRegistrationEnabled()
	{
		$config = ES::config();
		$isRegistrationEnabled = $config->get('registrations.enabled');

		return $isRegistrationEnabled;
	}

	public function showUserLogin()
	{
		return FDT::config()->get('es_layout_login', true);
	}

	public function getSuggestion()
	{
		$config = ES::config();
		$suggestion = $config->get('search.suggestion', true);
		
		return $suggestion;
	}

	public function getMinSearch()
	{
		if ($this->config()->get('search.minimum', true)) {
			return $this->config()->get('search.characters', '3');
		}

		// If the force minima is not enabled, we'll fall back to the default value: 3
		// Based on the ES3.2 original behavior.
		return '3';
	}

	public function getProfileStyling($id = null)
	{
		$user = $this->getUser($id);
		$profileParams = $user->getProfile()->getParams();

		$profileStyling = [];

		if ($profileParams->get('label_colour') && $profileParams->get('label_font_colour')) {
			$profileStyling['customStyle'][] = 'color: ' . $profileParams->get('label_font_colour');
		}

		if ($profileParams->get('label_background') && $profileParams->get('label_background_colour')) {
			$profileStyling['customStyle'][] .= 'background: ' . $profileParams->get('label_background_colour');
			$profileStyling['class'] = ' px-2xs rounded-sm es-user-label-styled';
		}

		if (count($profileStyling)) {
			$styling = array_shift($profileStyling);
			$profileStyling['customStyle'] = 'style="' . implode(';', $styling) . '"';
		}

		return $profileStyling;
	}
}