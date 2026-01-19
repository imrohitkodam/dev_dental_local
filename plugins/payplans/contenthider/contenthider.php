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

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';
if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansContenthider extends PPplugins
{	
	public $user;
	public $regex = "#{payplans(.*?)}(.*?){/payplans}#s";
	
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$text = is_object($row) ? $row->text : $row;

		// Prepare matches using regex on text.
		$matches = $this->prepareMatches($text);

		if (!$matches) {
			return true;
		}

		// There are afew syntax can be found in an article.
		// We'll loop for each of syntax found.
		foreach ($matches[0] as $index => $raw) {
			// Repopulate match.
			$match = array(
				$matches[0][$index],	// Raw
				$matches[1][$index],	// Params
				$matches[2][$index]		// Hidden content
			);

			$text = str_replace($matches[0][$index], $this->process($match), $text);
		}

		if (is_object($row)) {
			$row->text = $text;
			return true;
		}

		$row = $text;
		return true;
	}

	public function prepareMatches($text)
	{
		$matches = '';

		// strpos will return string index. 
		$position = PPJString::strpos($text, '{payplans');

		// Therefore, checking should be "===" on boolean because of if string found at index 0 it will return true.
		// Example: "{payplans} The 2010s..."
		if ($position === false) {
			// If syntax is not available, straightly return.
			return $matches;
		}

		// Check against default regex. It not available, it could be syntax given is not complete.
		// Example: 
		// 1. {payplans}hidden content{/payplans}	- (complete syntax)
		// 2. {payplans}hidden cont... 				- (truncated syntax because of introtext)
		$found = preg_match_all($this->regex, $text, $matches);

		if (!$found) {
			// If not found, this means syntax given is truncated.
			// Thus, we'll be using modified regex.
			$regex = "#{payplans(.*?)}(.*)#s";
			preg_match_all($regex, $text, $matches);
		}

		return $matches;
	}

	public function getUserPlans($idsOnly = false)
	{
		$userPlans = $this->user->getPlans();

		if ($idsOnly) {
			$plans = array();

			foreach ($userPlans as $plan) {
				$plans[] = $plan->getId();
			}

			return $plans;
		}

		return $userPlans;
	}

	public function getPlans($idsOnly = false)
	{
		$plans = PP::plan()->getPlans(true, true);

		if ($idsOnly) {
			$planids = array();

			foreach ($plans as $plan) {
				$planids[] = $plan->getId();
			}

			return $planids;
		}

		return $plans;
	}

	/**
	 * Process each match
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function process($match)
	{
		// the process logic as of 05 June 2020
		// with SHOW flag
		// {payplans plan_id=1 SHOW} Content A {/payplans}
		/*
		 * The above syntax can be intepreted as:
		 * Show the content within the shortcode if user subscribed to plan 1
		 * Hide the content if user did not subscribed to plan 1
		 */

		// with HIDE flag
		// {payplans plan_id=1 HIDE} Content B {/payplans}
		/*
		 * The above syntax can be intepreted as:
		 * Hide the content within the shortcode if user subscribed to plan 1
		 * Show the content if user did not subscribed to plan 1
		 */

		// without any flag
		// {payplans plan_id=1} Content C {/payplans}
		/*
		 * The above syntax can be intepreted as:
		 * Show the content within the shortcode if user subscribed to plan 1
		 * Show the plan subscribe links (restricted content) if user did not subscribed to plan 1
		 */

		$raw = isset($match[0]) ? $match[0] : false;
		$params = isset($match[1]) ? $match[1] : false;
		$hiddenContent = isset($match[2]) ? $match[2] : false;

		// Determine whether there is hidden content available.
		if (!$hiddenContent) {
			return;
		}

		// Get user properties.
		$this->user = PP::user();

		// Return everything for admin.
		if ($this->user->isAdmin()) {
			return $hiddenContent;
		}

		// Retrieve all available plans.
		$plans = $this->getPlans(true);

		// Returns if plans is not available.
		if (!$plans) {
			return $hiddenContent;
		}

		// Process hidden contents.
		$flag = ''; // this is when there is no flag passed in.
		$restrictedPlan = array();

		// check for the flag and plan ids.
		if ($params) {

			$restrictions = explode('=', trim($params));

			// Determine whether plan_id is provided.
			if ($restrictions && $restrictions[0] != '') {

				if (isset($restrictions[1])) {
					$args = isset($restrictions[1]) ? preg_replace("/\s|&nbsp;/", '', $restrictions[1]) : '';
				} else {
					$args = isset($restrictions[0]) ? preg_replace("/\s|&nbsp;/", '', $restrictions[0]) : '';
				}

				if (stripos($args, 'hide') !== false) {
					$flag = 'hide';
				}

				if (stripos($args, 'show') !== false) {
					$flag = 'show';
				}

				$args = str_ireplace($flag, '', $args);
				$restrictedPlan = $args ? explode(',', $args) : array();

				// Reset action ?? why we need to reset action?
				// $flag = isset($args[2]) ? isset($args[2]) : $flag;
			}
		}

		// Retrieve user's subscribed plan ids.
		$userPlans = $this->getUserPlans(true);

		// Determine whether user has subscribed to restricted plan_id.
		$compare = array_intersect($plans, $userPlans);

		if ($restrictedPlan) {
			$compare = array_intersect($compare, $restrictedPlan);
		}

		$planCompareCount = count($compare);

		if ($flag) {
			if (($flag == 'show' && $planCompareCount) || ($flag == 'hide' && !$planCompareCount)) {
				return $hiddenContent;
			}

			if (($flag == 'show' && !$planCompareCount) || ($flag == 'hide' && $planCompareCount)) {
				return '';
			}
		}

		// from this points onward, this mean the shortcode has no flag passed in.
		// we need to check if user has any subscriptions or not.
		if (($restrictedPlan && $planCompareCount) || (!$restrictedPlan && $userPlans)) {
			return $hiddenContent;
		}

		// if reached here, mean we should show restricted content (the plans subcribe links)

		// If restricted plan is not provided.
		// Just show all plans.
		$showAll = true;
		
		if (!$restrictedPlan) {
			$showAll = false;
		}

		// Removing remaining plan if restricted plan is available.
		$remainingPlans = array_intersect($plans, $restrictedPlan);

		if (!$remainingPlans) {
			$remainingPlans = $plans;
		}

		// Load plans.
		$plans = array();
		foreach ($remainingPlans as $p) {
			$plans[] = PP::plan($p);
		}

		$this->set('showAll', $showAll);
		$this->set('plans', $plans);
		$this->set('user', $this->user);
		$this->set('loginUrl', $this->getLoginUrl());
		return $this->output('restricted');

	}

	public function getLoginUrl()
	{
		$component = $this->input->get('option', '', 'cmd');

		if ($component == 'com_easyblog') {
			$config = EB::config();
			$provider = $config->get('main_login_provider');

			$currentUri = EBR::getCurrentURI();
			$returnURL = '?return=' . base64_encode($currentUri);

			$url = EB::_('index.php?option=com_easyblog&view=login', false) . $returnURL;

			if ($provider == 'easysocial' && EB::easysocial()->exists()) {
				$url = ESR::login(array(), false) . $returnURL;
			}

			return $url;
		}

		return JRoute::_('index.php?option=com_users&view=login', false);
	}
}
