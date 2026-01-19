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

class ToolbarHtmlAvatar
{
	private $adapter = null;
	private $my = null;
	private $fd = null;

	public function getAvatar($args = [])
	{
		$this->adapter = FDT::getAdapter(FDT::getMainComponent());
		$this->my = JFactory::getUser();
		$this->fd = FDT::themes()->fd;

		$obj = FH::normalize($args, 'user', '');

		// Users argument can be an array because of group conversation.
		if (!is_array($obj) && method_exists($obj, 'getType') && $obj->getType() !== SOCIAL_TYPE_USER) {
			return $this->cluster($args);
		}

		return $this->user($args);
	}

	public function user($args)
	{
		$config = FDT::config();
		$class = FH::normalize($args, 'class', '');
		$users = FH::normalize($args, 'user', $this->adapter->getUser($this->my->id));
		$size = FH::normalize($args, 'size', 'md');
		$isOnline = false;
		$isMobile = false;
		$name = '';
		$avatarStyle = $config->get('avatar_style', 'rounded');
		$showOnlineState = $config->get('show_online', true);
		$canShowOnlineState = false;

		if (is_array($users) && count($users) === 1) {
			$users = $users[0];
		}

		if (!is_array($users) && $showOnlineState) {
			if (method_exists($users, 'isOnline')) {
				$canShowOnlineState = true;
				$isOnline = $users->isOnline();
			}

			if (method_exists($users, 'isOnlineMobile')) {
				$isMobile = $users->isOnlineMobile();
			}

			$name = $this->my->name;

			if (method_exists($users, 'getName')) {
				$name = $users->getName();
			}
		}

		$fallback = null;
		$wrapperAttributes = null;

		if (is_array($users) && count($users) > 1) {
			$avatar = rtrim(JURI::root(),'/') . '/media/com_easysocial/defaults/avatars/group/large.png';
		} else {
			$avatar = $this->adapter->getAvatar($users->id, 'large');

			// Due to ED hasn't integrate with FD yet, we need to address its text avatar with a temporary fix because text avatar in ED behaves differently
			// This code needs to be removed once it has been addressed in ED #132
			if ($this->adapter->shortName === 'ed' && !$avatar) {
				$userNameInitial = $this->adapter->getUser($users->id)->getNameInitial();
				$fallback = $userNameInitial->text;

				$backgroundColors = [
					'#ffebee',
					'#e1bee7',
					'#bbdefb',
					'#b2dfdb',
					'#ffe0b2'
				];
				$userNameInitialCode = $userNameInitial->code;

				$wrapperAttributes = 'style="background-color: ' . $backgroundColors[$userNameInitialCode - 1] . ';"';
			}
		}

		return $this->fd->html('avatar.' . $size, $avatar, false, [
			'name' => $name,
			'isOnline' => $isOnline,
			'isMobile' => $isMobile,
			'showOnlineState' => $canShowOnlineState && $showOnlineState,
			'style' => $avatarStyle,
			'fallback' => $fallback,
			'wrapperAttributes' => $wrapperAttributes,
		]);
	}

	public function cluster($args)
	{
		$cluster = FH::normalize($args, 'user', null);
		$size = FH::normalize($args, 'size', 'md');

		$avatar = $cluster->getAvatar();
		$name = $cluster->getTitle();
		$avatarStyle = $this->adapter->getAvatarStyle();

		return $this->fd->html('avatar.' . $size, $avatar, false, [
			'name' => $name,
			'style' => $avatarStyle
		]);
	}
}
