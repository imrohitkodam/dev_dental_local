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

class PayplansAppFormatter extends PayplansFormatter
{	
	function getIgnoredata()
	{
		$ignore = ['_trigger', '_component', '_name', '_errors', '_tplVars','_location'];
		return $ignore;
	}
	
	/**
	 * override parent applyFormatterRules to handle app_params
	 *  $data is passes through reference
	 */
	public function applyFormatterRules(&$data,$rules)
	{
		$new = array();

		foreach ($data['previous'] as $key => $value)
		{	
			// if there is some rule for that param then apply rule
			if(array_key_exists($key, $rules)){
				$args = [&$key, &$value ,$data['previous']];
				$this->callFormatRule($rules[$key]['formatter'],$rules[$key]['function'], $args);	
			}
			// handling of app params 
			// display all app params in new line 
			if(in_array($key,array('app_params','core_params')))
			{
				foreach($value as $param=>$v){
					$new['previous'][$param]= $v;
				}
				unset($new['previous'][$key]);
				continue;
			}
			$new['previous'][$key]= $value;
		}	
		
		foreach ($data['current'] as $key => $value)
		{
			// if there is some rule for that param then apply rule
			if(array_key_exists($key, $rules)){
				$args = array(&$key, &$value,$data['current']);
				$this->callFormatRule($rules[$key]['formatter'],$rules[$key]['function'], $args);
			}	
			// handling of app params 
			// display all app params in new line 
			if(in_array($key, ['app_params','core_params']))
			{
				foreach($value as $param=>$v){
					$new['current'][$param]= $v;
				}
				unset($new['current'][$key]);
				continue;
			}
			$new['current'][$key]= $value;
		}	
		
		$data['previous'] = isset($new['previous'])? $new['previous']: '';
		$data['current'] = isset($new['current']) ? $new['current'] : '';
		unset($new);
	}
	
	// get rules
	public function getVarFormatter()
	{
		$rules = [
			'_appplans' => [
				'formatter'=> 'PayplansAppFormatter', 
				'function' => 'getAppPlans'
			]
		];
		
		return $rules;
	}
	
}