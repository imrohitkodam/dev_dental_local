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

class PayplansPlanFormatter extends PayPlansFormatter
{
	public function getIgnoredata()
	{
		$ignore = array('_trigger', '_component', '_name', '_errors','_blacklist_tokens');
		return $ignore;
	}


	public static function getPlanName($key,$value,$data)
	{
		if (!$value) {
			return;
		}

		$plan = PP::plan($value);
		$url = JRoute::_('index.php?option=com_payplans&view=plan&task=edit&id='. $value, false);
		$value = '<a href="' . $url . '" target="_Blank">' . $plan->getTitle() . '</a>';

		return $value;
	}

}