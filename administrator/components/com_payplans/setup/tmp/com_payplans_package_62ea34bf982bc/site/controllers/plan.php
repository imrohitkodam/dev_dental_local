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

class PayPlansControllerPlan extends PayPlansController
{
	/**
	 * Processes after a user clicks on a subscribe link or button
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function subscribe()
	{
		$planId = $this->input->get('plan_id', 0, 'int');
		// Ensure that the plan is a valid plan
		$plan = PP::plan($planId);

		if (!$planId || !$plan->getId()) {
			throw new Exception('COM_PAYPLANS_PLAN_PLEASE_SELECT_A_VALID_PLAN');
		}

		// Determine if user can really subscribe to the plan
		if (!$plan->canSubscribe()) {
			$this->info->set($plan->getError()->text, 'error');
			return $this->redirectToView('plan', '', 'plan_id=0');
		}

		// Trigger event after a plan has been selected
		$args = [&$planId, $this];
		PP::event()->trigger('onPayplansPlanAfterSelection', $args, '', $plan);

		// If the user is not logged in, we'll link to a dummy user
		$userId = $this->my->id;

		// Reset the user's new id from session at this point when they are placing a new order
		$session = PP::session();
		$session->set('REGISTRATION_NEW_USER_ID', 0);

		if (!$userId && $this->config->get('registrationType') === 'auto') {
			$userId = PP::getDummyUserId();

			// Reset to the dummy id
			$session->set('REGISTRATION_NEW_USER_ID', $userId);
		}

		// Check if there is any price variation to this plan
		$priceVariation = $this->input->get('pricevariation', false, 'default');

		if ($priceVariation && $priceVariation != 'default') {
			$planPriceVariation = PP::planPriceVariation($priceVariation);
			$priceVariationData = $planPriceVariation->parse($priceVariation);

			if (!$planPriceVariation->hasPrice($priceVariationData->price)) {
				die('Invalid price provided');
			}

			$plan->setPlanPriceVariation($priceVariation);
		}

		// Check if there is any advanced pricing assigned to this plan
		$advPricing = $this->input->get('advpricing', false, 'cmd');

		if ($advPricing) {
			$plan->setAdvPricing($advPricing);
		}

		// Subscribe to the plan
		$order = $plan->subscribe($userId);
		$invoice = $order->createInvoice();

		// check for discount coupon code in plan purchase url
		$discountCode = $this->input->get('coupon_code', '', 'default');
		$discountCode = trim($discountCode);

		if ($discountCode) {
			$this->applyDiscountCoupon($invoice, $discountCode);
		}

		$invoiceKey = $invoice->getKey();

		// Construct url variable
		$var = 'invoice_key=' . $invoiceKey . PP::getExcludeTplQuery('checkout');

		// Directly go to thanks page for free invoice
		if ($this->config->get('skip_free_invoices') && $invoice->isFree()) {

			if ($this->my->id) {
				$redirect = PPR::_('index.php?option=com_payplans&task=checkout.confirm&invoice_key=' . $invoiceKey . '&app_id=0', false);
				return $this->app->redirect($redirect);
			} else {
				$var .= '&skipInvoice=1';
			}
		}

		// Get Cancel and Return url if user subscribe through plan module
		$returnUrl = $this->input->get('return_url', '', 'default');
		if ($returnUrl) {
			$var .= '&returnUrl='.$returnUrl;
		}

		// Redirect to confirm action
		return $this->redirectToView('checkout', '', $var);
	}


	/**
	 * Apply discount code if pass in query string
	 *
	 * @since	4.2.3
	 * @access	public
	 */
	public function applyDiscountCoupon($invoice, $discountCode = '')
	{
		// Ensure that discounts is really enabled
		if (!$this->config->get('enableDiscount')) {
			return false;
		}

		// Ensure that the discount code is applicable for the plan
		$table = PP::table('Discount');
		$exists = $table->load([
			'coupon_code' => $discountCode, 
			'published' => 1
		]);

		if (!$invoice->getId() || !$exists || !$table->prodiscount_id) {
			return false; // Invalid coupon code
		}

		// Check if the coupon code can really be applied on the selected plan
		$discount = PP::discount($table);

		$allowed = $discount->isInvoiceApplicable($invoice);
		if (!$allowed) {
			return false;
		}

		// Create temporary standard object to apply as modifier
		$modifier = $discount->toModifier($invoice);

		if ($modifier) {
			// Check for maximum allowed discount
			$allowed = $discount->checkForUpperLimit($modifier, $invoice);
			if (!$allowed) {
				return false;
			}
		}

		$args = [&$invoice, $discountCode];
		$results = PP::event()->trigger('onPayplansDiscountRequest', $args, '', $invoice);

		// if something return, means there are errors
		if ($results && isset($results[0]) && $results[0]) {
			return false;
		}

		// Apply the modifier on the invoice
		// Exchange the standard object with a proper PPModifier object
		$modifier = $invoice->addModifier($modifier);

		// For non recurring discounts
		if (!$discount->isForRecurring() && $invoice->getRecurringType() == PP_PRICE_RECURRING) {
			$invoiceParams = $invoice->getParams();

			$recurrenceCount = (int) $invoiceParams->get('recurrence_count');

			$invoiceParams->set('expirationtype', 'recurring_trial_1');
			$invoiceParams->set('recurrence_count', $recurrenceCount > 0 ? $recurrenceCount - 1 : 0);
			$invoiceParams->set('trial_price_1', $invoiceParams->get('price'));
			$invoiceParams->set('trial_time_1', $invoiceParams->get('expiration'));
			$invoiceParams->set('recurring_onetime_discount', true);

			$invoice->params = $invoiceParams;
		}

		$invoice->refresh();
		$invoice->save();

		// Trigger event to allow apps to manipulate data
		$args = [&$invoice, $discount->getCouponCode()];
		PP::event()->trigger('onPayplansDiscountAfterApply', $args, '', $invoice);

		return true;
	}
}
