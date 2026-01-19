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

// require_once(__DIR__ . '/formatter.php');

class PPAppFriendSubscription extends PPApp
{

	/**
	 * Triggered before an invoice is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */	
	public function onPayplansInvoiceBeforeSave($prev, $new)
	{ 
		if (!isset($prev) || !isset($new)) {
			return true;
		}

		if ($new->getStatus() != PP_INVOICE_CONFIRMED) {
			return true;
		}

		$friendUserId = $this->input->get('app_friend_user_id');

		if ($friendUserId) {
			$plan = $new->getPlan();

			$friend = PP::user($friendUserId);

			// Check if Freind can subscribe this plan or not
			$canSubscribe = PPLimitsubscription::canSubscribe($friend, $plan->getId());

			if (!$canSubscribe) {

				PP::info()->set(JText::sprintf('COM_PP_FREINDSUBSCRIPTION_FRIEND_ALREADY_SUBSCRIBED_PLAN', $friend->getName()), 'error');

				$redirect = PPR::_('index.php?option=com_payplans&view=checkout&invoice_key=' . $new->getKey() . '&tmpl=component', false);
				return PP::redirect($redirect);
			}
		}

		return true;
	}

	public function onPayplansInvoiceAfterSave($prev, $new)
	{ 
		if (!isset($prev) || !isset($new)) {
			return true;
		}

		if ($new->getStatus() != PP_INVOICE_CONFIRMED) {
			// If invoice is not free then only return
			if (!$new->isFree()) {
				return true;
			}
		}

		//save friend id on subscription params
		$friendUserId = $this->input->get('app_friend_user_id');

		if ($friendUserId) {
			$order = $new->getReferenceObject();
			$subscription = $order->getSubscription();

			$subsParams = $subscription->getParams();

			$subsParams->set('friend_user_id', $friendUserId);

			$subscription->params = $subsParams->toString();
			$subscription->save();
		}

		return true;
	}

	public function onPayplansSubscriptionBeforeSave($prev, $new)
	{
		if ($prev != null && $prev->getStatus() == $new->getStatus()) {
			return true;
		}
		
		if ($new->isActive()) {
			$friendUserId = $new->getParams()->get('friend_user_id',0);

			if ($friendUserId && !$new->getParams()->get('friend_subscription_updated', 0)) {

				$originalBuyer = $new->getBuyer();

				$user = PP::user($friendUserId);

				$order = $new->getOrder();
				$invoice = $order->getinvoice();

				// Update subscription owner
				$invoice->updatePurchaser($user);

				// reset fixed date expiration params 
				$new->setBuyer($user->getId());
				$subParams = $new->getParams();
				$subParams->set('friend_subscription_updated', 1);
				$new->setParams($subParams);

				// Notify User and his freind about subscription 
				$helper = $this->getHelper();
				if ($helper->notifyUsers()) {

					// Send email to original Buyer
					$helper->sendEmail($originalBuyer, $new, 'buyer');

					// Send email to friend about subscription
					$helper->sendEmail($user,  $new, 'friend');
				}

				// always send es system notification to new owner
				$helper->sendEasySocialNotification($originalBuyer, $user, $new);
			}
		}

		return true;
	}
}
