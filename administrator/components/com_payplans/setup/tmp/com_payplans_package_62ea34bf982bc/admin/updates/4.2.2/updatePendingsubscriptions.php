<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(PP_LIB . '/maintenance/dependencies.php');

class PPMaintenanceScriptUpdatePendingSubscriptions extends PPMaintenanceScript
{
	public static $title = "Updating Pending Order Subscription data";
	public static $description = "Update Pending order subscription dates.";

	public function main()
	{
		$db = PP::db();

		// Update pending subscription dates
		
		$query = "update `#__payplans_subscription` set";
		$query .= " `subscription_date` = " . $db->Quote('0000-00-00 00:00:00');
		$query .= ", `expiration_date` = " . $db->Quote('0000-00-00 00:00:00');
		$query .= " where `status` = " . $db->Quote(0);
		$query .= " and `subscription_date` != " . $db->Quote('0000-00-00 00:00:00');
		$query .= " and `expiration_date` != " . $db->Quote('0000-00-00 00:00:00');

		$db->setQuery($query);
		$db->query();

		return true;
	}

}
