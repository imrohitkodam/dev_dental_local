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

class PPMaintenanceScriptUpdateSubscriptionOrderStatus extends PPMaintenanceScript
{
	public static $title = "Updating Expired subscription order status";
	public static $description = "Update expired subscription order stats.";

	public function main()
	{
		$db = PP::db();

		$query = "update `#__payplans_order` o, `#__payplans_subscription` s";
		$query .= " set o.`status` = " . $db->Quote(305);
		$query .= " where o.`status` = s.`order_id`";
		$query .= " and s.`status` = ". $db->Quote(1603);

		$db->setQuery($query);
		$db->query();
			
		return true;
	}

}
