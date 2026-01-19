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

require_once(__DIR__ . '/controller.php');

class PayplansControllerInstallToolbar extends PayplansSetupController
{
	/**
	 * Perform installation of Toolbar Package
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function execute()
	{
		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_PP_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// There should be a queries.zip archive in the archive.
		$tmpToolbarPath = $tmpPath . '/pkg_toolbar.zip';

		$package = JInstallerHelper::unpack($tmpToolbarPath);

		$installer = JInstaller::getInstance();
		$installer->setOverwrite(true);

		$state = $installer->install($package['dir']);

		if (!$state) {
			// If for some reason, the toolbar package failed, flag it.
			$this->updateConfig('toolbar_installation_failed', "1");

			$result = $this->getResultObj(JText::_('Unable to complete installation of Stackideas Toolbar. Manual installation of the module is required'), true);
			return $this->output($result);

		}

		// Clean up the installer
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		$result = $this->getResultObj(JText::_('Stackideas Toolbar Package installed on the site'), true);

		return $this->output($result);
	}
}
