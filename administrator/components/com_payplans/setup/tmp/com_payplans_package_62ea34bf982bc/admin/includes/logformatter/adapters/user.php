<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PayplansUserFormatter extends PayplansFormatter
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getIgnoredata()
	{
		$ignore = array('_trigger', '_component', '_errors', '_name', '_blacklist_tokens','_transactions');
		return $ignore;
	}

	/**
	 * Get user link
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getBuyerName($key, &$value, $data)
	{
		if (!$value) {
			return;
		}

		// $user = JFactory::getUser($value);
		// $name = $user->name;

		$url = JRoute::_('index.php?option=com_payplans&view=user&layout=form&id='. $value, false);
		$value = '<a href="' . $url . '" target="_Blank">' . $value . '</a>';

		return $value;
	}
}