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

class PPUpgrade extends PPAbstract
{
	const APPLY_TRIAL_ALWAYS = 1;
	const APPLY_TRIAL_NEVER = 0;

	const ACTIVATE_PRORATE = 1;
	const DEACTIVATE_PRORATE = 0;

	/**
	 * Calculates discounts if any for upgrades
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function calculateDiscounts($newPlan, $payableAmount = null, $curInvoice = '', $discountCodePrefix = PP_PRODISCOUNT_UPGRADE_DISCOUNT)
	{
		$options = [
			'coupon_type' => 'autodiscount_onupgrade',
			'published' => 1
		];

		$discounts = PPHelperDiscount::getApplicableDiscountsByPlan($newPlan, $options);

		if (!$discounts) {
			return 0;
		}

		if (is_null($payableAmount)) {
			$payableAmount = $newPlan->getPrice();
		}

		$discountItems = [];
		$isApplyFirstDiscount = false;
		$isSkippedDiscountUsageCondition = false;
		$isFirstDisallowCombineDiscount = false;

		$fixedDiscountAmountItem = [];
		$percentageDiscountAmountItem = [];

		$model = PP::model('Discount');
		$allowClubbing = PP::config()->get('multipleDiscount');

		foreach ($discounts as $discount) {

			if ($curInvoice) {

				// Disallow user to use the same discount code on different subscriptions if discount is not reusable
				$user = $curInvoice->getBuyer();

				$applicable = PPHelperDiscount::checkForApplicableDates($discount);

				if (!$applicable) {
					continue;
				}

				$isCurrentDiscountRuleCombinable = $discount->isCombinable();

				if (!$allowClubbing) {

					// skip this if the upgrade discount already applied
					if ($isApplyFirstDiscount) {
						continue;
					}

					if (!$isCurrentDiscountRuleCombinable) {
						// skip this if already proceed the first disallow combining discount discount rule
						// And skipped the discount rules from discount multiple usage check
						if ($isFirstDisallowCombineDiscount && !$isSkippedDiscountUsageCondition) {
							continue;
						}

						$isFirstDisallowCombineDiscount = true;
					}

				} else {
					// Only allow to apply the first non-combineable discount rule, then skip the rest of the discount rules
					if (!$isCurrentDiscountRuleCombinable && $isApplyFirstDiscount) {
						continue;
					}
				}

				$discountId = $discount->getId();

				// set coupon_code so that unique reference can be identified from modifier for this instance
				$discountCode = $discountCodePrefix . '_' . $discountId;
				$hasUsedDiscountUsage = $model->hasUsed($discountCode, $user->getId(), $discount->getCouponType());

				// Check whether this discount code still available or not
				if (!$user->isPlaceholderAccount() && !$discount->isReusable() && $hasUsedDiscountUsage) {
					$isSkippedDiscountUsageCondition = true;
					continue;
				}

				// If admin configured the use quantity, then we need to check if it is allowed
				$allowedQuantity = $discount->getAllowedQuantity();

				if ($allowedQuantity) {

					// Get total usage
					$usage = $discount->getCounter();

					// Skip this if the discount code reached the usage limits
					if ($usage >= $allowedQuantity) {
						continue;
					}
				}
			}

			$isApplyFirstDiscount = true;

			$isFixed = $discount->isFixed();
			$isPercentage = $discount->isPercentage();

			// If this is fixed amount discount type
			if ($isFixed) {
				$fixedDiscountAmountItem[] = $discount;
			}

			// If this is percentage amount discount type
			if ($isPercentage) {
				$percentageDiscountAmountItem[] = $discount;
			}
		}

		$totalDiscounts = 0;

		// here only calculate the discount
		// Fixed amount discount
		if ($fixedDiscountAmountItem) {
			$totalFixedAmount = 0;

			foreach ($fixedDiscountAmountItem as $item) {
				$totalFixedAmount = $item->getCouponAmount();

				$totalDiscounts += $totalFixedAmount;
				$payableAmount = $payableAmount - $totalFixedAmount;
			}

		}

		// Pecentage amount discount
		if ($percentageDiscountAmountItem) {
			$totalPercentageAmount = 0;

			foreach ($percentageDiscountAmountItem as $item) {
				$totalPercentageAmount = ($payableAmount * $item->getCouponAmount()) / 100;

				$totalDiscounts += $totalPercentageAmount;
				$payableAmount = $payableAmount - $totalPercentageAmount;
			}
		}

		return $totalDiscounts;
	}

	/**
	 * Calculates Tax if any for upgrades
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function calculateTaxes($newInvoice, $planPrice)
	{
		$taxAmount = 0;

		// Get all the basic tax apps
		$basicTaxApp = PPHelperApp::getAvailableApps('basictax');
		$euVatApp = PPHelperApp::getAvailableApps('euvat');


		if ($basicTaxApp || $euVatApp) {

			// Get user country and preferences data
			$user = $newInvoice->getBuyer();
			$country = $user->getCountry();

			$userPref = $user->getPreferences();

			$businessVatno = $userPref->get('tin');
			$businessName = $userPref->get('business_name');
			$purpose = $userPref->get('business_purpose', 1);	


			$args  = [$newInvoice, $country, $purpose, $businessVatno];
			$results = PPEvent::trigger('onPayplansApplyTax', $args, '', $newInvoice);

			$taxes = [];
			foreach ($results as $result => $result_val) {
				if (!is_null($result_val)) {
					$taxes[]= $result_val;
				}
			}

			// Calculate Tax
			if ($taxes) {
				$taxAmount = 0;
				foreach ($taxes as $tax) {
					$amount = ($planPrice * $tax->rate) / 100;
					$taxAmount += floatval($amount);
				}
			}
		}

		return $taxAmount;
	}
	
	/**
	 * Calculates unutilized values from the old subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function calculateUnutilizedValue(PPSubscription $subscription, PPPlan $curPlan, PPPlan $newPlan)
	{
		$default = ['paid' => 0, 'unutilized'=> 0];

		// If subscription is no more active, value as 0
		if (!$subscription->isActive()) {
			return $default;
		}

		// For the subs start, we need to get the last master invoices.
		$order = $subscription->getOrder();
		
		// Get Latest Invoice
		$invoices = $order->getInvoices(array(PP_INVOICE_PAID));
		$lastInvoice = array_pop($invoices);
		
		// $subStart = $subscription->getSubscriptionDate(null, false);
		$subStart = PP::date($lastInvoice->getPaidDate(false));
		$expDate = $subscription->getExpirationDate();

		// Find value utilized by old subscription
		$start = ($subStart !== false) ? intval($subStart->toUnix()) : 0;
		$expires = ($expDate !== false) ? intval($expDate->toUnix()) : 0;
		$now = intval(PP::date()->toUnix());

		$totalTime = $expires - $start;

		$totalValue = self::calculatePaymentsDuringPreviousUpgradations($order);

		$usedTax = 0;
		$usedValue = 0;
		$unutilizedValue = 0;
		$unutilizedTax = 0;

		$result = [
			'paid' => $totalValue['planPrice'],
			'unutilized' => $unutilizedValue,
			'unutilizedTax' => $unutilizedTax
		];

		// Only calculate this if upgrade proration is activated
		$isActivateProration = self::isActivateProration($curPlan, $newPlan);

		$result['isActivateProration'] = $isActivateProration;

		if ($isActivateProration) {

			// Pro rate values if it is a paid plan previously
			if ($totalValue['planPrice'] != 0 && $expires != 0) {
				$used = $now - $start;

				// if total time is not in hours, then calculate as per days
				$oneday = 24 * 60 * 60;

				if ($totalTime > (3 * $oneday)) {
					$used = intval($used / $oneday);
					$totalTime = intval($totalTime/$oneday);
				}

				$usedValue = $totalValue['planPrice'] * $used / $totalTime;
				$usedTax = $totalValue['taxIncluded'] * $used / $totalTime;
			}

			// the value which is not utilized, and will be added into discount
			$unutilizedValue = $totalValue['planPrice'] - $usedValue;
			$unutilizedTax = $totalValue['taxIncluded'] - $usedTax;

			$result['unutilized'] = $unutilizedValue;
			$result['unutilizedTax'] = $unutilizedTax;
		}

		return $result;
	}

	/**
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected static function calculatePaymentsDuringPreviousUpgradations($order)
	{
		// get payments
		$invoices = $order->getInvoices(PP_INVOICE_PAID);

		if (count($invoices) == 0) {
			// none of payment were completed
			$totalValue = 0;
		} else {
			// pick last paid invoice
			$invoice = array_pop($invoices);
			$modifiers = $invoice->getModifiers();
			$modifiers = PPHelperModifier::rearrange($modifiers);

			$discountableAddon = [];
			$addonTaxableTotal = [];
			$taxValue = [];
			$adjustment = 0;
			$totalAmountofPlan = 0;
			$taxAmount = 0;

			if ($modifiers) {
				// if there are something to process, lets do it.
				foreach ($modifiers as $modifier) {
					if (in_array($modifier->getSerial(), array(PP_MODIFIER_PERCENT_OF_SUBTOTAL_DISCOUNTABLE,PP_MODIFIER_PERCENT_OF_SUBTOTAL_TAXABLE))) {
						$addonTaxableTotal[] = str_replace('-', '', PPFormats::displayAmount($modifier->_modificationOf));
					}

					if (in_array($modifier->getSerial(), array(PP_MODIFIER_PERCENT_OF_SUBTOTAL_DISCOUNTABLE))) {
						$discountableAddon[] = str_replace('-', '', PPFormats::displayAmount($modifier->_modificationOf));
					}

					if (in_array($modifier->getSerial(), array(PP_MODIFIER_FIXED_TAX,PP_MODIFIER_PERCENT_TAX))) {
						$taxValue[] = $modifier->_modificationOf;
					}

					if (in_array($modifier->getSerial(),array(PP_MODIFIER_FIXED_NON_TAXABLE))) {
						$adjustment = PPFormats::displayAmount($modifier->_modificationOf);
					}
				}
			}

			$plandiscountableamount = $invoice->getSubtotal() + $adjustment;

			if ($plandiscountableamount > 0 && array_sum($discountableAddon) > 0) {
				$discountApplicableonAddon = (array_sum($discountableAddon) * $invoice->getDiscount()) / ($plandiscountableamount + array_sum($discountableAddon));
			} else {
				$discountApplicableonAddon = 0;
			}

			$finalAddonTotal= array_sum($addonTaxableTotal) - $discountApplicableonAddon;

			if ($plandiscountableamount > 0 && array_sum($discountableAddon) > 0) {
				$discountAmountofplan = ($plandiscountableamount * $invoice->getDiscount()) / ($plandiscountableamount + array_sum($discountableAddon));
			} else {
				$discountAmountofplan = 0;
			}

			$totalAmountofPlan = $plandiscountableamount - $discountAmountofplan;


			if ($totalAmountofPlan > 0 && array_sum($taxValue) > 0) {
				$taxAmount = ($totalAmountofPlan * array_sum($taxValue)) / ($totalAmountofPlan + $finalAddonTotal);
			} else {
				$taxAmount = 0;
			}
		}

		return ['planPrice' => $totalAmountofPlan, 'taxIncluded' => $taxAmount];
	}


	/**
	 * Retrieves a list of plans available for the current subscription plan
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function findAvailableUpgrades(PPSubscription $subscription)
	{
		$plans = [];

		// Subscription must be active in order to be able to upgrade
		if (!$subscription->isActive()) {
			return $plans;
		}

		$plan = $subscription->getPlan();

		// only published and visible plans
		$plans = PPHelperPlan::getPlans(['published' => 1]);

		$upgradePlans = [];

		$upgrades = self::loadUpgrades();

		if ($upgrades) {
			foreach ($upgrades as $upgrade) {

				// we need to check if this upgrade can be used by this subscription or not
				if (!$upgrade->getApplyAll()) {
					$appPlans = $upgrade->getPlans();

					// this upgrade is not meant for this subscription
					if (! in_array($plan->getId(), $appPlans)) {
						continue;
					}
				}

				$param = $upgrade->app_params;
				$tmpPlans = $param->get('upgrade_to');

				if ($tmpPlans) {
					foreach ($tmpPlans as $pid) {
						// make sure the plan is valid
						if (isset($plans[$pid])) {
							// at the same time, we distinct the plans
							$upgradePlans[$pid] = $plans[$pid];
						}
					}
				}
			}
		}

		return $upgradePlans;
	}


	/**
	 * Get all upgrades
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function loadUpgrades()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$model = PP::model('App');
			$options = ['type' => 'upgrade', 'published' => 1];
			$results = $model->loadRecords($options);

			if ($results) {
				$_cache = array();
				foreach ($results as $item) {
					$upgrade = PP::app($item);
					$_cache[] = $upgrade;
				}
			}
		}

		return $_cache;
	}

	/**
	 * Creates a new order from the old subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function createUpgradeOrder(PPSubscription $subscription, PPPlan $newPlan)
	{
		// Get the previous order
		$oldOrder = $subscription->getOrder();
		$newOrder = $newPlan->subscribe($subscription->getBuyer()->id);

		// @TODO: Currently we do not support trial prices in new plan, We will do it in future

		// Update the params so we can refer to the newly upgraded order
		$newOrder->setParam('upgrading_from', $subscription->getId())->save();
		$oldOrder->setParam('upgraded_to', $newOrder->getSubscription()->getId())->save();

		return $newOrder;
	}

	/**
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function willTrialApply($oldPlan, $newPlan)
	{
		$upgradeApps = self::loadUpgrades();

		if ($upgradeApps) {
			foreach ($upgradeApps as $app) {

				$upgradeTo = $app->app_params->get('upgrade_to',array());
				$upgradeTo = is_array($upgradeTo) ? $upgradeTo : array($upgradeTo);

				$willTrialApply = $app->app_params->get('willTrialApply',false);
				if ($app->getApplyAll() && in_array($newPlan->getId(), $upgradeTo)) {
					if ($willTrialApply) {
						return self::APPLY_TRIAL_ALWAYS;
					}
				} else if (in_array($oldPlan->getId(), $app->getPlans()) && in_array($newPlan->getId(),$upgradeTo)) {
					if ($willTrialApply) {
						return self::APPLY_TRIAL_ALWAYS;
					}
				}
			}
		}

		return self::APPLY_TRIAL_NEVER;
	}

	/**
	 * Determine which plan activate/deactivate for the upgrade prorate
	 * 
	 * @since	4.2.15
	 * @access	public
	 */
	public static function isActivateProration($oldPlan, $newPlan)
	{
		$upgradeApps = self::loadUpgrades();

		if ($upgradeApps) {

			foreach ($upgradeApps as $app) {

				$upgradeTo = $app->app_params->get('upgrade_to', []);
				$upgradeTo = is_array($upgradeTo) ? $upgradeTo : [$upgradeTo];

				$isActivateProration = $app->app_params->get('upgradeProRate', true);
				$oldPlanId = $oldPlan->getId();
				$newPlanId = $newPlan->getId();

				if ($app->getApplyAll() && in_array($newPlanId, $upgradeTo)) {

					if ($isActivateProration) {
						return self::ACTIVATE_PRORATE;
					}

				} else if (in_array($oldPlanId, $app->getPlans()) && in_array($newPlanId, $upgradeTo)) {
					
					if ($isActivateProration) {
						return self::ACTIVATE_PRORATE;
					}
				}
			}
		}

		return self::DEACTIVATE_PRORATE;
	}

