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

class ToolbarAjax
{
	public $adapter = null;

	public function __construct()
	{
		$this->adapter = FDT::getAdapter(FDT::getMainComponent());
	}

	public function conversations()
	{
		$args = [
			'conversations' => $this->adapter->getConversations(),
			'ck' => $this->adapter->isCKEnabled(),
			'my' => JFactory::getUser(),
		];

		$themes = FDT::themes();
		$output = $themes->output('ajax/conversations', $args);

		header('Content-type: application/json; UTF-8');

		echo json_encode($output);
		exit;
	}

	public function friends()
	{
		$args = [
			'requests' => $this->adapter->getFriendsRequest(),
			'my' => JFactory::getUser(),
		];

		$themes = FDT::themes();
		$output = $themes->output('ajax/friends', $args);

		header('Content-type: application/json; UTF-8');

		echo json_encode($output);
		exit;
	}

	public function notifications()
	{
		$args = [
			'items' => $this->adapter->getNotifications(),
			'viewAllNotificationsLink' => $this->adapter->getViewAllNotificationLink(),
		];

		$themes = FDT::themes();
		$output = $themes->output('ajax/notifications', $args);

		header('Content-type: application/json; UTF-8');

		echo json_encode($output);
		exit;
	}

	public function setallreadajax()
	{
		$state = $this->adapter->setallreadajax();

		header('Content-type: application/json; UTF-8');

		echo json_encode($state);
		exit;
	}

	public function poll()
	{
		$data = new stdClass();

		// Poll for new system notifications
		$this->adapter->getSystemNotifications($data);

		// Poll for new friend notifications
		$this->adapter->getFriendNotifications($data);

		// Poll for new conversations
		$this->adapter->getConversationNotifications($data);

		header('Content-type: application/json; UTF-8');

		echo json_encode($data);
		exit;
	}

	public function friendAccept($id)
	{
		$data = $this->adapter->friendAccept($id);
		header('Content-type: application/json; UTF-8');

		echo json_encode($data);
		exit;
	}

	public function friendReject($id)
	{
		$data = $this->adapter->friendReject($id);
		header('Content-type: application/json; UTF-8');

		echo json_encode($data);
		exit;
	}

	public function search()
	{
		$themes = FDT::themes();

		// In respect with the setting passed on the form, we'll load the adapter based on it.
		$adapter = FDT::getAdapter(JFactory::getApplication()->input->get('component'));

		$output = $themes->output('search/ajax/search', [
			'items' => $adapter->searchKeyword(),
		]);

		header('Content-type: application/json; UTF-8');

		echo json_encode($output);
		exit;
	}

	public function categories()
	{
		// In respect with the setting passed on the form, we'll load the adapter based on it.
		$adapter = FDT::getAdapter(JFactory::getApplication()->input->get('component'));

		$themes = FDT::themes();
		$output = $themes->output('search/ajax/categories', [
			'adapter' => $adapter,
			'categories' => $adapter->getCategories(),
		]);

		header('Content-type: application/json; UTF-8');

		echo json_encode($output);
		exit;
	}

	public function dialog()
	{
		$adapter = FDT::getAdapter(JFactory::getApplication()->input->get('component'));

		$themes = FDT::themes();
		$output = $themes->output('search/ajax/dialog', [
			'component' => $adapter->getComponent(),
			'task' => $adapter->getTask(),
			'itemid' => $adapter->getSearchRoute()
		]);

		header('Content-type: application/json; UTF-8');

		echo json_encode($output);
		exit;
	}
}