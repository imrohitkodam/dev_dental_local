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

class PayplansAppEasydiscussticketFormatter extends PayplansAppFormatter
{
	public  $template = 'view_log';

	public function getVarFormatter()
	{
		$rules = array(
					'_appplans' => array(
										'formatter'=> 'PayplansAppFormatter',
										'function' => 'getAppPlans'
									),
					'app_params' => array(
										'formatter'=> 'PayplansAppEasysocialeventsubmissionFormatter',
										'function' => 'getFormattedContent'
									)
				);

		return $rules;
	}

	public function getFormattedContent($key,$value,$data)
	{
		// $value['esgroupOnActive'] = $this->getGroupNames($value['esgroupOnActive']);
		// $value['esgroupOnHold'] = $this->getGroupNames($value['esgroupOnHold']);
		// $value['esgroupOnExpire'] = $this->getGroupNames($value['esgroupOnExpire']);

		$this->template = 'view';
	}
}