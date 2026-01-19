<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ToolbarAdapterPayplans extends ToolbarAdapter
{
	public $component = 'com_payplans';
	public $shortName = 'pp';
	public $jsName = 'PayPlans';

	public function __construct()
	{
		// Ensure that Payplans is loaded in the page
		require_once(JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php');
	}
	
	public function getQueryName()
	{		
		return 'q';
	}

	public function config()
	{
		return PP::config();
	}

	public function getUser($id = null)
	{
		$user = PP::user($id);
		return $user;
	}
}
