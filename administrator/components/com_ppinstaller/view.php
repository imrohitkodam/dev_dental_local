<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	PayPlans-Installer
* @contact 		payplans@readybytes.in
*/

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

/**
 * PayPlans Installer Controller
 *
 * @package		Joomla.Administrator
 */

if(!class_exists('PpinstallerViewAdapt')) {
	if(interface_exists('JView')) {
		abstract class PpinstallerViewAdapt extends JViewLegacy {			
		}
		
	} else {
		class PpinstallerViewAdapt extends JView {
		}
	}
}
