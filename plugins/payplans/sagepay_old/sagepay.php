<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		Payplans
* @subpackage	SagePay Payment App
* @contact		payplans@readybytes.in
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Payplans SagePaye Plugin
 * 
 * 
 * @author Team PayPlans
 */
class plgPayplansSagepay extends XiPlugin
{
	public function onPayplansSystemStart()
	{
		//add discount app path to app loader
		$appPath = dirname(__FILE__).DS.'sagepay'.DS.'app';
		PayplansHelperApp::addAppsPath($appPath);

		return true;
	}
}
