<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;

class Kunena6AvatarEasySocial extends KunenaAvatar
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Generates the edit url for EasySocial
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getEditURL(): string
	{
		$url = ESR::profile(array('layout' => 'edit'));

		return $url;
	}

	/**
	 * Generates the avatar url from EasySocial
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
	{
		$user = KunenaFactory::getUser($user);
		$user = ES::user($user->userid);

		$avatar = $user->getAvatar(SOCIAL_AVATAR_SQUARE);

		return $avatar;
	}
}