	/**
	 * Sends out an e-mail when a subscription is being updated
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function sendCancelUpgradeEmail($oldOrder, $oldInvoice, $oldSub, $oldPayment)
	{
		$params = [
			'order_key' => $oldOrder->getKey(),
			'invoice_key' => $oldInvoice->getKey(),
			'subscription_key' => $oldSub->getKey(),
			'payment_key' => $oldPayment->getKey()
		];

		$recipient = $oldOrder->getBuyer()->getEmail();

		$mailer = PP::mailer();
		$mailer->send($recipient, 'COM_PAYPLANS_UPGRADES_ORDER_CANCEL_SUBJECT', 'plugins:/payplans/upgrade/emails/cancel', $params);
	}

	//update new invoice as per trial if applicable
	public static function updateInvoiceParams($newInvoice, $willTrialApply)
	{
		$isRecurring = $newInvoice->getRecurringType();

		if ($willTrialApply === self::APPLY_TRIAL_NEVER && ($isRecurring === PP_PRICE_RECURRING_TRIAL_1 || $isRecurring === PP_PRICE_RECURRING_TRIAL_2)) {
			$oldParams = $newInvoice->getParams()->toArray();

			$newParams['expirationtype'] = 'recurring';
			$newParams['trial_price_1'] = '0.00';
			$newParams['trial_time_1'] = '000000000000';
			$newParams['trial_price_2'] = '0.00';
			$newParams['trial_time_2'] = '000000000000';

			$params = array_merge($oldParams, $newParams);

			$invoiceParams = new JRegistry($params);
			$newInvoice->params = $invoiceParams->toString();

			$newInvoice->subtotal = $oldParams['price'];
			$newInvoice->refresh()->save();
		}

		// change new invoice to trial so that to apply discounted price only once
		if ($isRecurring === PP_PRICE_RECURRING) {
			$oldParams = $newInvoice->getParams()->toArray();

			$recurrenceCount = 0;
			if ($oldParams['recurrence_count']) {
				$recurrenceCount = $oldParams['recurrence_count'] -1;
			}

			$newParams['expirationtype'] = 'recurring_trial_1';
			$newParams['recurrence_count'] = $recurrenceCount;
			$newParams['trial_price_1'] = $oldParams['price'];
			$newParams['trial_time_1'] = $oldParams['expiration'];

			$params = array_merge($oldParams, $newParams);

			$invoiceParams = new JRegistry($params);
			$newInvoice->params = $invoiceParams->toString();

			//subtotal does not modified by params value automatically
			$newInvoice->subtotal = $oldParams['price'];
			$newInvoice->refresh()->save();
		}

		return $newInvoice;
	}

	/**
	 * Upgrades a subscription to a new plan
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function upgradeSubscription(PPSubscription $subscription, PPPlan $newPlan, $upgradeType, $priceVariation = '')
	{
		// Determines if the subscription can be upgraded to the new plan
		if (!$subscription->canUpgrade($newPlan->getId())) {
			return false;
		}

		// Get the previous plan
		$old = new stdClass();
		$old->plan = $subscription->getPlan();
		$old->order = $subscription->getOrder();
		$old->invoices = $old->order->getInvoices(PP_INVOICE_PAID);

		$oldInvoice = null;
		if ($old->invoices) {
			$oldInvoice = array_pop($old->invoices);
		} else {
			$oldInvoice = $old->order->createInvoice();
		}

		// Check if there is any price variation to this plan
		if ($priceVariation && $priceVariation != 'default') {
			$newPlan->setPlanPriceVariation($priceVariation);
		}

		$result = self::calculateUnutilizedValue($subscription, $old->plan, $newPlan);
		$paidAmount = $result['paid'];
		$unutilized = $result['unutilized'];
		$unutilizedTax = $result['unutilizedTax'];
		$isActivateProration = $result['isActivateProration'];

		// Create a new upgrade order for the subscription
		$newOrder = self::createUpgradeOrder($subscription, $newPlan);
		$newInvoice = $newOrder->createInvoice();

		// Check whether trial is applicable or not and then update invoice params accordingly
		$applyTrial = self::willTrialApply($old->plan, $newPlan);
		$newInvoice = self::updateInvoiceParams($newInvoice, $applyTrial);

		if ($isActivateProration) {
			$params = new stdClass();
			$params->type = 'upgrade';
			$params->reference = $oldInvoice->getKey();
			$params->percentage = false;
			$params->amount = -$unutilized;
			// $params->amount = $unutilized;
			$params->serial = PP_MODIFIER_FIXED_NON_TAXABLE;
			$params->message = JText::_('COM_PAYPLANS_UPGRADE_MESSAGE');

			$newInvoice->addModifier($params);
		}

		// Save the new invoice with the modifier
		$newInvoice->save();

		if ($isActivateProration) {
			// Update the tax modifiers
			$params = new stdClass();
			$params->type = 'upgradeTax';
			$params->reference = $oldInvoice->getKey();
			$params->percentage = false;
			$params->amount = -$unutilizedTax;
			$params->serial = PP_MODIFIER_FIXED_NON_TAXABLE_TAX_ADJUSTABLE;
			$params->message = JText::_('COM_PAYPLANS_UPGRADE_TAX_MESSAGE');
			
			$newInvoice->addModifier($params);
		}

		$newInvoice->save();

		// Post process after the invoice is saved
		if ($upgradeType == 'free') {
			self::upgradeFree($newInvoice);
		}

		if ($upgradeType == 'offline') {
			self::upgradeOffline($newInvoice);
		}

		if ($upgradeType == 'user') {
			self::upgradePartial($newInvoice);
		}

		return $newInvoice;
	}

	/**
	 * Process free upgrades
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function upgradeFree($invoice)
	{
		$newOrder = $invoice->getReferenceObject();

		if (is_a($newOrder, 'PPOrder')) {
			$reference = $newOrder->getParam('upgrading_from');
		}

		//set the modifier reference to the old subscription key
		$reference = isset($reference)? $reference : 'order_upgrade';

		$modifierParams = new stdClass();
		$modifierParams->type ='free_upgrade';
		$modifierParams->percentage	= true;
		$modifierParams->serial = PP_MODIFIER_PERCENT_DISCOUNTABLE;
		$modifierParams->amount = -100;
		$modifierParams->message = 'COM_PAYPLANS_FREE_UPGRADE_MESSAGE';
		$modifierParams->reference = $reference;

		$invoice->addModifier($modifierParams);
		$invoice->save();

		// Transaction added for free upgrade
		$transaction = PP::transaction();
		$transaction->user_id = $invoice->getBuyer()->id;
		$transaction->invoice_id = $invoice->getId();
		$transaction->amount = 0;
		$transaction->payment_id = 0;
		$transaction->message = 'COM_PAYPLANS_TRANSACTION_CREATED_FOR_FREE_UPGRADE';

		$transaction->save();

		return true;
	}

	/**
	 * Process offline upgrades. We need to create a new transaction to offset the amount.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function upgradeOffline($invoice)
	{
		$params = new JRegistry();
		$params->set('transaction_amount', $invoice->getTotal());
		$params->set('transaction_message', JText::_('COM_PAYPLANS_TRANSACTION_CREATED_FOR_OFFLINE_UPGRADE'));

		$transaction = $invoice->addTransaction($params);
		return true;
	}

	/**
	 * Process partial upgrades
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function upgradePartial($invoice)
	{
		// Send user an e-mail confirmation so that they can pay for the new invoice

		$subject = JText::_('COM_PAYPLANS_INVOICE_EMAIL_LINK_SUBJECT');
		$namespace = 'emails/upgrade/order.payment';

		$text = JText::_('COM_PAYPLANS_INVOICE_EMAIL_LINK_BODY');
		$content = PP::rewriteContent($text, $invoice);

		// Send notification to the buyer
		$user = $invoice->getBuyer();
		$data = [
			'content' => $content
		];

		$mailer = PP::mailer();
		$state = $mailer->send($user->getEmail(), $subject, $namespace, $data);

		if (!$state) {
			PPLog::log(PPLogger::LEVEL_INFO, JText::_('COM_PAYPLANS_EMAIL_SENDING_FAILED'), $invoice, $content);
			return false;
		}

		PPLog::log(PPLogger::LEVEL_INFO, JText::_('COM_PAYPLANS_EMAIL_SEND_SUCCESSFULLY'), $invoice, $content);
		return true;
	}
}
