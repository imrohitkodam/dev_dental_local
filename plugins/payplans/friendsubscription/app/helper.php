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

class PPHelperFriendSubscription extends PPHelperStandardApp
{
	/**
	 * Determines if app is really enabled
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isEnabled()
	{
		if (!$this->app->getId()) {
			return false;
		}

		if (!$this->app->published) {
			return false;
		}

		return true;
	}

	/**
	 * Get Notify Email param
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function notifyUsers()
	{
		static $notify = null;

		if (is_null($notify)) {
			$notify = $this->params->get('notify_user');
		}

		return $notify;
	}

	public function sendEmail($user, $subscription, $type = 'buyer') 
	{
		$recipient = $user->getEmail();
	
		$subject = JText::_('COM_PP_FRIEND_SUBSCRIPTION_'.strtoupper($type).'_EMAIL_SUBJECT');

		$contents = JText::sprintf('COM_PP_FRIEND_SUBSCRIPTION_'.strtoupper($type).'_EMAIL_CONTENT', $subscription->getTitle(), $user->getName());

		$mailer = PP::mailer();
		$mailer->send($recipient, $subject, 'emails/custom/blank', array('contents' => $contents));
		return true;
	}

	public function sendEasySocialNotification($oriBuyer, $newBuyer, $subscription)
	{
		$esLib = PP::easysocial();

		if ($esLib->exists()) {

			$plan = $subscription->getPlan();

			$url = PPR::_('index.php?option=com_payplans&view=dashboard');
			$title = JText::sprintf('COM_PP_FRIEND_SUBSCRIPTION_FRIEND_NOTIFICATION_TITLE', $plan->getTitle());

			$systemOptions = array(
				'title' => $title,
				'context_type' => 'subscription',
				'context_ids' => $subscription->getId(),
				'url' => $url,
				'actor_id' => $oriBuyer->getId(),
				'uid' => $newBuyer->getid(),
				'aggregate' => false
			);

			$targets = array($newBuyer->getId());

			// Try to send the system notification
			ES::notify('payplans.friend.purchase', $targets, array(), $systemOptions, 3);
		}

	}

}
