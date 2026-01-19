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

class PayPlansFormatter extends PayPlans
{
	// Set default template for logs
	public $template = 'view';
	
	public static function writer($previous, $current)
	{	
		$content['previous'] = $previous ? (method_exists($previous, 'toArray') ? $previous->toArray() : (array)$previous ) : [];
		$content['current'] = method_exists($current, 'toArray') ? $current->toArray() : (array)$current;
		
		return $content;
	}
	
	/**
	 * Formats the log content
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatter($content, $type = null)
	{
		// if content is not array convert it in array
		if (!is_array($content)) {
			$content = array($content);
		}

		// if content has previous or current set
		if (array_key_exists('previous', $content) || array_key_exists('current', $content)) {
			
			$data['previous'] = PP::normalize($content, 'previous', []);
			$data['current'] = PP::normalize($content, 'current', []);
			$data['previous'] = array_key_exists('previous', $data['previous']) ? array_pop($data['previous']) : $data['previous'];

			if (method_exists($this, 'getIgnoredata')) {
				$ignore = $this->getIgnoredata();

				foreach ($ignore as $key) {
					unset($data['previous'][$key]);
					unset($data['current'][$key]);
				}
			}
			
			if (method_exists($this, 'getVarFormatter')) {
				$rules = $this->getVarFormatter();
				$this->applyFormatterRules($data,$rules);
			}

			return $data;
		}

		// if content doesn't have previous and current set
		// for email logs and error logs and cron logs
		$data['previous'] = [];
		$data['current'] = $content;

		return $data;
	}
	
	public function callFormatRule($formatter, $functionName, $args)
	{
		//call function on instance or use call_user_func_array
		if ($formatter) {
			
			if (!class_exists($formatter)) {
				// load the formatter class if not loaded yet.
				PP::getFormatter($formatter, '');
			}

			$call = [$formatter, $functionName];
		} else {
			$call = $functionName;
		}

		return call_user_func_array($call,$args);
	}
	
	/**
	 * Apply rules on data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function applyFormatterRules(&$data,$rules)
	{
		$new = [];

		foreach ($data['previous'] as $key => $value) {
			if (array_key_exists($key, $rules)) {
				$args = [&$key, &$value ,$data['previous']];
				$this->callFormatRule($rules[$key]['formatter'],$rules[$key]['function'], $args);	
			}

			$new['previous'][$key] = $value;
		}	
		
		foreach ($data['current'] as $key => $value) {
			if (array_key_exists($key, $rules)) {
				$args = [&$key, &$value,$data['current']];
				$this->callFormatRule($rules[$key]['formatter'],$rules[$key]['function'], $args);	
			}
			$new['current'][$key]= $value;
		}
		
		$data['previous'] = isset($new['previous'])? $new['previous']: '';
		$data['current']  = isset($new['current']) ? $new['current'] : '';
		unset($new);
	}
	
	// format params in all logs
	public function getFormattedParams($key,$value,$data)
	{
		$key = JText::_('COM_PAYPLANS_LOG_KEY_PARAMS');
		
		$params = "";

		if ($value && !is_array($value)) {
			// convert it into array
			$value = PP::makeArray($value);
		}

		if ($value) {
			foreach ($value as $index => $val) {
				$params .= $index . ' = ' . $val . ',';
			}
		}
		
		$params = explode(",", $params);
		$value = implode("<br/>", $params);
	}
}
