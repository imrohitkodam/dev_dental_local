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

class PayplansAppEmailFormatter extends PayplansAppFormatter
{
	public $template = 'view_log';

	// get Ignore data 
	public function getIgnoredata()
	{
		$ignore = ['_trigger', '_tplVars', '_mailer', '_location', '_errors', '_component'];
		return $ignore;
	}
	
	public function getVarFormatter()
	{
		$rules = [
			'_appplans' => [
				'formatter'=> 'PayplansAppFormatter', 
				'function' => 'getAppPlans'
			],

			'app_params' => [
				'formatter'=> 'PayplansAppEmailFormatter', 
				'function' => 'getFormattedContent'
			]
		];

		return $rules;
	}

	/**
	 * Format email app content,status, expiration time 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFormattedContent($key, $value, $data)
	{
		foreach ($value as $param => $v) {
			
			if ($param === 'content') {
				$value[$param] = base64_decode($v);
			}

			if ($param === 'on_status') {
				$value[$param] = PP::string()->getStatusName($v);
			}

			if (($param === 'on_preexpiry' || $param === 'on_postexpiry' || $param === 'on_postactivation' || $param === 'on_preexpiry_trial') && !empty($v)) {
				$rawTime = PPHelperPlan::convertIntoTimeArray($v);
				$value[$param] = PP::themes()->html('html.plantime', $rawTime);
			}
		}
		
		$this->template = 'view';
	}
}