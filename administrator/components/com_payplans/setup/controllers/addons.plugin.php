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

class PayplansControllerAddonsPlugin extends PayplansSetupController
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
		$path = $this->input->get('path', '', 'default');
		$group = $this->input->get('group', '', 'default');

		if (!$path || !$group) {
			$this->setInfo('Missing plugin path or plugin group value!', false);
			return $this->output();
		}

		$pluginGroupPath = $path . '/' . $group;

		$plugins = JFolder::folders($pluginGroupPath, '.', false, true);

		if ($plugins) {
			foreach ($plugins as $plg) {

				$installer = JInstaller::getInstance();
				$installer->setOverwrite(true);

				// Prevent any output from the installer
				ob_start();

				$state = $installer->install($plg);

				ob_end_clean();

				// Ensure that the plugins are published
				if ($state) {

					$element = basename($plg);

					$group = strtolower($group);
					$element = strtolower($element);

					$options = array('folder' => $group, 'element' => $element);

					$plugin = JTable::getInstance('Extension');
					$plugin->load($options);

					// set the state to 0 means 'installed'.
					$plugin->state = 0;
					$plugin->enabled = true;
					$plugin->store();
				}
			}
		}

		$result = $this->getResultObj(JText::_('Plugins under ' . ucfirst($group) . ' group installed on the site'), true);

		return $this->output($result);
	}
}
