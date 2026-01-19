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

class PayplansControllerAddonsModule extends PayplansSetupController
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
		$module = $this->input->get('module', '', 'default');

		if (!$path || !$module) {
			$this->setInfo('Missing module path or module name!', false);
			return $this->output();
		}

		$modulePath = $path . '/' . $module;

		// Get Joomla's installer instance
		$installer = new JInstaller();

		// Allow overwriting existing modules
		$installer->setOverwrite(true);

		// Prevent any output from the installer
		ob_start();
		// Install the module
		$state = $installer->install($modulePath);
		ob_end_clean();

		if (!$state) {
			$result = $this->getResultObj(JText::_('Module ' . ucfirst($module) . ' failed to install on the site'), true);
			return $this->output($result);

		}

		$db = JFactory::getDBO();

		$query = array();
		$query[] = 'UPDATE ' . $db->qn('#__extensions') . ' SET ' . $db->qn('access') . '=' . $db->Quote(1);
		$query[] = 'WHERE ' . $db->qn('type') . '=' . $db->Quote('module');
		$query[] = 'AND ' . $db->qn('element') . '=' . $db->Quote($module);
		$query[] = 'AND ' . $db->qn('access') . '=' . $db->Quote(0);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$this->ppQuery($db);

		// Check if this module already exists on module_menu
		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ', b.' . $db->qn('moduleid') . ' FROM ' . $db->qn('#__modules') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__modules_menu') . ' AS b ON a.' . $db->qn('id') . ' = b.' . $db->qn('moduleid');
		$query[] = 'WHERE a.' . $db->qn('module') . ' = ' . $db->Quote($module);
		$query[] = 'AND b.' . $db->qn('moduleid') . ' IS NULL';

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			$result = $this->getResultObj(JText::_('Module ' . ucfirst($module) . ' installed on the site'), true);
			return $this->output($result);
		}

		foreach ($result as $row) {
			$mod = new stdClass();
			$mod->moduleid = $row->id;
			$mod->menuid = 0;

			$db->insertObject('#__modules_menu', $mod);
		}

		$result = $this->getResultObj(JText::_('Module ' . ucfirst($module) . ' installed on the site'), true);

		return $this->output($result);

	}
}
