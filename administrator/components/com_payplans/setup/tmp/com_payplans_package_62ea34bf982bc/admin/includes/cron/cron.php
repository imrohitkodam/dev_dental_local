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

class PPCron extends PayPlans
{
	/**
	 * Convert Seconds to hh:mm:ss format
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function convertSecondsTohhmmss($frequency)
	{
		$hh = intval($frequency / 3600);
		$mm = (intval($frequency /60) % 60)*4;
		$ss = $frequency % 60;
		
		if(strlen($hh) == 1) {
			$hh = "0".$hh;
		}

		if(strlen($mm) == 1) {
			$mm = "0".$mm;
		}

		if(strlen($ss) == 1) {
			$ss = "0".$ss;
		}

		$time = "000000".$hh.$mm.$ss;
		return $time;
	}

	/**
	 * Retrieves the image that should be used to serve the output from cron
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getImage()
	{
		static $image = null;

		if (is_null($image)) {
			$file = PP_MEDIA . '/images/cron.png';
			$image = file_get_contents($file);
		}

		return $image;
	}

	/**
	 * Generates the url for cron
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getImageUrl()
	{
		$url = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&view=cron&tmpl=component';
		return $url;
	}

	/**
	 * Determines if cron 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function hasBeenRunning()
	{
		static $running = null;

		if (!is_null($running)) {
			return $running;
		}

		if (!$this->config->get('expert_run_automatic_cron')) {
			$running = true;

			return $running;
		}

		// Determines the last cron access time
		$lastExecuted = $this->config->get('cronAcessTime');

		if (!$lastExecuted) {
			$running = false;

			return $running;
		}

		$frequency = $this->config->get('cronFrequency');

		if ($this->config->get('microsubscription')) {
			$frequency = ($this->config->get('cronFrequency') / PP_CONFIG_CRONFREQUENCY_DIVIDER);
		}

		$time = $this->convertSecondsTohhmmss($frequency);

		$date = PP::date($lastExecuted);
		$expiryDateTime = $date->addExpiration($time);
		$unixTimeStamp = $expiryDateTime->toUnix(); 
		
		$current_time = PP::date();
		$currentUnixTime = $current_time->toUnix();

		if ($currentUnixTime > $unixTimeStamp) {
			$running = false;
			
			return $running;
		}

		$running = true;

		return $running;
	}

	/**
	 * Process user download requests
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function processDownloadRequests()
	{
		$gdpr = PP::gdpr();
		
		return $gdpr->cron();
	}

	/**
	 * Process recurring billing and expired subscriptions
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processSubscriptions($time = null)
	{
		$model = PP::model('Subscription');

		$subscriptions = $model->getExpiredSubscriptions($time);

		if (!$subscriptions) {
			return true;
		}

		foreach ($subscriptions as $item) {

			$subscription = PP::subscription($item->subscription_id);

			// subscription is fixed
			if ($subscription->isRecurring() == false || !$subscription->isRequiredToProcessByCron()) {
				$subscription->setStatus(PP_SUBSCRIPTION_EXPIRED)->save();
				$model->unlock($item->subscription_id);
				continue;
			}
			
			// If order is already cancelled or expired
			$order = $subscription->getOrder();
			
			if ($order->isExpired() || $order->isCancelled()) {
				$subscription->setStatus(PP_SUBSCRIPTION_EXPIRED)->save();
				$model->unlock($item->subscription_id);
				continue;
			}
			
			// For recurring subscription, ask for next payment 
			$now = JFactory::getDate();
			$user = $subscription->getBuyer();

			if ($user->id) {
				$args = array(&$subscription);
				PPEvent::trigger('onPayplansNewPaymentRequest', $args);
			}

			// reinitiate subscription incase subscription being udpate from else where.
			$subscription = PP::subscription($item->subscription_id);

			// check the new expiry date of subscription, if payment was successfull, it must have updated the expiry time
			$newExpirationDate = $subscription->getExpirationDate();

			if ($this->config->get('expert_wait_for_payment') != '000000000000') {
				$newExpirationDate->addExpiration($this->config->get('expert_wait_for_payment'));
			}
			
			// if grace period is finished, expire it
			if($newExpirationDate->toUnix() < $now->toUnix()){
				$subscription->setStatus(PP_SUBSCRIPTION_EXPIRED)->save();
				$model->unlock($item->subscription_id);
				continue;
			}


			// payment process. lets unlock this subscription record.
			$model->unlock($item->subscription_id);
		}
		
		return true;
	}

	/**
	 * Determines if the cron should be running
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function shouldRun($now = null, $defaultExecutionTime = 60)
	{
		$debug = $this->input->get('debug', false, 'bool');

		if ($debug) {
			return true;
		}

		$frequency = ($this->config->get('microsubscription')) ? $this->config->get('cronFrequency') / PP_CONFIG_CRONFREQUENCY_DIVIDER : $this->config->get('cronFrequency');
		$accessTime = $this->config->get('cronAcessTime');
		$currentAccessTime = 0;
		
		if ($this->config->get('currentCronAcessTime') != 0) {
			$currentAccessTime = $this->config->get('currentCronAcessTime');
		}
		
		if (empty($currentAccessTime)) {
			return true;
		}
		
		if($now === null){
			$now = PP::date();
			$now = $now->toUnix();
		}	

		// if diff of $accessTime and $currentAccessTime is greater than  $defaultExecutionTime than probaly there is cron failure
		if(($currentAccessTime - $accessTime) > $defaultExecutionTime){
			return true;
		}
		
		// if diff of $now and $currecAccessTime is greater than $frequency then return true
		if(($now - $currentAccessTime) > $frequency){
			return true;
		}	
		
		return false;	
	}

	/**
	 * Delete orphan orders
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function deleteOrphanOrders()
	{
		$periodToSubtract = $this->config->get('expert_auto_delete');
		
		if ($periodToSubtract == "NEVER") {
			return ;
		}

		$date = PP::date();

		$modifiedDate = $date->subtractExpiration($periodToSubtract);

		// PP_NONE is added for checking subscription's status also
		$model = PP::model('Order');
		$items = $model->getDummyOrders($modifiedDate, array(PP_NONE, PP_ORDER_CONFIRMED), PP_NONE);

		if ($items) {
			foreach ($items as $item) {
				$order = PP::order($item);
				$state = $order->delete();
			}
		}
		
		return true;
	}

	/**
	 * Delete pdf invoices
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function deletePdfInvoices()
	{
		if (!$this->config->get('enable_pdf_invoice')) {
			return true;
		}
		
		$folder = JPATH_ROOT . '/media/com_payplans/tmp/pdfinvoices';

		JFolder::delete($folder);
	
		return true;
	}

	/**
	 * Process plan scheduling
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processPlanScheduling()
	{
		// Get all apps including the unpublished apps
		$plans = PPHelperPlan::getPlans(array());

		foreach ($plans as $plan) {
			$plan->checkSchedulingStatus();
		}

		return true;
	}

	/**
	 * Purge expired cronjob logs
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function purgeExpiredLogs()
	{
		$model = PP::model('Log');
		$model->purgeLogs('Payplans_Cron');
	}

	/**
	 * Purge downloads that has expired
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function purgeExpiredDownloads()
	{
		$days = $this->config->get('users_download_expiry');

		$model = PP::model('Download');
		$model->deleteExpiredRequests($days);

		return true;
	}

	/**
	 * Process statistics data of payplans
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processStatistics()
	{
		$date = PP::date();

		//stats_build_cron_time
		$cronTime = $this->config->get('stats_build_cron_time', '');
		$cronInterval = $this->config->get('stats_build_cron_interval', '000000060000');

		// if there is previous stats build execution datetime
		if ($cronTime) {

			$cronDateTime = PP::date($cronTime);

			// lets add the interval into previous execution datetime for later comparison
			$cronDateTime->addExpiration($cronInterval);

			// check if we should run the stats build or not.
			if($cronDateTime->toUnix() > $date->toUnix()) {
				return true;
			}
		}

		$statistics = PP::statistics();
		$statistics->calculateStatistics();

		// save the current time into config after process stats build.
		$date = PP::date();
		$model = PP::model('Config');
		$model->save(array(
			'stats_build_cron_time' => $date->toUnix()
		));

	}

	/**
	* Check if reset serial is required
	*
	* @since 4.0.0
	* @access  public
	*/
	public function resetInvoiceSerial()
	{
		// Check for reset Invoice Serial
		$config = PP::config();
		$autoResetSerial = $config->get('auto_reset_invoice_serial', false);
		if (!$autoResetSerial) {
			return true;
		}

		// Get CurrentYear 
		$currentYear   = date("Y");
		$resetSerial = $config->get('resetSerial', 0);
		// Do nothing if invoice serial already reset for current year
		if ($resetSerial && $resetSerial == $currentYear) {
			return true;
		}

		// Get current year first date
		$startDate = date('m/d/Y', strtotime($currentYear."-01-01"));

		$startDate = PP::date($startDate);
		$startDate = $startDate->toSql();

		$model = PP::model('Invoice');

		$lastYearInvoice = $model->getLastYearLatestInvoice($startDate);

		if (empty($lastYearInvoice)) {
			return true;
		}

		$lastYearInvoice = array_pop($lastYearInvoice); 

		
		$currentYearFirstInvoice = $model->getCurrentYearFirstInvoice($startDate, $limit = 1);

		if (empty($currentYearFirstInvoice)) {
			$model = PP::model('config');
			$model->save(array('expert_invoice_last_serial'=> 0, 'resetSerial' => $currentYear));
			return true;
		}

		$currentYearFirstInvoice = array_pop($currentYearFirstInvoice);

		$lastSerial = $lastYearInvoice->serial;
		$currentSerial = $currentYearFirstInvoice->serial;

		if ($currentSerial == $lastSerial + 1) {
			// Reset Invoice serial 
			$currentYearInvoices = $model->getCurrentYearFirstInvoice($startDate);
			$lastCounter = 0;

			$serialFormate = $config->get('expert_invoice_serial_format', '[[number]]');

			foreach ($currentYearInvoices as $value) {
				
				$lastCounter++;
				$invoice = PP::invoice($value->invoice_id);

				$invoicePaidOn = PP::date($value->paid_date);
				$paidYear = PPFormats::date($invoicePaidOn, '%Y');
				$paidMonth = PPFormats::date($invoicePaidOn, '%m');
				$paidDate = PPFormats::date($invoicePaidOn, '%d');
				$paidDay = PPFormats::date($invoicePaidOn, '%A');

				$search  = array('[[number]]', '[[date]]', '[[month]]', '[[year]]','[[day]]');
				$replace = array($lastCounter, $paidDate, $paidMonth, $paidYear, $paidDay);

				$newSerial = str_replace($search, $replace, $serialFormate);

				$invoice->serial = $newSerial;
				$invoice->refresh()->save();
			}

			$model = PP::model('config');
			$model->save(array('expert_invoice_last_serial' => $lastCounter, 'resetSerial' => $currentYear));

			return true;
		}

	}
}