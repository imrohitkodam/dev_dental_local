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

class PPHelperEmail extends PPHelperStandardApp
{
	
	/**
	 * Retrieves a list of abandoned invoices
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAbandonedInvoices($expiry)
	{
		$config = PP::config();

		$cronTime = PP::date($config->get('cronAcessTime'));
		$current = PP::date();

		$cronTime->subtractExpiration($expiry);
		$current->subtractExpiration($expiry);

		$db = PP::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__payplans_invoice');
		$query[] = 'WHERE ' . $db->qn('status') . '=' . $db->Quote(PP_INVOICE_CONFIRMED);
		$query[] = 'AND ' . $db->qn('created_date') . '>' . $db->Quote($cronTime->toSql()) . ' AND ' . $db->qn('created_date') . '<' . $db->Quote($current->toSql());
		$query = implode(' ', $query);

		$db->setQuery($query);
		$invoices = $db->loadObjectList();

		return $invoices;
	}

	/**
	 * Determines on which status should the emails be sent to
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getStatusToSend()
	{
		if ($this->getWhenToSend() === 'on_cancellation') {
			return PP_ORDER_CANCEL;
		}

		return $this->params->get('on_status', PP_NONE);
	}

	/**
	 * Retrieves the subject used for e-mail
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getWhenToSend()
	{
		$when = $this->params->get('when_to_email', '');

		return $when;
	}

	/**
	 * Mark a subscription as sent
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function markSent(PPSubscription $subscription, $event, $expiryTime, $invoiceCount = '')
	{
		$params = $subscription->getParams();
		$params->set($event . $expiryTime . $invoiceCount, true);
		$subscription->params = $params->toString();

		return $subscription->save();
	}

	/**
	 * Sends a new notification via the notification library
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function send($object)
	{
		$notification = PP::notifications($this->app->getId());
		return $notification->send($object);
	}

	/**
	 * Given the previous and new object, determines if 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function shouldSendEmailForRecurringLastCycle()
	{
		$send = (bool) $this->params->get('on_lastcycle', false);

		return $send;
	}

	/**
	 * Given the previous and new object, determines if 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function shouldSendEmail($prev, $new)
	{
		// These events should only be executed by cronjob
		$cronItems = array('on_preexpiry', 'on_postexpiry', 'on_postactivation', 'on_cart_abondonment', 'on_preexpiry_trial');

		if (in_array($this->getWhenToSend(), $cronItems)) {
			return false;
		}

		// no need to trigger if previous and current state is same
		if ($prev != null && $prev->getStatus() == $new->getStatus()) {
			return false;
		}

		// check the status
		if ($new->getStatus() != $this->getStatusToSend()){
			return false;
		}

		$results = PPEvent::trigger('onPayplansBeforeSendEmail', array($prev, $new));

		if (in_array(false, $results)) {
			return false;
		}
		
		return true;
	}
}