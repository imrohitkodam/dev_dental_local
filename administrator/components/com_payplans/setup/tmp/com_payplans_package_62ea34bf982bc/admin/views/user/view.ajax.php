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

class PayPlansViewUser extends PayPlansAdminView
{
	/**
	 * Renders a browser user dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function browse()
	{
		$callback = $this->input->get('jscallback', '', 'word');

		$theme = PP::themes();
		$theme->set('callback' , $callback);

		$output = $theme->output('admin/user/dialogs/browse');

		return $this->resolve($output);
	}

	/**
	 * Import user subscription via csv
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function importUserSubscription()
	{
		$planId = $this->input->get('plan_id', '', 'default');
		$totalRecords = $this->input->get('totalRecords', 0, 'int');
		$importSubscriptionStatus = $this->input->get('importSubscriptionStatus', '', 'default');
		$importSubscriptionStartDate = $this->input->get('importSubscriptionStartDate', '', 'default');
		$importSubscriptionExpirationDate = $this->input->get('importSubscriptionExpirationDate', '', 'default');
		$importSubscriptionNote = $this->input->get('importSubscriptionNote', '', 'default');
		$limit = $this->input->get('limit', 5, 'int');

		$plan = PP::plan($planId);

		if (!$plan->getId()) {
			return $this->view->exception('COM_PP_INVALID_PLAN_ID');
		}

		// Get the file csv file from the directory
		$importCSVDirectory =  PP_MEDIA . '/import';
		$path = $importCSVDirectory . '/usersubscription.csv';

		// Get the current CSV file total items
		$data = PP::parseCSV($path, false, false);

		// Standard CSV file header columns
		$fields = [
			'username',
			'fullname',
			'email',
			'password',
			'business_name',
			'tin',
			'business_address',
			'business_city',
			'business_state',
			'business_zip',
			'business_country',
			'subscription_status',
			'subscription_date', 
			'subscription_exp_date', 
			'subscription_notes'
		];

		// Determine whether the first loop already can import all the data
		$isOnlyProcessOnetime = ($totalRecords - 1) - $limit;
		$isOnlyProcessOnetime = $isOnlyProcessOnetime === 0 ? true : false;

		// Total items from the CSV file
		$currentCSVTotal = count($data);

		// Determines if there is still have the items need to be import
		$remainDataCount = $currentCSVTotal - $limit;
		$hasBalance = true;

		// Set the balance to false as long as no more data to proceed
		if ($remainDataCount <= 0 || $isOnlyProcessOnetime) {
			$hasBalance = false;
		}

		// Calculate the progress bar percentage
		$progressPercentage = ($totalRecords - ($currentCSVTotal - $limit)) / $totalRecords * 100;
		$progressPercentage = round($progressPercentage);

		// Skip this process if there's nothing to import
		if (!$data) {
			$progressPercentage = 100;
			return $this->ajax->resolve('noitem', $progressPercentage);
		}

		$session = JFactory::getSession();
		$importSessionStat = $session->get('PP_IMPORT_STAT', '', 'PAYPLANS');

		if (!$importSessionStat) {
			$importSessionStat = new stdClass();
			$importSessionStat->newUser = 0;
			$importSessionStat->existsUser = 0;
			$importSessionStat->totalSuccess = 0;
			$importSessionStat->totalFailure = 0;
		}

		$model = PP::model('user');
		
		$newUserCount = [];
		$existsUserCount = [];
		$success = [];
		$failed = [];

		// Process the user here.
		$processed = 0;

		foreach ($data as $key => $item) {

			if ($item && isset($item[0]) && ($item[0] === 'username')) {
				// we know this is a header row. skip it.
				unset($data[$key]);
				continue;
			}

			// Import the item
			$result = $model->importUser($item, $fields, $planId, $importSubscriptionStatus, $importSubscriptionStartDate, $importSubscriptionExpirationDate, $importSubscriptionNote);			

			// Determine whether the CSV data imported new user or existing user
			$isNewUser = $result->newUser ? 1 : 0;

			if ($result->state) {
				$importSessionStat->totalSuccess++;
				
				if ($isNewUser) {
					$importSessionStat->newUser++;
				} else {
					$importSessionStat->existsUser++;
				} 
				
			} else {
				$importSessionStat->totalFailure++;
			}

			// update import session stat
			$session->set('PP_IMPORT_STAT', $importSessionStat, 'PAYPLANS');

			$this->ajax->append('[data-user-progress-status]', $result->message . '<br />');

			unset($data[$key]);
			$processed++;

			if ($processed >= $limit) {
				break;
			}
		}

		// Generate CSV data from array as buffer
		$tmp = fopen('php://temp', 'rw');

		foreach ($data as $row) {
			fputcsv($tmp, $row);
		}

		rewind($tmp);
		$csv = stream_get_contents($tmp);
		fclose($tmp);

		JFile::delete($path);
		JFile::write($path, $csv);

		$hasmore = false;

		// Determine if there got balance which mean that still have some items haven't import finish yet
		if ($hasBalance) {
			$hasmore = true;
		}

		if (!$hasmore) {

			$progressPercentage = 100;

			$stat = JText::sprintf('COM_PP_USER_IMPORT_TOTAL_NEW_USER', $importSessionStat->newUser) . '<br />';
			$stat .= JText::sprintf('COM_PP_USER_IMPORT_TOTAL_EXISTING_USER', $importSessionStat->existsUser) . '<br />';
			$stat .= JText::sprintf('COM_PP_USER_IMPORT_TOTAL_SUCCESS_USER', $importSessionStat->totalSuccess) . '<br />';
			$stat .= JText::sprintf('COM_PP_USER_IMPORT_TOTAL_FAIL_USER', $importSessionStat->totalFailure) . '<br />';

			$this->ajax->append('[data-user-progress-stat]', $stat);

			// we need to clear the stat variable that stored in session.
			$jSession = JFactory::getSession();
			$jSession->set('PP_IMPORT_STAT', '', 'PAYPLANS');

			if (JFile::exists($path)) {
				JFile::delete($path);
			}
		}

		return $this->ajax->resolve($hasmore, $progressPercentage);
	}	
}

