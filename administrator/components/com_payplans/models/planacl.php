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

PP::import('admin:/includes/model');

class PayplansModelPlanacl extends PayPlansModel
{
	public function __construct()
	{
		parent::__construct('planacl');
	}

	public function clearAcl($planId)
	{
		$db = $this->db;

		$query = 'DELETE FROM `#__payplans_plan_acl` WHERE `plan_id` = ' . $db->Quote($planId);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function getAcl($planId)
	{
		$db = $this->db;

		$query = 'SELECT * FROM `#__payplans_plan_acl` WHERE `plan_id` = ' . $db->Quote($planId);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}

