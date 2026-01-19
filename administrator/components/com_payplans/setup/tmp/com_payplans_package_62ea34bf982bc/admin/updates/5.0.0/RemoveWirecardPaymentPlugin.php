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

class PPMaintenanceScriptRemoveWirecardPaymentPlugin extends PPMaintenanceScript
{
	public static $title = "Removing wirecard payment plugin";
	public static $description = "Removing wirecard payment plugin as wirecard insolvent";

	public function main()
	{
		// 1. we need to remove plugin
		// 3. remove app from databse

		$db = PP::db();

		$query = "select * from `#__extensions`";
		$query .= " where `folder` = " . $db->Quote('payplans');
		$query .= " and `element` = " . $db->Quote('wirecard');
		$query .= " and `type` = " . $db->Quote('plugin');

		$db->setQuery($query);
		$plugin = $db->loadObject();

		if ($plugin) {

			$extensionId = $plugin->extension_id;

			// check if these app exits or not.
			$query = 'select count(1) from `#__payplans_app` where `type` = ' . $db->Quote('wirecard');
			$db->setQuery($query);
			$exists = $db->loadResult();

			// delete app instance from databse if created
			if ($exists) {
				$query = 'DELETE FROM ' . $db->quoteName('#__payplans_app');
				$query .= ' WHERE ' . $db->quoteName('type') . '=' . $db->Quote('wirecard');
				
				$db->setQuery($query);
				$db->query();
			}

			if ($extensionId) {
				$installer = JInstaller::getInstance();
				$state = $installer->uninstall('plugin', $extensionId);

				if (!$state) {
					// uninstallation failed. lets just unpublish this plugin.
					$query = "update `#__extensions` set `enabled` = 0";
					$query .= " where `extension_id` = " . $db->Quote($extensionId);

					$db->setQuery($query);
					$db->query();
				}
			}
		}

		return true;
	}

}
