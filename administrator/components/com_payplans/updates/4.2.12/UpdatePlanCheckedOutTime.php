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

class PPMaintenanceScriptUpdatePlanCheckedOutTime extends PPMaintenanceScript
{
	public static $title = "Updating plan checked out value";
	public static $description = "Updating plan checked out value";

	public function main()
	{
		$db = PP::db();

		$query = "update `#__payplans_plan`";
		$query .= " set `checked_out_time` = NULL";
		$query .= " where `checked_out_time` IS NOT NULL";

		$db->setQuery($query);
		$db->query();
			
		$query = "update `#__payplans_plan`";
		$query .= " set `checked_out` = NULL";
		$query .= " where `checked_out` IS NOT NULL";

		$db->setQuery($query);
		$db->query();

		return true;
	}

}
