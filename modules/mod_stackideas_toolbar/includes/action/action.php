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

class ToolbarAction
{
	/**
	 * Renders commpose actions on the toolbar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function compose()
	{
		$composeButtons = [];
		$extensions = [];

		$adaptive = FDT::config()->get('adaptiveMenu', true);

		if ($adaptive) {
			$input = JFactory::getApplication()->input;
			$current = $input->get('option', '', 'string');

			$extensions[] = str_replace('com_', '', $current);
		}

		if (!$adaptive) {
			// Get all available extensions
			$extensions = FDT::getAvailableExtensions();
		}

		foreach ($extensions as $extension) {
			$adapter = FDT::getAdapter($extension);

			$buttons = $adapter->getComposeButtons();

			if (!$buttons) {
				continue;
			}

			foreach ($buttons as $button) {
				$composeButtons[] = $button;
			}
		}

		// Sort the buttons alphabetically
		usort($composeButtons, function($a, $b) {
			return strcmp($a['title'], $b['title']);
		});

		if (!$composeButtons) {
			return;
		}

		$args = [
			'composeButtons' => $composeButtons
		];

		$theme = FDT::themes();

		return $theme->output('compose/default', $args);
	}

	/**
	 * Renders notification action on the toolbar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function notifications()
	{
		$my = JFactory::getUser();

		if ($my->guest) {
			return;
		}

		// Notification will be based on the main component.
		// Notifications, friends and conversation should appear when choosing EasyBlog toolbar.
		$adapter = FDT::getAdapter(FDT::getMainComponent());

		$options = [];
		$options['moduleId'] = FDT::getModuleId();

		$input = JFactory::getApplication()->input;
		$currentView = $input->get('view', '', 'string');

		$options['currentView'] = $currentView;

		$showFriendRequests = $adapter->showFriendRequests();
		$showConversations = $adapter->showConversations();
		$showNotifications = $adapter->showNotifications();

		if (!$showFriendRequests && !$showConversations && !$showNotifications) {
			return;
		}

		$options['showFriendRequests'] = $showFriendRequests;

		if ($options['showFriendRequests']) {
			$options['newFriendRequests'] = $adapter->getTotalFriendRequests();
			$options['viewAllFriendRequestLink'] = $adapter->getViewAllFriendRequestLink();
		}

		$options['showConversations'] = $showConversations;

		if ($options['showConversations']) {
			$options['newConversations'] = $adapter->getTotalNewConversations();
			$options['viewAllConversationsLink'] = $adapter->getViewAllConversationsLink();

			$options['canCreateConversations'] = $adapter->canCreateConversation();

			if ($options['canCreateConversations']) {
				$options['createConversationLink'] = $adapter->getConversationLink();
			}
		}

		$options['showNotifications'] = $showNotifications;

		if ($options['showNotifications']) {
			$options['newNotifications'] = $adapter->getTotalNewNotifications();
			$options['viewAllNotificationsLink'] = $adapter->getViewAllNotificationLink();
		}

		$themes = FDT::themes();
		return $themes->output('notifications/default', $options);
	}

	/**
	 * Renders the search action on the toolbar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function search()
	{
		// Retrieve the search behavior of the toolbar.
		$behavior = FDT::config()->get('defaultSearch', 'search-default');

		$component = str_replace('search-', 'com_', $behavior);

		if ($behavior === 'search-default') {
			$component = JFactory::getApplication()->input->get('option');
		}

		// We'll need this custom adapter so the search form will loaded correctly according to the perspective.
		$adapter = FDT::getAdapter($component);

		if (!$adapter->showSearch()) {
			return;
		}

		$themes = FDT::themes();
		return $themes->output('search/button', [
			'isMobile' => FH::responsive()->isMobile(),
			'component' => $component
		]);
	}

	/**
	 * Renders the subscription action on the toolbar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function subscriptions()
	{
		$adapter = FDT::getAdapter(JFactory::getApplication()->input->get('option'));
		$showSubscription = $adapter->showSubscription();

		if (!$showSubscription) {
			return;
		}

		$args = [
			'showSubscription' => $showSubscription,
			'subscription' => $adapter->getSubscriptions(),
			'config' => $adapter->config()
		];

		$namespace = 'subscriptions/' . $adapter->getComponent(false);

		$themes = FDT::themes();
		$output = $themes->output($namespace, $args);

		return $output;
	}
}
