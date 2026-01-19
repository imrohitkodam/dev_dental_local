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

class PayPlansViewCheckout extends PayPlansSiteView
{
	/**
	 * Renders the check out page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$invoiceKey = $this->input->get('invoice_key', '', 'default');
		$invoice = null;

		$planId = $this->input->get('plan_id', 0, 'int');
		$plan = PP::plan($planId);

		$subscriptionParams = new JRegistry();
		$buyerId = $this->my->id;

		if (!$invoiceKey) {
			return $this->redirectToView('plan', '', 'plan_id=0');
		}

		if ($invoiceKey) {
			$invoiceId = (int) PP::encryptor()->decrypt($invoiceKey);
			$invoice = PP::invoice($invoiceId);

			if (!$invoice->getId()) {
				return $this->redirectToView('plan', '', 'plan_id=0');
			}

			// If invoice already paid then should not show checkout page and redirect to user dashbaord
			if ($invoice->isPaid()) {
				$redirect = PPR::_('index.php?option=com_payplans&view=dashboard', false);
				return PP::redirect($redirect);
			}

			$plan = $invoice->getPlan();
			$planId = $plan->getId();
			$subscription = $invoice->getSubscription();
			$subscriptionParams = $subscription->getParams();

			if (!$buyerId) {
				$buyer = $subscription->getBuyer();
				$buyerId = $buyer->getId();
			}
		}

		if (!$plan->getId()) {
			throw new Exception('COM_PAYPLANS_PLAN_PLEASE_SELECT_A_VALID_PLAN');
		}

		PP::setMeta(PP_META_TYPE_CHECKOUT, $plan->getId());

		// Trigger event after a plan has been selected
		$args = [&$planId, $this];

		PP::event()->trigger('onPayplansPlanAfterSelection', $args, '', $plan);

		// add any default addons if available
		if ($this->config->get('addons_enabled')) {
			$invoice->attachDefaultServices($plan, true);
		}

		// Get referral apps
		$referrals = false;

		if ($this->config->get('discounts_referral')) {
			$referralsModel = PP::model('Referrals');
			$referralApp = $referralsModel->getApplicableApp($plan);
			$referral = PP::referral($referralApp);

			// We should not display the referral form again if a referral discount is already applied
			if (!$referral->isUsed($invoice) && $referralApp) {
				$referrals = true;
			}
		}

		// Get a list of applicable payment methods for the plan
		$providers = $invoice->getPaymentProviders();
		$provider = null;

		if (count($providers) == 1) {
			$provider = $providers[0];
		}

		$user = PP::user($buyerId);

		$addons = [];
		$purchasedAddons = [];

		if ($this->config->get('addons_enabled')) {
			$addonModel = PP::model('addons');
			$addons = $addonModel->getAvailableServices(array($planId));
			$purchasedAddons = $addonModel->getPurchasedServices($invoiceId);
		}

		// Retrieves a list of customdetails
		$customDetailsModel = PP::model('Customdetails');

		$userCustomDetails = $customDetailsModel->getPlanCustomDetails($plan, PP_CUSTOM_DETAILS_TYPE_USER);
		$subsCustomDetails = $customDetailsModel->getPlanCustomDetails($plan, PP_CUSTOM_DETAILS_TYPE_SUBSCRIPTION);

		$payment = $invoice->getPayment();

		if ($payment) {
			$provider = $payment->getApp();
		}

		$modifiers = $invoice->getModifiers();
		$total = PPHelperModifier::getTotal($invoice->getSubtotal(), $modifiers);

		// Get registration provider and set the necessary data
		$registration = PP::registration();

		$registration->setInvoiceKey($invoice->getKey());
		$registration->setSessionKey('PP_CHECKOUT_REGISTRATION');

		$socialDiscount = PP::socialdiscount();

		// If payplans registration plugin enabled then only show register link on checkout page
		// default account type should be login
		$defaultAccountType = 'login';

		$isPluginEnabled = JPluginHelper::isEnabled('payplans', 'registration');
		if ($isPluginEnabled) {
			$defaultAccountType = $this->config->get('default_form_order', 'login');	
		}

		$accountType = $this->input->get('account_type', $defaultAccountType, 'string');
		$skipInvoice = $this->input->get('skipInvoice', 0, 'int');

		if ($skipInvoice && (!$invoice->isFree() || $this->my->id)) {
			$skipInvoice = false;
		}

		$registrationOnly = false;

		// Determine if we should hide unnecessary things initially
		if (!$this->my->id && !$registration->isBuiltIn()) {

			// There might be user coming from new registration. #601
			if ($accountType === 'register' && !$registration->getNewUserId()) {
				$registrationOnly = true;
			} else if ($accountType === 'login' && $registration->getNewUserId()) {
				$accountType = 'register';
			}
		}

		$this->page->title($plan->getTitle());
		$session = PP::session();

		// Determine payment method layout 
		$paymentMethodLayout = $this->config->get('checkout_payment_method_layout', 'dropdown');

		$this->set('subscription', $subscription);
		$this->set('session', $session);
		$this->set('registrationOnly', $registrationOnly);
		$this->set('accountType', $accountType);
		$this->set('socialDiscount', $socialDiscount);
		$this->set('registration', $registration);
		$this->set('referrals', $referrals);
		$this->set('userCustomDetails', $userCustomDetails);
		$this->set('subsCustomDetails', $subsCustomDetails);
		$this->set('subscriptionParams', $subscriptionParams);
		$this->set('user', $user);
		$this->set('step', 'info');
		$this->set('modifiers', $modifiers);
		$this->set('plan', $plan);
		$this->set('provider', $provider);
		$this->set('providers', $providers);
		$this->set('invoice', $invoice);
		$this->set('addons', $addons);
		$this->set('purchasedAddons', $purchasedAddons);
		$this->set('paymentMethodLayout', $paymentMethodLayout);

		// Check if there is integration with EasySocial for Business Details
		$easysocial = PP::easysocial();
		$canEditBusinessDetails = $easysocial->canEditBusinessDetails($user->id);
		
		$purpose = $user->getBusinessPurpose();
		$business = $user->getBusinessData();

		$purposeValue = 'none';

		if ($purpose === PP_EUVAT_PURPOSE_BUSINESS) {
			$purposeValue = 'business';
		}

		if ($purpose === PP_EUVAT_PURPOSE_PERSONAL) {
			$purposeValue = 'personal';
		}

		// If return url set from module then set redirect url else default url is plan page
		$returnUrl = $this->input->get('returnUrl', '', 'default');
		if ($returnUrl) {
			$returnUrl = base64_decode($returnUrl);
		} else {
			$returnUrl = PPR::_('index.php?option=com_payplans&view=plan&from=checkout');
		}

		// Determine it is allowed compnay name and tax identification number on billing details
		$hideCompanyNameAndVat = false;

		// Get Available EU VAt app
		$euVatApps = PPHelperApp::getAvailableApps('euvat');
		if (!$euVatApps && !$this->config->get('show_company_name_and_vat', true)) {
			$hideCompanyNameAndVat = true;
		}

		$this->set('business', $business);
		$this->set('purpose', $purpose);
		$this->set('purposeValue', $purposeValue);
		$this->set('skipInvoice', $skipInvoice);
		$this->set('canEditBusinessDetails', $canEditBusinessDetails);
		$this->set('hideCompanyNameAndVat', $hideCompanyNameAndVat);
		$this->set('returnUrl', $returnUrl);

		return parent::display('site/checkout/default/default');
	}
}
