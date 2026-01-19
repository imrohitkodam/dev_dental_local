<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(PP_LIB . '/maintenance/dependencies.php');

class PPMaintenanceScriptReinstallPluginAuthentication extends PPMaintenanceScript
{
	public static $title = "Reinstall PayPlans authentication plugin";
	public static $description = "To ensure system install payplans authentication plugin.";

	public function main()
	{
		$pluginName = 'payplans';
		$groupName = 'authentication';
		$pluginPath = rtrim(JPATH_ROOT, '/') . '/plugins/' . $groupName . '/' . $pluginName;

		$langPath = rtrim(JPATH_ROOT, '/') . '/administrator/language/en-GB';
		$langfilePath = $langPath . '/en-GB.plg_authentication_payplans.ini';
		$langsysfilePath = $langPath . '/en-GB.plg_authentication_payplans.sys.ini';

		// check if files exists or not.
		$folderExists = JFolder::exists($pluginPath);
		$xmlExists = JFile::exists($pluginPath . '/' . $pluginName . '.xml');
		$langFileExists = JFile::exists($langfilePath);

		// make sure local has all the required files before we proceed.
		if ($folderExists && $xmlExists && $langFileExists) {

			// We need to try to load the plugin first to determine if it really exists
			$plugin = JTable::getInstance('extension');
			$options = array('folder' => strtolower($groupName), 'element' => strtolower($pluginName));
			$plgExists = $plugin->load($options);

			if (!$plgExists) {

				$now = PP::date()->toSql();

				// copy folder to Joomla tmp folder
				$tmpRootPath = rtrim(JPATH_ROOT, '/') . '/tmp/' . md5($now);
				$tmpPath = $tmpRootPath . '/' . $pluginName;
				JFolder::copy($pluginPath, $tmpPath);

				// now copy language files into this tmp folder.
				JFile::copy($langfilePath, $tmpPath . '/en-GB.plg_authentication_payplans.ini');
				JFile::copy($langsysfilePath, $tmpPath . '/en-GB.plg_authentication_payplans.sys.ini');
				
				// Get Joomla's installer instance
				$installer = new JInstaller();

				// Allow overwriting existing plugins
				$installer->setOverwrite(true);
				$state = $installer->install($tmpPath);

				// delete tmp folder
				JFolder::delete($tmpRootPath);

				$plugin->load($options);
			}

			// Load the plugin and ensure that it's published
			$plugin->enabled = true;
			$plugin->store();
		}

		return true;
	}
}
