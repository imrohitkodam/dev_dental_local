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

require_once(__DIR__ . '/formatter.php');

class PPAppEmail extends PPApp
{
	/**
	 * Determines if this app should get executed when triggered
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isApplicable($refObject = null, $eventName = '')
	{
		// if not with reference to payment then return
		if ($eventName === 'onPayplansCron' || $eventName === 'getTemplatedata') {
			return true;
		}
		
		return parent::isApplicable($refObject, $eventName);
	}

	/**
	 * Triggered when an order is saved
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansOrderAfterSave($prev, $new)
	{
		$this->triggerOnStatus($prev, $new);
	}

	/**
	 * Triggered after an invoice is saved
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansInvoiceAfterSave($prev, $new)
	{
		$this->triggerOnStatus($prev, $new);
	}

	/**
	 * Triggered after a subscription is saved
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		$this->triggerOnStatus($prev, $new);
	}

	/**
	 * Common event for all rules that uses "on_status" for "when_to_send_email"
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function triggerOnStatus($prev, $new)
	{	
		if (!$this->helper->shouldSendEmail($prev, $new)) {
			return;
		}

		return $this->helper->send($new);
	}

	/**
	 * Triggered by cron event
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansCron()
	{
		$plans = $this->helper->getApplicablePlans();
		$applyAll = $this->helper->isApplicableToAllPlans();
		
		if (!$applyAll && !$plans) {
			return false;
		}

		$subscriptions = [];

		// Determine when should we send these e-mails
		$whenToEmail = $this->helper->getWhenToSend();
		$expiry = $this->getAppParam($whenToEmail);

		// If this app is configured to only send e-mails when their status changes, it should not be processed during cronjobs
		if ($whenToEmail === 'on_status') {
			return;
		}

		// On cart abandoned, we have nothing else to process
		if ($whenToEmail === 'on_cart_abondonment') {
			$this->cartAbandoned();
			return;
		}
		
		if ($whenToEmail === 'on_preexpiry') {
			$this->preExpiry($expiry);
			return;
		}

		if ($whenToEmail === 'on_preexpiry_trial') {
			$this->trialPreExpiry($expiry);
			return;
		}

		$event = false;

		if ($whenToEmail === 'on_postactivation') {
			$event = 'postActivation';
			$subscriptions = $this->postActivation($expiry);
		}

		if ($whenToEmail === 'on_postexpiry') {
			$event = 'postExpiry';
			$subscriptions = $this->postExpiry($expiry);
		}
		
		if (!$subscriptions) {
			return;
		}

		// latest cron execution time
		$cronExecutionTime = (int) PP::config()->get('cronAcessTime');

		$currentDate = PP::date('now');
		$cronExecutionDate = PP::date(PP::config()->get('cronAcessTime'));
		$startDate = $cronExecutionDate->subtractExpiration($expiry);
		$startDate = $startDate->toUnix();

		// Only for post activation and post expiration
		foreach ($subscriptions as $id => $row) {

			$subscription = PP::subscription($row);

			$params = $subscription->getParams();
			$sent = $params->get($event . $expiry, false);

			// Check for Activation time
			if ($event === 'postActivation') {

				// Get subscription date and post activation time
				$subscriptionDate = $subscription->getSubscriptionDate();
				$postActivationDate = $subscriptionDate->addExpiration($expiry);
				$postActivationDate = $postActivationDate->toUnix();

				// check for postactivation time with the start date of cron job
				if ($postActivationDate < $startDate) {
					continue;
				}

				// check for postactivation time with cron time to decide whether send email or not
				if ($cronExecutionTime && $cronExecutionTime < $postActivationDate) {
					continue;
				}
			}

			if ($event === 'postExpiry') {

				// Get subscription expiration date and post expiration time
				$expirationDate = $subscription->getExpirationDate();
				$postExpirationDate = $expirationDate->addExpiration($expiry);
				$postExpirationDate = $postExpirationDate->toUnix();

				// check for postexpiration time with the start date of cron job
				if ($postExpirationDate < $startDate) {
					continue;
				}

				// check for postexpiration time with cron time to decide whether send email or not
				if ($cronExecutionTime && $cronExecutionTime < $postExpirationDate) {
					continue;
				}	
			}
			
			if (!$sent) {
				$this->helper->send($subscription);

				// Mark emails as sent for the subscription
				$this->helper->markSent($subscription, $event, $expiry);
			}
		}
	}

	/**
	 * Notifies user when their subscription is going to be expired
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function preExpiry($expiry)
	{
		$model = PP::Model('Subscription');
		$subscriptions = $model->getPreExpirySubscriptions($this->helper->getApplicablePlans(), $expiry, $this->helper->isApplicableToAllPlans());

		//check for each subscription: if it is of recurring type and 
		//email is allowed to send only for last cycle of recurring then
		//unset the subscription which is not the last subscription of recurring cycle
		foreach ($subscriptions as $id => $row) {
			$subscription = PP::subscription($row);

			$order = $subscription->getOrder();
			$count = $subscription->getRecurrenceCount();

			if ($subscription->getExpirationType() == PP_RECURRING_TRIAL_1) {
				$count += 1;
			}

			if ($subscription->getExpirationType() == PP_RECURRING_TRIAL_2) {
				$count += 2;
			}

			$paidInvoices = $order->getInvoices(PP_INVOICE_PAID);
			$refundedInvoices = $order->getInvoices(PP_INVOICE_REFUNDED);

			$invoiceCount = count($paidInvoices) + count($refundedInvoices);

			// If it is a recurring type and we should send on tsshe last cycle, send the e-mails
			if ($subscription->isRecurring() && $this->helper->shouldSendEmailForRecurringLastCycle()) {
				if ($invoiceCount != $count) {
					continue;
				}
			}

			// Ensure that we did not send it before
			$params = $subscription->getParams();
			$sent = $params->get('preExpiry' . $expiry . $invoiceCount, false);

			if (!$sent) {
				$this->helper->send($subscription);

				// Mark emails as sent for the subscription
				$this->helper->markSent($subscription, 'preExpiry', $expiry, $invoiceCount);
			}
		}
	}

	/**
	 * Notifies user when their trial is going to be expired
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function trialPreExpiry($expiry)
	{
		$model = PP::Model('Subscription');
		$subscriptions = $model->getTrialPreExpirySubscriptions($this->helper->getApplicablePlans(), $expiry, $this->helper->isApplicableToAllPlans());

		foreach ($subscriptions as $id => $row) {

			// to determine if we need to send out trial pre expiration email or not,
			// we will use below logic:

			// For trial_1:
			// W will check againts how many invoice been issued.
			// If there is only one invoice, we will need to send the notification.
			// If more than one invoices, this mean we already sent and this pre-expiration is meant for
			// normal recurring after the trial. lets skip.

			// For trial_2:
			// W will check againts how many invoice been issued.
			// If the invoice issued is less than 2, we will need to send the notification.
			// If more than 2 invoices, this mean we already sent both trial 1 and trial 2 and this pre-expiration is meant for
			// normal recurring after the 2 trials. lets skip.

			$doSend = false;

			$subscription = PP::subscription($row);
			$order = $subscription->getOrder();

			$invoicePaid = $order->getInvoices(PP_INVOICE_PAID);
			$invoiceCount = count($invoicePaid);

			if ($subscription->getExpirationType() == PP_RECURRING_TRIAL_1 && $invoiceCount <= 1) {
				$doSend = true;
			}

			if ($subscription->getExpirationType() == PP_RECURRING_TRIAL_2 && $invoiceCount <= 2) {
				$doSend = true;
			}

			if ($doSend) {
				// Ensure that we do not sent duplicate
				$params = $subscription->getParams();
				$sent = $params->get('trialPreExpiry' . $expiry . $invoiceCount, false);

				if (!$sent) {
					$this->helper->send($subscription);

					// Mark emails as sent for the subscription
					$this->helper->markSent($subscription, 'trialPreExpiry', $expiry, $invoiceCount);
				}
			}
		}
	}

	/**
	 * Sends e-mails to users who abandoned their cart
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function cartAbandoned()
	{
		$expiry = $this->getAppParam('on_cart_abondonment');
		$invoices = $this->helper->getAbandonedInvoices($expiry);

		if (!$invoices) {
			return false;
		}

		$event = 'oncartabondonment';

		foreach ($invoices as $id => $row) {
			$invoice = PP::invoice($row);

			 $subscription = $invoice->getSubscription();

			 // If subscription already active then do nothing
			 if ($subscription->getStatus() == PP_SUBSCRIPTION_ACTIVE) {
				return true;
			 }

			// Check if mail has already been sent
			$params = $invoice->getParams();
			$sent = $params->get('oncartabondonment' . $expiry, false);

			if (!$sent) {
				$this->helper->send($invoice);

				// Mark emails as sent for the subscription
				$this->helper->markSent($subscription, 'oncartabondonment', $expiry);
			}
		}

		return true;
	}

	/**
	 * Send e-mails for post activation 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function postActivation($expiry)
	{
		$model = PP::model('Subscription');

		$subscriptions = $model->getPostActivationSubscriptions($this->helper->getApplicablePlans(), $expiry, $this->helper->isApplicableToAllPlans());

		return $subscriptions;
	}

	/**
	 * Notifies user when their subscription has expired
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function postExpiry($expiry)
	{
		$model = PP::model('Subscription');
		$subscriptions = $model->getPostExpirySubscriptions($this->helper->getApplicablePlans(), $expiry, $this->helper->isApplicableToAllPlans());

		return $subscriptions;
	}
}