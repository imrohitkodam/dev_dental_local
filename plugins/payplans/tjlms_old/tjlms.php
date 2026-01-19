<?php
/**
 * @package     Payplans.Plugin
 * @subpackage  Payplans.app
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Payplans Tjlms Plugin
 *
 * @since  1.6
 */
class PlgPayplansTjlms extends XiPlugin
{
	/**
	 * Add tjlms app path to app loader
	 *
	 * @return  avoid
	 *
	 * @since   1.6
	 */

	public function onPayplansSystemStart()
	{
		if (!JFolder::exists(JPATH_SITE . DS . 'components' . DS . 'com_tjlms'))
		{
			return false;
		}

		// Add app path to app loader
		$appPath = dirname(__FILE__) . DS . 'tjlms' . DS . 'app';
		PayplansHelperApp::addAppsPath($appPath);

		return true;
	}
}
