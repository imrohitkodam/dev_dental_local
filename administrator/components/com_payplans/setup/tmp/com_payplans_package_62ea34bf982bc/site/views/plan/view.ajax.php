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

class PayplansViewPlan extends PayPlansSiteView
{
	/**
	 * Retrieve the recent purchased plans on the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getPurchasedPlans()
	{
		if (!$this->config->get('enable_purchased_popup') || PP::responsive()->isMobile() && !$this->config->get('purchased_popup_mobile')) {
			return $this->ajax->reject('COM_PP_FEATURE_DISABLED');
		}

		$options = [
			'hidePendingOrder' => true,
			'ordering' => 'subscription_date',
			'limit' => $this->config->get('purchased_popup_total'),
			'delay' => $this->config->get('purchased_popup_delay'),
			'duration' => $this->config->get('purchased_popup_duration'),
			'loop' => $this->config->get('purchased_popup_loop'),
			'interval' => $this->config->get('purchased_popup_interval'),
			'duration' => $this->config->get('purchased_popup_lapsed_duration'),
			'position' => $this->config->get('purchased_popup_position')
		];

		$excludeBuyerId = $this->input->get('excludeBuyerId', 0, 'int');

		if ($excludeBuyerId) {
			$options['excludeUserIds'] = $excludeBuyerId;
		}

		$model = PP::model('Subscription');
		$subscriptions = $model->getItemsWithoutState($options);

		if (empty($subscriptions)) {
			return $this->ajax->resolve([]);
		}

		$plans = [];

		$options['total'] = count($subscriptions);

		foreach ($subscriptions as $subscription) {
			$buyer = $subscription->getBuyer();
			$plan = $subscription->getPlan();
			$purchaser = $this->config->get('purchased_popup_purchaser') ? JText::sprintf('COM_PP_SALES_POPUP_PURCHASER', $buyer->getName()) : JText::_('COM_PP_SOMEONE');
			$country = $this->config->get('purchased_popup_purchaser_country') && $buyer->getCountryLabel();

			if ($country) {
				$purchaser = JText::sprintf('COM_PP_SALES_POPUP_PURCHASER_WITH_COUNTRY', $purchaser, $buyer->getCountryLabel());
			}

			$permalink = PPR::_('index.php?options=com_payplans&view=plan&from=popup&plan_id=' . $plan->getId());
			$groups = $plan->getGroups();
			$groupId = null;

			if (!empty($groups)) {
				// If the plan is associated to one of the groups, just get the first one
				$groupId = $groups[0];
				$permalink .= '&group_id=' . $groupId;
			}

			$obj = new stdClass();
			$obj->id = $plan->getId();
			$obj->message = JText::sprintf('COM_PP_SALES_POPUP_MESSAGE', $purchaser, $plan->getTitle(), $permalink);
			$obj->avatar = $buyer->getAvatar();

			if ($this->config->get('purchased_popup_lapsed')) {
				$obj->subscribed = $subscription->getSubscriptionDate()->toLapsed();
			}

			$plans[] = $obj;
		}

		// Remove unnecessary options
		unset($options['hidePendingOrder']);
		unset($options['ordering']);
		unset($options['limit']);

		return $this->ajax->resolve($plans, $options);
	}
}