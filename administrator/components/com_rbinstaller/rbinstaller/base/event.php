<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Front-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Base Event
 * @author Gaurav Jain
 */
class RBInstallerEvent extends JEvent
{
	
}

$dispatcher = JDispatcher::getInstance();
//$dispatcher->register('onRBInstallerCron', 'RBInstallerEvent');

