<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Jticketing
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Jticketing is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_jticketing/models/migration.php';

/**
 * com_jticketing Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 * @since       0.0.9
 */
class JTicketingControllerJtmigration extends JControllerForm
{
	public function migrateData1()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->addOldEventVendor();
	}

	public function migrateData2()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->addOldVenueVendor();
	}

	public function migrateData3()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->fixPayoutsTable();
	}

	public function migrateData4()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->addActivity();
	}

	public function migrateData5()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->imageMigration();
	}

	public function migrateData6()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->attendeeMigration();
	}

	public function migrateData7()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->deletDuplicateJlikeData();
	}

	public function migrateData8()
	{
		$migrationModel = JModelLegacy::getInstance('Migration', 'JticketingModel');

		$result = $migrationModel->fixActivityActorid();
	}
}
