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

class KunenaPrivateEasySocial extends KunenaPrivate
{
	protected $loaded = false;
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;

		ES::initialize();
	}

	/**
	 * Retrieves the link for inbox
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getInboxLink($text)
	{
		if (!$text) {
			$text = JText::_('COM_KUNENA_PMS_INBOX');
		}

		$url = $this->getInboxURL();

		return '<a href="' . $url . '" rel="follow">' . $text . '</a>';
	}

	/**
	 * Generates the links to conversations
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getInboxURL()
	{
		return ESR::conversations();
	}

	/**
	 * Kunena would trigger the onclick event so that the plugin could inject the data attributes
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	protected function getOnClick($userid)
	{
		$userid = (int) $userid;

		return ' data-es-conversations-compose data-es-conversations-id="' . $userid . '"';
	}

	/**
	 * This trigger would populate the href="" in the pm link
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	protected function getURL($id)
	{
		return "javascript:void(0)";
	}
}
