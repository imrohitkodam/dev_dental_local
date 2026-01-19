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

class PayplansControllerSubscription extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('orders');

		$this->registerTask('save', 'store');
		$this->registerTask('savenew', 'store');
		$this->registerTask('apply', 'store');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		$this->registerTask('remove', 'delete');
		$this->registerTask('close', 'cancel');

		$this->registerTask('updateStatus', 'updateStatus');
	}

	/**
	 * Updates status of subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateStatus()
	{
		$ids = $this->input->get('cid', [], 'array');
		$status = $this->input->get('status', 0, 'int');

		if (!$ids) {
			$this->info->set('COM_PP_INVALID_IDS', PP_MSG_ERROR);
			return $this->redirectToView('subscription');
		}

		foreach ($ids as $id) {
			$subscription = PP::subscription((int) $id);
			$order = $subscription->getOrder();

			// Do not allow admins to update the subscription to active/inactive if order doesn't permit
			// Subscription can be expired only if cancelled already
			if ($status != PP_SUBSCRIPTION_EXPIRED && !$order->updateable()) {
				$this->info->set('COM_PAYPLANS_SUBSCRIPTION_UPDATE_REFUND_SUBSCRIPTION', 'danger');
				return $this->redirectToView('subscription');
			}

			// Set as Activate
			if ($status == PP_SUBSCRIPTION_ACTIVE) {
				$subscription->activate();
			}

			// Set as Refund
			if ($status == PP_SUBSCRIPTION_HOLD) {
				$subscription->refund();
			}

			// Set as expired
			if ($status == PP_SUBSCRIPTION_EXPIRED) {
				$subscription->expired();
			}
		}

		$this->info->set('COM_PP_SUBSCRIPTION_STATUS_SUCCESS_MESSAGE', 'success');
		return $this->redirectToView('subscription');
	}


	/**
	 * Delete a list of subscriptions from the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', 0, 'int');

		foreach ($ids as $id) {
			$subscription = PP::subscription((int) $id);
			$order = $subscription->getOrder();
			$order->delete();
			$state = $subscription->delete();

			// $state = $subscription->delete();
			if ($state === false) {
				$error = $subscription->getError();

				$this->info->set($error->text, $error->type);
				return $this->redirectToView('subscription');
			}
		}

		$this->info->set('COM_PP_SUBSCRIPTION_DELETED_SUCCESSFULLY', 'success');

		return $this->redirectToView('subscription');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=subscription');
	}

	/**
	 * Saves a subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$id = $this->input->get('subscription_id', 0, 'int');
		$data = $this->input->post->getArray();

		$subscription = PP::subscription($id);
		$order = $subscription->getOrder();

		// Create a new order
		if (!$id) {
			$data['user_id'] = $this->input->get('user_id', 0, 'int');

			if (!$data['user_id']) {
				$this->info->set('COM_PP_SUBSCRIPTION_USER_REQUIRED', 'danger');
				return $this->redirectToView('subscription', 'form');
			}

			$order->setBuyer($data['user_id']);
			$order->save();

			$data['order_id'] = $order->order_id;

			// For new orders we need to assign the plan to the subscription
			$data['plan_id'] = $this->input->get('plan_id', 0, 'int');
			$plan = PP::plan($data['plan_id']);

			$subscription->setPlan($plan);
		}

		// Set plan details in params
		$params = $subscription->getParams();

		// append subscription params
		$paramsData = $data['params'];
		foreach ($paramsData as $key => $value) {
			$params->set($key, $value);
		}

		$data['params'] = $params;

		// Do not allow admins to update the subscription to active/inactive if order doesn't permit
		// Subscription can be expired only if cancelled already
		if ($data['status'] != PP_SUBSCRIPTION_EXPIRED && !$order->updateable()) {
			$this->info->set('COM_PAYPLANS_SUBSCRIPTION_UPDATE_REFUND_SUBSCRIPTION', 'danger');
			return $this->redirectToView('subscription');
		}

		// If the admin sets subscription to active, we need to activate their subscription
		$activateSubscription = false;

		if (in_array($subscription->getStatus(), [PP_SUBSCRIPTION_NONE, PP_SUBSCRIPTION_EXPIRED, PP_SUBSCRIPTION_HOLD]) && $data['status'] == PP_SUBSCRIPTION_ACTIVE) {
			$data['status'] = PP_SUBSCRIPTION_NONE;
			$activateSubscription = true;
		}

		$params = isset($data['params']) ? $data['params'] : '';
		unset($data['params']);

		// Save custom details
		$customDetails = isset($data['subscriptionparams']) ? $data['subscriptionparams'] : '';
		unset($data['subscriptionparams']);

		// Ensure that it is an array instead of JRegistry
		if ($params instanceof JRegistry) {
			$params = $params->toArray();
		}

		if ($customDetails) {
			$params = array_merge($params, $customDetails);
		}

		// before we bind the data, we need to handle the dates. # 816
		if (isset($data['subscription_date']) && $data['subscription_date']) {
			$data['subscription_date'] = PP::convertToGMTDate($data['subscription_date']);
		}

		if (isset($data['expiration_date']) && $data['expiration_date']) {
			$data['expiration_date'] = PP::convertToGMTDate($data['expiration_date']);
		}

		$subscription->bind($data);

		if ($params) {
			$subscription->setParams($params);
		}

		$subscription->save();
		
		// Reload the order with the correct subscription data
		$order->refresh(true);
		$order->save(true);

		// Create invoice only if new order
		if (!$id) {
			$invoice = $order->createInvoice($subscription);
			$invoice->confirm(0);
			$invoice->save();
		}

		if ($activateSubscription) {
			$subscription->activate();
		}

		$message = 'COM_PP_SUBSCRIPTION_CREATED_SUCCESSFULLY';
		$actionString = JText::_('COM_PP_ACTIONLOGS_SUBSCRIPTION_CREATED');

		if ($id) {
			$message = 'COM_PP_SUBSCRIPTION_UPDATED_SUCCESSFULLY';
			$actionString = JText::_('COM_PP_ACTIONLOGS_SUBSCRIPTION_UPDATED');
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'subscription', [
			'link' => 'index.php?option=com_payplans&view=subscription&layout=form&id=' . $subscription->getId(), 
			'key' => $subscription->getKey()
		]);

		$this->info->set($message, 'success');

		if ($this->task === 'saveNew') {
			return $this->redirectToView('subscription', 'form');
		}

		if ($this->task === 'apply') {
			return $this->redirectToView('subscription', 'form', 'id=' . $subscription->getId());
		}

		return $this->redirectToView('subscription');
	}

	/**
	 * Extends an existing subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function extend()
	{
		$ids = $this->input->get('cid', [], 'int');
		$timeframe = $this->input->get('extend_time', false, '');

		foreach ($ids as $id) {

			$id = (int) $id;
			$subscription = PP::subscription($id);

			// If subscription is expired then add expiration time from now and activate the subscription.
			$subscription->extend($timeframe);
		}

		$message = JText::_('COM_PP_SUBSCRIPTION_EXTENDED_SUCCESSFULLY');
		$this->info->set($message, 'success');

		return $this->redirectToView('subscription');
	}


	/**
	 * Process subscription upgrade
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function upgrade()
	{
		$id = $this->input->get('id', 0, 'int');
		$upgradeType = $this->input->get('type', '', 'default');
		$newPlanId = $this->input->get('upgrade_to', 0, 'int');

		if (!$id || !$newPlanId) {
			$message = JText::_('COM_PP_UPGRADE_SUBSCRIPTION_INVALID_ID');
			$this->info->set($message, 'danger');

			return $this->redirectToView('subscription');
		}

		if (!$upgradeType) {
			$message = JText::_('COM_PP_UPGRADE_SUBSCRIPTION_INVALID_PAYMENT_MODE');
			$this->info->set($message, 'danger');

			return $this->redirectToView('subscription');
		}

		$sub = PP::subscription($id);
		$order = $sub->getOrder();

		$newPlan = PP::plan($newPlanId);

		// Determine if we have any dynamic modifier being applied to the upgrade
		$modifier = $this->input->get('modifier', false, 'string');

		// process upgrade
		$newInvoice = PPUpgrade::upgradeSubscription($sub, $newPlan, $upgradeType, $modifier);

		if ($newInvoice === false) {
			// upgrade failed.
			$message = JText::_('COM_PP_UPGRADE_SUBSCRIPTION_FAILED');

			$this->info->set($message, 'danger');
			return $this->redirectToView('subscription');
		}

		// trigger onPayplansUpgradeBeforeDisplay, e.g discount related apps
		$args = [$newPlanId, $sub, $newInvoice];
		$results = PPEvent::trigger('onPayplansUpgradeBeforeDisplay', $args, '', $sub);

		$newSub = PP::subscription($newInvoice->table->object_id);

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_SUBSCRIPTION_UPGRADED', 'subscription', [
			'link' => 'index.php?option=com_payplans&view=subscription&layout=form&id=' . $sub->getId(),
			'key' => $sub->getKey(),
			'newSubLink' => 'index.php?option=com_payplans&view=subscription&layout=form&id=' . $newSub->getId(),
			'newSubKey' => $newSub->getKey()
		]);

		// show messsage
		$message = JText::_('COM_PP_UPGRADE_SUBSCRIPTION_SUCCESSFULLY_FREE_TYPE');

		if ($upgradeType === 'offline'){
			$message = JText::_('COM_PP_UPGRADE_SUBSCRIPTION_SUCCESSFULLY_OFFLINE_TYPE');
		}

		if ($upgradeType === 'user') {
			$message = JText::_('COM_PP_UPGRADE_SUBSCRIPTION_SUCCESSFULLY_PARTIAL_TYPE');
		}

		$newSub = $newInvoice->getSubscription();

		$this->info->set($message, 'success');
		return $this->redirectToView('subscription', 'form', 'id=' . $newSub->getId());
	}

	/**
	 * Executes Subscription Cancellation for customer
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function terminate()
	{
		$id = $this->input->get('orderId', 0, 'int');

		$order = PP::order($id);
		$subscription = $order->getSubscription();

		$state = $order->terminate();

		$message = 'COM_PP_CANCEL_SUCCESS';

		if (!$state) {
			$message = 'COM_PP_CANCEL_FAILED';
		}

		$this->info->set($message, $state ? 'success' : 'danger');

		return $this->redirectToView('subscription', 'form', 'id=' . $subscription->getId());
	}

	/**
	 * Add Invoice for recurring Subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addInvoice()
	{
		$id = $this->input->get('orderId', 0, 'int');

		return $this->app->redirect('index.php?option=com_payplans&task=order.createInvoice&id='.$id);
	}

}
