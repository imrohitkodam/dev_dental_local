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

class PayPlansControllerOrder extends PayPlansController
{
	/**
	 * Allows user to cancel their subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancelSubscription()
	{
		PP::requireLogin();

		$orderId = $this->getKey('order_key');

		if (!$orderId) {
			die('Invalid order id');
		}

		$order = PP::order($orderId);
		$subscription = $order->getSubscription();

		// Ensure that the subscription really belongs to the current viewer
		if ($order->getBuyer()->getId() != $this->my->id) {
			die('You do not own this order');
		}

		// Ensure that it really can be cancelled
		if (!$subscription->canCancel()) {
			die('The current subscription does not allow you to cancel');
		}

		$invoice = $order->getInvoice();
		$payment = $invoice->getPayment();

		$output = $order->terminate();

		$message = 'COM_PP_SUBSCRIPTION_CANCELLED_SUCCESSFULLY';
		$state = 'success';

		// @TODO: Determine if the subscription cancellation has failed

		$this->info->set($message, $state);
		$this->redirectToView('dashboard');
	}

	/**
	 * Allow user to upgrade their plan
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processUpgrade()
	{
		PP::requireLogin();

		$orderId = $this->getKey('key');
		$newPlanId = $this->input->get('upgrade_to', 0, 'int');

		// Upgrade failed
		if (!$orderId || !$newPlanId) {
			$message = JText::_('COM_PP_ORDER_INVALID_ID');

			$this->info->set($message, 'error');
			return $this->redirectToView('dashboard');
		}

		$order = PP::order($orderId);

		// check if user is the buyer or not
		$buyer = $order->getBuyer();

		if ($buyer->id != $this->my->id && !$this->my->isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_PP_ORDER_UPGRADE_NOT_ALLOWED'));
		}

		$sub = $order->getSubscription();
		$newPlan = PP::plan($newPlanId);

		// Determine if we have any price varaition being applied to the upgrade
		$priceVariation = $this->input->get('priceVariation', false, 'string');

		// process upgrade
		$newInvoice = PPUpgrade::upgradeSubscription($sub, $newPlan, 'subscription', $priceVariation);

		if ($newInvoice === false) {
			// upgrade failed.
			$message = JText::_('COM_PP_ORDER_UPGRADE_FAILED');

			$this->info->set($message, 'error');
			return $this->redirectToView('dashboard');
		}

		// trigger onPayplansUpgradeBeforeDisplay, e.g discount related apps
		$args = [$newPlanId, $sub, $newInvoice];
		$results = PPEvent::trigger('onPayplansUpgradeBeforeDisplay', $args, '', $sub);

		$invoiceKey = $newInvoice->getKey();

		// Construct url variable
		$var = 'invoice_key=' . $invoiceKey . PP::getExcludeTplQuery('checkout');

		// Directly go to thanks page for free invoice
		if ($this->config->get('skip_free_invoices') && $newInvoice->isFree()) {

			if ($this->my->id) {
				$redirect = PPR::_('index.php?option=com_payplans&task=checkout.confirm&invoice_key=' . $invoiceKey . '&app_id=0', false);
				return $this->app->redirect($redirect);
			} else {
				$var .= '&skipInvoice=1';
			}
		}

		// redirect to checkout page
		return $this->redirectToView('checkout', '', $var);
	}

	/**
	 * Allows user to delete their subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function deleteSubscription()
	{
		PP::requireLogin();

		$orderId = $this->getKey('order_key');

		if (!$orderId) {
			die('Invalid order id');
		}

		$order = PP::order($orderId);
		$subscription = $order->getSubscription();

		// Ensure that the subscription really belongs to the current viewer
		if ($order->getBuyer()->getId() != $this->my->id) {
			die('You do not own this order');
		}

		// delete order and subscription
		$order->delete();
		$state = $subscription->delete();

		if ($state === false) {
			// upgrade failed.
			$message = JText::_('COM_PP_ORDER_DELETION_FAILED');

			$this->info->set($message, 'error');
			return $this->redirectToView('dashboard');
		}

		$message = 'COM_PP_SUBSCRIPTION_DELETED_SUCCESSFULLY';
		$state = 'success';

		// @TODO: Determine if the subscription cancellation has failed

		$this->info->set($message, $state);
		$this->redirectToView('dashboard');
	}
}
