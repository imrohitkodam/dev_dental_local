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

PP::import('admin:/includes/upgrade/upgrade');

class PayPlansViewOrder extends PayPlansAdminView
{
	/**
	 * Displays upgrade details without creating a new order
	 *
	 * @since	4.0.15
	 * @access	public
	 */
	public function calculatePriceVariation()
	{
		PP::requireLogin();

		$key = $this->input->get('key', '', 'default');
		$planId = $this->input->get('id', 0, 'int');

		$orderId = PP::getIdFromInput('key');
		$curOrder = PP::order($orderId);

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
				die("Invalid price provided");
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

		return $this->resolve($response);
	}
}
