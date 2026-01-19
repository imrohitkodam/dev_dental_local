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

PP::import('site:/views/views');
PP::import('admin:/includes/upgrade/upgrade');

class PayPlansViewOrder extends PayPlansSiteView
{
	/**
	 * Displays upgrade details without creating a new order
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function calculatePriceVariation()
	{
		PP::requireLogin();

		$key = $this->input->get('key', '', 'default');
		$planId = $this->input->get('id', 0, 'int');

		$orderId = PP::getIdFromInput('key');
		$curOrder = PP::order($orderId);

		// check if user is the buyer or not
		$buyer = $curOrder->getBuyer();
		
		if ($buyer->id != $this->my->id && !$this->my->isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_UPGRADE_NOT_ALLOWED'));
		}

		$curSub = $curOrder->getSubscription();
		$availablePlans = PPUpgrade::findAvailableUpgrades($curSub);

		if (!$planId || !isset($availablePlans[$planId])) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_INVALID_PLAN'));
		}

		$curPlan = $curSub->getPlan();
		$curOrderInvoices = $curOrder->getInvoices(PP_INVOICE_PAID);
		$currentInvoice = array_pop($curOrderInvoices);
		
		$newPlan = PP::plan($planId);

		$priceVariationString = $this->input->get('priceVariation', '', 'cmd');

		if (!$priceVariationString) {
			die('No price variation provided');
		}

		if ($priceVariationString == 'default') {
			$payableAmount = $newPlan->getPrice();
			$planPrice = $newPlan->getPrice();
		}

		if ($priceVariationString != 'default') {
			$planPriceVariation = PP::planPriceVariation($priceVariationString);
			$priceVariationData = $planPriceVariation->parse($priceVariationString);
			
			if (!$planPriceVariation->hasPrice($priceVariationData->price)) {
				die('Invalid price provided');
			}

			$payableAmount = $priceVariationData->price;
			$planPrice = $priceVariationData->price;
		}

		$discounts = PPUpgrade::calculateDiscounts($newPlan, $payableAmount, $currentInvoice);
		$taxes = PPUpgrade::calculateTaxes($currentInvoice, $planPrice);
		$result = PPUpgrade::calculateUnutilizedValue($curSub, $curPlan, $newPlan);

		$paidAmount = $result['paid'];
		$unutilized = $result['unutilized'];
		$unutilizedTax = $result['unutilizedTax'];
		$isActivateProration = $result['isActivateProration'];

		$response = new stdClass();

		$payableAmount = ($planPrice - $unutilized - $unutilizedTax - $discounts);
		$payableAmount += $taxes;

		$themes = PP::themes();

		$payableAmount = ($payableAmount < 0) ? 0 : $payableAmount;
		$response->amount = $planPrice;
		$response->currency = $newPlan->getCurrency();

		$response->price = $themes->html('html.amount', $planPrice, $response->currency);
		$response->unutilized = $themes->html('html.amount', $unutilized, $response->currency);
		$response->unutilizedTax = $themes->html('html.amount', $unutilizedTax, $response->currency);
		$response->payableAmount = $themes->html('html.amount', $payableAmount, $response->currency);
		$response->discounts = $themes->html('html.amount', $discounts, $response->currency);
		$response->taxes = $themes->html('html.amount', $taxes, $response->currency);
		$response->isActivateProration = $isActivateProration;

		return $this->ajax->resolve($response);
	}

	/**
	 * Confirm subscription cancellation dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function confirmCancellation()
	{
		$key = $this->input->get('order_key', '', 'default');

		$theme = PP::themes();
		$theme->set('key', $key);
		$output = $theme->output('site/order/dialogs/confirm.cancel');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirm subscription upgrade dailog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function confirmUpgrade()
	{
		PP::requireLogin();

		$key = $this->input->get('key', '', 'default');
		$orderId = PP::getIdFromInput('key');

		if (!$orderId) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_INVALID_ID'));
		}

		$order = PP::order($orderId);

		// check if user is the buyer or not
		$buyer = $order->getBuyer();
		if ($buyer->id != $this->my->id && !$this->my->isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_UPGRADE_NOT_ALLOWED'));
		}

		$sub = $order->getSubscription();
		$plans = PPUpgrade::findAvailableUpgrades($sub);

		$currentPlan = $sub->getPlan();

		$theme = PP::themes();
		$theme->set('key', $key);
		$theme->set('upgrade_to', $plans);
		$theme->set('plan', $currentPlan);
		$output = $theme->output('site/order/dialogs/confirm.upgrade');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays upgrade details without creating a new order
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function showUpgradeDetails()
	{
		PP::requireLogin();

		$key = $this->input->get('key', '', 'default');
		$planId = $this->input->get('id', 0, 'int');

		$orderId = PP::getIdFromInput('key');
		$curOrder = PP::order($orderId);

		// check if user is the buyer or not
		$buyer = $curOrder->getBuyer();
		if ($buyer->id != $this->my->id && !$this->my->isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_UPGRADE_NOT_ALLOWED'));
		}

		$curSub = $curOrder->getSubscription();
		$availablePlans = PPUpgrade::findAvailableUpgrades($curSub);

		if (!$planId || !isset($availablePlans[$planId])) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_INVALID_PLAN'));
		}

		$curPlan = $curSub->getPlan();
		$curOrderInvoices = $curOrder->getInvoices(PP_INVOICE_PAID);
		$newPlan = PP::Plan($planId);

		$curInvoice = null;

		if ($curOrderInvoices) {
			$curInvoice = array_pop($curOrderInvoices);
		} else {
			// now invoice found. lets create new one.
			$curInvoice = $curOrder->createInvoice();
		}

		$result = PPUpgrade::calculateUnutilizedValue($curSub, $curPlan, $newPlan);

		$paidAmount = $result['paid'];
		$unutilized = $result['unutilized'];
		$unutilizedTax = $result['unutilizedTax'];
		$isActivateProration = $result['isActivateProration'];

		$planPrice = $newPlan->getPrice();
		$willTrialApply = PPUpgrade::willTrialApply($curPlan, $newPlan);

		if ($willTrialApply == PPUpgrade::APPLY_TRIAL_ALWAYS) {
			$expiration_type = $newPlan->getExpirationType();

			if ($expiration_type == 'recurring_trial_2' || $expiration_type == 'recurring_trial_1'){
				$planPrice 	= $newPlan->getPrice(PP_RECURRING_TRIAL_1);
			}
		}

		// Get price variation for this plan
		$priceVariations = $newPlan->getPlanPriceVariations();
		$priceVariationsHtml = '';
		
		if ($priceVariations) {
			if (count($priceVariations) > 1) {
				$priceVariations = array($priceVariations[0]);
			}

			$separator = $newPlan->isRecurring() !== false ? JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR') : JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR_FOR');

			$theme = PP::themes();
			$theme->set('plan', $newPlan);
			$theme->set('separator', $separator);
			$theme->set('priceVariations', $priceVariations);

			$priceVariationsHtml = $theme->output('site/upgrades/pricevariation');
		}

		$response = new stdClass();

		$payableAmount = ($planPrice - $unutilized - $unutilizedTax);

		// Once we calcuate the tax, we need to update the payble amount
		$taxes = PPUpgrade::calculateTaxes($curInvoice, $planPrice);
		$payableAmount += $taxes;

		// Once we calculate the discount, we need to update the paybale amount
		$discounts = PPUpgrade::calculateDiscounts($newPlan, $payableAmount, $curInvoice);
		$payableAmount -= $discounts;

		$themes = PP::themes();

		$payableAmount = ($payableAmount < 0) ? 0 : $payableAmount;
		$response->amount = $newPlan->getPrice();
		$response->currency = $newPlan->getCurrency();

		$response->price = $themes->html('html.amount', $newPlan->getPrice(), $response->currency);
		$response->unutilized = $themes->html('html.amount', $unutilized, $response->currency);
		$response->unutilizedTax = $themes->html('html.amount', $unutilizedTax, $response->currency);
		$response->payableAmount = $themes->html('html.amount', $payableAmount, $response->currency);
		$response->discounts = $themes->html('html.amount', $discounts, $response->currency);
		$response->taxes = $themes->html('html.amount', $taxes, $response->currency);
		$response->planpricevariation = $priceVariationsHtml;
		$response->isActivateProration = $isActivateProration;

		return $this->ajax->resolve($response);
	}

	/**
	 * Confirm subscription Deleteion dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function confirmDeleteion()
	{
		$key = $this->input->get('order_key', '', 'default');

		$theme = PP::themes();
		$theme->set('key', $key);
		$output = $theme->output('site/order/dialogs/confirm.delete');

		return $this->ajax->resolve($output);
	}

}
