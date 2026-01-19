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

class PayPlansViewReferrals extends PayPlansSiteView
{
	/**
	 * Checks if a given coupon code is valid
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function apply()
	{
		$id = $this->getKey('invoice_key');
		$code = $this->input->get('code', '', 'default');
		
		$invoice = PP::invoice($id);

		$my = JFactory::getUser();

		if (!$my->guest && !$invoice->validateBuyer()) {
			return $this->ajax->reject(JText::_('COM_PP_NOT_ALLOWED_HERE'));
		}

		$plan = $invoice->getPlan();

		$model = PP::model('Referrals');
		$hasApp = $model->hasApplicableApp($plan);

		if (!$hasApp) {
			return $this->ajax->reject(JText::_('COM_PP_REFERRAL_CODE_CANNOT_USE'));
		}

		$referral = PP::referral();
		$allowed = $referral->allowed($invoice, $code);

		if (!$allowed) {
			return $this->ajax->reject($referral->getError());
		}

		// Apply discount to referral (purchaser)
		$state = $referral->applyPurchaserDiscount($invoice, $code);

		if (!$state) {
			return $this->ajax->reject($referral->getError());
		}

		// Apply discount to referrer (code sharer)
		$state = $referral->applySharerDiscount($invoice, $code);

		if (!$state) {
			return $this->ajax->reject($referral->getError());
		}

		PP::info()->set('COM_PP_REFERRER_CODE_APPLIED_SUCCESSFULLY');

		return $this->ajax->resolve();
	}
}