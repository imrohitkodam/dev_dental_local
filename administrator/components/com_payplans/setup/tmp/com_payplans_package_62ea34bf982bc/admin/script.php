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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_PayPlansInstallerScript
{
	/**
	 * Triggered after the installation is completed
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function postflight()
	{
		ob_start();
		include(__DIR__ . '/setup.html');

		$contents = ob_get_contents();
		ob_end_clean();

		echo $contents;
	}


	public function preflight($type, $parent)
	{
		// Ensure that this is Joomla 3.0
		$joomlaOutdated = version_compare(JVERSION, '3.0') === -1;

		if ($joomlaOutdated) {
			JFactory::getApplication()->enqueueMessage('Payplans requires a minimum of Joomla 3.0 to be installed', 'error');
			return false;
		}

		// Ensure it meet the minimum requirement on upgrade.
		if (!$this->canUpgrade()) {
			JFactory::getApplication()->enqueueMessage('Payplans requires a minimum of Payplans 3.7.1 to be installed on your site before you can upgrade to version 4.0.0', 'error');
			return false;
		}

		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/payplans.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}

		// Remove logs folder
		$this->removeLegacyLogFolder();

		// Disable plugins when upgrading from 3.x
		if ($this->isUpgradeFrom3x()) {
			$this->unInstallLegacyExtensions();
		}

		// now let check the PP config
		$this->checkPPVersionConfig();


		// check if we need to backup the template overrride files
		// when update to PP 5.x
		$this->backupThemeOverrideFiles();

	}

	/**
	 * Responsible to perform the uninstallation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function uninstall()
	{
		// Disable plugins
		$this->unPublishPlugins();
	}

	/**
	 * Responsible to perform component updates
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function update()
	{

	}

	/**
	 * Determine if upgrade is allowed or not.
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function canUpgrade()
	{
		// minimum supported version for upgrade to 4.0.0
		$min_version = '3.7.1';

		$xmlfile = JPATH_ROOT. '/administrator/components/com_payplans/payplans.xml';
		if (JFile::exists($xmlfile)) {

			// if file exists, then this is an upgrade.
			// lets check for existing version.
			$contents = file_get_contents($xmlfile);
			$parser = simplexml_load_string($contents);
			$version = $parser->xpath('version');
			$version = (string) $version[0];

			// if versin lower than 3.7.1, return false.
			if (!version_compare($version, $min_version, '>=')) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Responsible to check if the script need to backup the themes override files or not
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	private function backupThemeOverrideFiles()
	{
		// get current installed eb version.
		$xmlfile = JPATH_ROOT. '/administrator/components/com_payplans/payplans.xml';

		if (JFile::exists($xmlfile)) {
			$contents = file_get_contents($xmlfile);
			$parser = simplexml_load_string($contents);
			$version = $parser->xpath('version');
			$version = (string) $version[0];

			// if current installed version lower than 5.0.1
			if (version_compare($version, '5.0.1', '<')) {

				// now lets check if there is any template override files for PP or not.
				// if yes, lets rename the folder to something else.

				$db = JFactory::getDBO();

				$query = 'SELECT `template` FROM `#__template_styles`';
				$query .= ' WHERE `home` = 1';
				$query .= ' AND `client_id` = 0';

				$db->setQuery($query);

				$curTemplate = $db->loadResult();

				$overridePath = rtrim(JPATH_ROOT, '/') . '/templates/' . $curTemplate . '/html/com_payplans';

				if (JFolder::exists($overridePath)) {

					$backupPath = rtrim(JPATH_ROOT, '/') . '/templates/' . $curTemplate . '/html/com_payplans.outdated';

					//move the overrride folder
					JFolder::move($overridePath, $backupPath);

					// insert a flag so that we can display a proper message after the installation completed.
					$query = 'INSERT INTO ' . $db->quoteName('#__payplans_config') . ' (`key`, `value`) VALUES';
					$query .= ' (' . $db->Quote('show_override_outdated') . ',' . $db->Quote('1') . ')';

					$db->setQuery($query);

					if (method_exists($db, 'query')) {
						$db->query();
					} else {
						$db->execute();
					}

				}
			}
		}
	}

	/**
	 * Responsible to check pp configs db version
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function checkPPVersionConfig()
	{
		// if there is the config table but no dbversion, we know this upgrade is coming from pior 4.0. lets add on db_version into config table.
		if ($this->isUpgradeFrom3x()) {

			// get current installed eb version.
			$xmlfile = JPATH_ROOT. '/administrator/components/com_payplans/payplans.xml';

			// set this to version prior 3.5.5 so that it will execute the db script from 3.6.x as well incase
			// this upgrade is from very old version.
			$version = '3.5.5';

			if (JFile::exists($xmlfile)) {
				$contents = file_get_contents($xmlfile);
				$parser = simplexml_load_string($contents);
				$version = $parser->xpath('version');
				$version = (string) $version[0];
			}

			$db = JFactory::getDBO();

			// ok, now we got the version. lets add this version into dbversion.
			$query = 'INSERT INTO ' . $db->quoteName('#__payplans_config') . ' (`key`, `value`) VALUES';
			$query .= ' (' . $db->Quote('db_version') . ',' . $db->Quote($version) . '),';
			$query .= ' (' . $db->Quote('script_version') . ',' . $db->Quote($version) . ')';

			$db->setQuery($query);

			if (method_exists($db, 'query')) {
				$db->query();
			} else {
				$db->execute();
			}

		}
	}

	/**
	 * Check if this is an upgrade from version 3.x
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '%" . $prefix . "payplans_config%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('value') . ' FROM ' . $db->quoteName('#__payplans_config') . ' WHERE ' . $db->quoteName('key') . '=' . $db->Quote('db_version');
				$db->setQuery($query);

				$exists = $db->loadResult();
				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}

	/**
	 * Unpublish Payplans legacy extensions
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function unInstallLegacyExtensions()
	{
		// components
		// PPInstaller extensions
		// RBInstaller extensions
		$this->unInstallLegacyComponents();

		// plugins - systems
		// rbsl
		$this->unInstallLegacyPlugins();
	}

	/**
	 * Unpublish Legacy Payplans extensions from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function unInstallLegacyComponents()
	{
		$db = JFactory::getDBO();

		$extensions = array('component' => array('com_ppinstaller', 'com_rbinstaller'));

		foreach ($extensions as $type => $elements) {

			$tempElements = '';

			foreach ($elements as $element) {
				$tempElements .= ($tempElements) ? ',' . $db->Quote($element) : $db->Quote($element);
			}


			$query = "update `#__extensions` set `enabled` = 0";
			$query .= " WHERE `type` = " . $db->Quote($type);
			$query .= " AND `element` IN (" . $tempElements . ")";

			$db->setQuery($query);
			
			if (method_exists($db, 'query')) {
				$db->query();
			} else {
				$db->execute();
			}

			// $query = "select `extension_id` from `#__extensions`";
			// $query .= " WHERE `type` = " . $db->Quote($type);
			// $query .= " AND `element` IN (" . $tempElements . ")";


			// $db->setQuery($query);
			// $items = $db->loadColumn();

			// if ($items) {
			// 	foreach ($items as $eid) {

			// 		$installer = JInstaller::getInstance();
			// 		$state = $installer->uninstall($type, $eid);

			// 		if (!$state) {
			// 			// uninstallation failed. lets just unpublish this plugin.
			// 			$query = "update `#__extensions` set `enabled` = 0";
			// 			$query .= " where `extension_id` = " . $db->Quote($eid);

			// 			$db->setQuery($query);
			// 			$db->query();
			// 		}

			// 	}
			// }
		}

		return true;
	}

	/**
	 * Unpublish Legacy Payplans plugins from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function unInstallLegacyPlugins()
	{
		$db = JFactory::getDBO();

		$plugins = array('system' => array('rbsl', 'payplanslogincontroller'));

		foreach ($plugins as $folder => $elements) {

			$tempElements = '';

			foreach ($elements as $element) {
				$tempElements .= ($tempElements) ? ',' . $db->Quote($element) : $db->Quote($element);
			}


			$query = "update `#__extensions` set `enabled` = 0";
			$query .= " WHERE `folder` = " . $db->Quote($folder);
			$query .= " AND `element` IN (" . $tempElements . ")";
			$query .= " AND `type` = " . $db->Quote('plugin');

			$db->setQuery($query);
			
			if (method_exists($db, 'query')) {
				$db->query();
			} else {
				$db->execute();
			}

			// $query = "select `extension_id` from `#__extensions`";
			// $query .= " WHERE `folder` = " . $db->Quote($folder);
			// $query .= " AND `element` IN (" . $tempElements . ")";
			// $query .= " AND `type` = " . $db->Quote('plugin');

			// $db->setQuery($query);
			// $items = $db->loadColumn();

			// if ($items) {
			// 	foreach ($items as $eid) {

			// 		$installer = JInstaller::getInstance();
			// 		$state = $installer->uninstall('plugin', $eid);

			// 		if (!$state) {
			// 			// uninstallation failed. lets just unpublish this plugin.
			// 			$query = "update `#__extensions` set `enabled` = 0";
			// 			$query .= " where `extension_id` = " . $db->Quote($eid);

			// 			$db->setQuery($query);
			// 			$db->query();
			// 		}

			// 	}
			// }
		}

		// remove /media/plg_system_rbsl folder.
		$folder = JPATH_ROOT . '/media/plg_system_rbsl';
		// if (JFolder::exists($folder)) {
		// 	JFolder::delete($folder);
		// }

		return true;
	}


	/**
	 * Unpublish Payplans plugins from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function unPublishPlugins()
	{
		$db = JFactory::getDBO();

		$pluginNames = array(
				'system' => array('payplans')
			);

		foreach ($pluginNames as $folder => $elements) {

			$tempElements = '';

			foreach ($elements as $element) {
				$tempElements .= ($tempElements) ? ',' . $db->Quote($element) : $db->Quote($element);
			}

			// jos_modules
			$query = array();
			$query[] = 'UPDATE ' . $db->quoteName('#__extensions') . ' SET ' . $db->quoteName('enabled') . '=' . $db->Quote('0');
			$query[] = 'WHERE ' . $db->quoteName('folder') . '=' . $db->Quote($folder);
			$query[] = 'AND ' . $db->quoteName('element') . ' IN (' . $tempElements . ')';

			$query = implode(' ', $query);

			$db->setQuery($query);
			$state = false;

			if (method_exists($db, 'query')) {
				$state = $db->query();
			} else {
				$state = $db->execute();
			}

		}

		return true;
	}

	/**
	 * Removes legacy log folder
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public function removeLegacyLogFolder()
	{
		$legacyFolder = JPATH_ROOT . '/media/payplans';

		if (JFolder::exists($legacyFolder)) {
			JFolder::delete($legacyFolder);
		}
	}
}
