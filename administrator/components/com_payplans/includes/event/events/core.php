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

class PPEventCore extends PayPlans
{
	/**
	 * Triggered after an order is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function onPayplansOrderAfterSave($previous, $current)
	{
		// Consider Previous State also
		if (isset($previous) && $previous->getStatus() == $current->getstatus()) {
			return true;
		}

		// if there is change in status of order
		switch ($current->getStatus()) {
			case PP_NONE:
				$subsStatus = PP_NONE;
				break;

			case PP_ORDER_CONFIRMED:
				$subsStatus = PP_NONE;
				break;

			case PP_ORDER_COMPLETE:
				$subsStatus = PP_SUBSCRIPTION_ACTIVE;
				break;

			case PP_ORDER_HOLD:
				$subsStatus = PP_SUBSCRIPTION_HOLD;
				break;

			case PP_ORDER_EXPIRED:
				$subsStatus = PP_SUBSCRIPTION_EXPIRED;
				break;

			case PP_ORDER_PAID:
			default:
				$subsStatus = PP_NONE;
		}

		$subs = $current->getSubscription(true);

		if (is_a($subs, 'PPSubscription')) {
			$subs->load($subs->getId());

			// no change in status then need not to update
			if ($subs->getStatus() == $subsStatus || !$subsStatus) {
				return true;
			}

			$subs->setStatus($subsStatus)->save();
		}
		return true;
	}

	/**
	 * Triggered before an order is deleted
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function onPayplansOrderBeforeDelete($order)
	{
		$subscription = $order->getSubscription();

		// delete all the subscriptions linked with this order
		if (!empty($subscription)) {
			$subscription->delete();
		}

		$invoices = $order->getInvoices();

		if (!empty($invoices)) {
			foreach ($invoices as $invoice) {
				$invoice->delete();
			}
		}

		return true;
	}

	/**
	 * Triggered before an invoice is deleted
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function onPayplansInvoiceBeforedelete($invoice)
	{
		$payments = $invoice->getPayment();

		// delete all the payment linked with this order
		if (!empty($payments)) {  
			$payments->delete();
		}

		//get all the transaction records
		//related to tranaction and then delete transaction
		$transactions = $invoice->getTransactions();

		if (!empty($transactions)) {
			self::deleteTransaction($transactions);
		}

		//delete all modifier related to invoice
		$modifiers = $invoice->getModifiers();

		if (!empty($modifiers)) {
			self::deleteModifiers($modifiers);
		}

		return true;
	}

	protected static function deleteTransaction($transactions = [])
	{
		foreach ($transactions as $transaction) {
			$transaction->delete();
		}
	}
	
	protected static function deleteModifiers($modifiers = [])
	{
		foreach ($modifiers as $modifier) {
			$modifier->delete();
		}
	}

	/**
	 * Triggered by cronjob
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function onPayplansCron()
	{
		$cron = PP::cron();

		// Process recurring and expired subscriptions
		$cron->processSubscriptions();

		// Delete orphan orders
		$cron->deleteOrphanOrders();

		// Process Plan Scheduling
		$cron->processPlanScheduling();

		// Update the statistics data
		$cron->processStatistics();

		// Purge expired download requests
		$cron->purgeExpiredDownloads();
		
		// Process download requests
		$cron->processDownloadRequests();

		// Delete pdf invoices folder
		$cron->deletePdfInvoices();

		// Reset Invoice Serial
		$cron->resetInvoiceSerial();

		return true;
	}
	
	/**
	 * Append module positions based on views
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function onPayplansViewAfterRender($view, $task, &$output)
	{
		$position = 'payplans-';
		$app = JFactory::getApplication();

		if (PP::isFromAdmin()) {
			$position .= 'admin-';
		}

		$name = $view->getName();

		if (isset($name)) {
			$position .=  $name . '-';
		}

		if (isset($task)) {
			$position .= $task . '-';
		}

		$theme = PP::themes();

		// Append modules
		$modulehtmlTop = $theme->renderModule($position . 'top');
		$modulehtmlBottom = $theme->renderModule($position . 'bottom');

		// update output variable
		$output = $modulehtmlTop . $output . $modulehtmlBottom;

		return true;
	}

	/**
	 * Before deleting subscription changed its status to expired
	 * so as to trigger all the app which are set on status "Subscription-expired" 
	 * and do what thay are expected to on subscription expired status before the subscription gets deleted
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSubscriptionBeforeDelete($object)
	{
		// set deleteing  to true so that it won't ask for payment on order deletion
		$object->deleting = true;

		// Expire only when it is already active
		if ($object->isActive() || $object->isOnHold()) {
			$object->setStatus(PP_SUBSCRIPTION_EXPIRED);
			$object->save();
		}

		// IMP : Trigger event for resource cleaning 
		// so that app can work on this to remove the assigned resource
		$args = [$object];

		PPEvent::trigger('onPayplansSubscriptionCleanResource', $args);

		PP::deleteCustomDetailFiles(PP_CUSTOM_DETAILS_TYPE_SUBSCRIPTION, $object->getId());

		return true;
	}

	/**
	 * Internal trigger to replace tokens with proper values
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansRewriterReplaceTokens($refObject, $rewriter)
	{
		$user = false;

		if (method_exists($refObject, 'getBuyer')) {
			$user = $refObject->getBuyer(PP_INSTANCE_REQUIRE);
		}
		
		if (!$user && !($refObject instanceof PPUser)) {
			return;
		}
		
		$param = (!$user) ? $refObject->getParams() : $user->getParams();
		$data = $param->toArray();
		
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$data[$key]= implode("\n", $value);
			}
		}
		
		$data = (object)$data;
		$data->name = 'Userdetail';
		$rewriter->setMapping($data, false);
		return ;
	}

	/**
	 * After saving the subscription
	 * so as to trigger on "Subscription-Active" state to notify admin about new order
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// no need to trigger if previous and current state is same
		if ($prev != null && ($prev->getStatus() == $new->getStatus())) {
			return true;			
		}

		// if subscription is active
		if ($new->isActive()) {
			// Apply fixed expiration date if applicable
			self::applyFixedExpirationDate($new);

			// We reload the subscription so that we get the latest data
			// Check if subscription moderation required 

			/// notify admin about new order not configured
			if (PP::Config()->get('notify_admin_new_order')) {
				self::notifyAdmin($new);
			}
		}

		return true;
	}

	/**
	 * Before saving the subscription
	 * Process subscription moderation
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSubscriptionBeforeSave($prev, $new)
	{
		// no need to trigger if previous subscription not set
		if (!isset($prev)) {
			return true;
		}

		if ($prev->getStatus() != PP_SUBSCRIPTION_ACTIVE && $new->getStatus() == PP_SUBSCRIPTION_ACTIVE) {

			// We reload the subscription so that we get the latest data
			// Check if subscription moderation required 
			$isModerate = $new->processModeration();

			if ($isModerate) {
				// set subscription status to inactive on subscription object
				$new->setStatus(PP_SUBSCRIPTION_HOLD);

				// Set the status to onhold
				// Stop the subscription time. Wait for it to be approved later
				$new->table->status = PP_SUBSCRIPTION_HOLD;
				$new->table->expiration_date = '0000-00-00 00:00:00';
				
				// Next we need to send the moderation email to admin and the user
				$namespace = 'emails/subscription/moderate';
				$subject = JText::_('COM_PAYPLANS_SUBSCRIPTION_MODERATION_REQUIRED_SUBJECT');

				$mailer = PP::mailer();
				$emails = $mailer->getAdminEmails();

				foreach ($emails as $email) {
					$mailer->send($email, $subject, $namespace, ['type' => 'ADMIN']);
				}

				$subject = JText::_('COM_PAYPLANS_SUBSCRIPTION_UNDER_MODERATION_SUBJECT');

				$buyerEmail = $new->getBuyer()->getEmail();
				$mailer->send($buyerEmail, $subject, $namespace, ['type' => 'USER']);
			}
		}

		return true;
	}

	/**
	 * This is to notify admin in email about new order
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected static function notifyAdmin($subscription)
	{
		$mailer = PP::mailer();
		$emails = $mailer->getAdminEmails();

		// IMP: when there are no users who can receive system emails then return
		if (!$emails) {
			return true;
		}

		$rewriter = PP::rewriter();

		$jconfig = PP::jconfig();
		$siteName = $jconfig->get('sitename');

		$subject = JText::sprintf('COM_PAYPLANS_NOTIFY_ADMIN_ABOUT_NEW_ORDER', $siteName);
		// Replace Tokens
		$subject = $rewriter->rewrite($subject, $subscription);
		
		$emailTemplate = JPATH_ROOT . '/components/com_payplans/themes/wireframe/emails/subscription/admin.php';

		// Check for template overrides
		$currentTemplate = PP::getJoomlaTemplate();
		$overridePath = JPATH_ROOT . '/templates/' . $currentTemplate . '/html/com_payplans';

		$fileName = 'emails/subscription/admin.php';

		$overrideFilePath = $overridePath . '/' . $fileName;
		$overrideExists = JFile::exists($overrideFilePath);

		$path = JPATH_ROOT . '/components/com_payplans/themes/wireframe/' . $fileName;

		if ($overrideExists) {
			$emailTemplate = $overrideFilePath;
		}
		
		ob_start();
		include($emailTemplate);
		$contents = ob_get_contents();
		ob_end_clean();

		// Replace Tokens
		$contents = $rewriter->rewrite($contents, $subscription);

		foreach ($emails as $email) {
			$mailer->send($email, $subject, 'emails/custom/blank', ['contents' => $contents]);
		}

		return true;
	}

	protected static function applyFixedExpirationDate($subscription, $isRenew = false)
	{
		$plan = $subscription->getPlan();
		// Check if Fixed expiration is valid for this plan
		if (!$plan->isFixedExpirationDate()) {
			return true;	
		}

		//if recurring plan and forever/Lifetime plan skip the process
		if ($plan->isRecurring() || $plan->isForever()) {
			return;
		}
		
		$expirationDate = $plan->getExpirationOnDate();
		$subscriptionDate = $subscription->getSubscriptionDate();
		$actualExpiration = $subscription->getExpirationDate();
		$planIsLifeTime = !$actualExpiration ? true : $actualExpiration->toUnix();

		if (!$subscriptionDate) {
			$subscriptionDate =  PP::date();
		}

		$from = $plan->getSubscriptionFromExpirationDate();
		$to = $plan->getSubscriptionEndExpirationDate();

		// when range is set and current subscription does not lie within that range
		if (!empty($from) && !empty($to) && (($subscriptionDate->toUnix() < $from->toUnix()) || ($subscriptionDate->toUnix() > $to->toUnix()))) { 
			return;
		}

		// when range is not set then change the expiration date anyway 
		if (empty($from) && empty($to)) {
			return self::changeExpirationDate($expirationDate, $subscription, $isRenew);
		}

		// when range is set then check subscription date whether lies in that range
		if (!empty($from) && !empty($to) && ($subscriptionDate->toUnix() >= $from->toUnix()) && ($subscriptionDate->toUnix() <= $to->toUnix())) {
			return self::changeExpirationDate($expirationDate, $subscription, $isRenew);
		}

		// when start date is set
		if (!empty($from) && ($subscriptionDate->toUnix() >= $from->toUnix())) {
			return self::changeExpirationDate($expirationDate, $subscription, $isRenew);
		}

		// when end date is set
		if (!empty($to) && ($subscriptionDate->toUnix() <= $to->toUnix())) {
			return self::changeExpirationDate($expirationDate, $subscription, $isRenew);
		}

		return true;
	}

	protected static function changeExpirationDate($expirationDate, $subscription, $isRenew)
	{
		$currentDate = PP::date();

		// do nothing when current date is greater than the expiration date
		if ($currentDate->toMySQL() > $expirationDate->toMySQL()) {
			return true;
		}

		$subscription->setExpirationDate($expirationDate);

		// in case of renewal of active subscription, fixed date expiration should apply
		if ($isRenew) {
			$subParams = $subscription->getParams();
			$subParams->set('fixed_date_expiration_applicable', 1);
			$subscription->setParams($subParams);
		}

		$subscription->save();
	}

	/**
	 * Triggered when an invoice is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansInvoiceAfterSave($prev, $new)
	{
		// Nothing changed
		if ($prev != null && $new->getStatus() == $prev->getStatus()) {
			return true;
		}
		
		// If it isn't marked as paid, don't do anything
		if ($new->isPaid()) {
			$order = $new->getOrder();
			$subscription = $order->getSubscription();

			// reset fixed date expiration params 
			$subParams = $subscription->getParams();

			$subParams->set('fixed_date_expiration_applicable', 0);
			$subscription->setParams($subParams);
			$subscription->save();
		
			self::applyFixedExpirationDate($subscription, true);	

			// Trigger an event after invoice paid for renewal
			if ($new->isRenewalInvoice()) {

				$args = [$subscription, $new];
				PPEvent::trigger('onPayplansSubscriptionRenewalComplete', $args, '', $subscription);
			}

			// Trigger an event after invoice paid for recurring renewal (recurring payment not the manaul renewal)
			if ($new->isRecurringRenewal()) {
				$args = [$subscription, $new];
				PPEvent::trigger('onPayplansSubscriptionRecurringRenewalComplete', $args, '', $subscription);	
			}

		}

		return true;
	}
}
