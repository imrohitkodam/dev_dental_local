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

require_once(PP_LIB . '/abstract.php');

class PPRenewal extends PPAbstract
{
	/**
	 * Get all renewals
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function loadRenewals()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$model = PP::model('App');
			$options = [
				'type' => 'renewal', 
				'published' => 1
			];

			$results = $model->loadRecords($options);

			if ($results) {
				$_cache = [];
				foreach ($results as $item) {
					$renewal = PP::app($item);
					$_cache[] = $renewal;
				}
			}
		}

		return $_cache;
	}

	/**
	 * process Renewal
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function renew($subscription, $renewalPlan)
	{
		$price = $renewalPlan->getPrice();
		$planDetails = $renewalPlan->getDetails();
		$planType = $renewalPlan->getExpirationType();
		$planDetails->title = $renewalPlan->getTitle();

		// Get Previous order
		$order = $subscription->getOrder();

		// Change plan details as per new(renewal) plan
		$previous = $subscription->getParams()->toArray();
		$new  = $planDetails->toArray();
		$details = array_merge($previous, $new);

		$planParams = new JRegistry($details);
		$subscription->params = $planParams->toString();
		$subscription->save();

		$order->setParam('isRenewal', true)->save();
		$order->refresh()->save();

		// Create new invoice from order
		$invoice = $order->createInvoice();
		$invoice->table->subtotal = $price;

		// set params to invoice
		$subParams 	= $subscription->getParams()->toArray();
		$params = ['expirationtype', 'expiration', 'recurrence_count', 'price', 'title', 'trial_price_1', 'trial_price_2', 'trial_time_1', 'trial_time_2'];

		$params = new JRegistry();
		$params->set('expirationtype', $subParams['expirationtype']);
		$params->set('expiration', $subParams['expiration']);
		$params->set('trial_price_1', $subParams['trial_price_1']);
		$params->set('trial_time_1', $subParams['trial_time_1']);
		$params->set('trial_price_2', $subParams['trial_price_2']);
		$params->set('trial_time_2', $subParams['trial_time_2']);
		$params->set('recurrence_count', $subParams['recurrence_count']);
		$params->set('price', $subParams['price']);
		$params->set('title', $renewalPlan->getTitle());
		$params->set('isRenewal', true);

		$invoice->table->params = $params->toString();

		$invoice->refresh()->save();

		return $invoice;
	}

}
