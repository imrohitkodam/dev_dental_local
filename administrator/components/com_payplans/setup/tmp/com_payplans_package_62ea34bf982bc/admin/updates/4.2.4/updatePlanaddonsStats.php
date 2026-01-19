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

class PPMaintenanceScriptUpdatePlanaddonsStats extends PPMaintenanceScript
{
	public static $title = "Updating Plan Addon stats";
	public static $description = "Update Plan addon stats.";

	public function main()
	{
		$db = PP::db();

		// Get plan addons stats records

		$query = "select * ";
		$query .= " from `#__payplans_planaddons_stats`";
		$query .= " where `consumed` = 0";

		$db->setQuery($query);

		$results = $db->loadObjectList();

		foreach ($results as $value) {

			$invoice = PP::invoice($value->reference);

			if ($invoice->getId() && $invoice->isPaid()) {
				// Update plan addon state
				$addon = PP::table('AddonStat');
				$addon->load($value->planaddons_stats_id);

				if ($addon->planaddons_stats_id) {
					$addon->consumed = 1;

					$addon->store();
				}
			}
		}
			
		return true;
	}

}
