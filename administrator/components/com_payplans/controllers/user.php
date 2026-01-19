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

 class PayplansControllerUser extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('users');
		
		$this->registerTask('save', 'store');
		$this->registerTask('savenew', 'store');
		$this->registerTask('apply', 'store');
	}

	/**
	 * Saves a user record
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$id = $this->input->get('id', 0, 'int');
		$user = PP::user($id);

		if (!$id || !$user->getId()) {
			$this->info->set('COM_PP_USER_SAVED_FAILED', 'danger');
			return $this->redirectToView('user');
		}
		$data = $this->input->post->getArray();
		$preference = isset($data['preference']) ? $data['preference'] : '';
		$params = isset($data['params']) ? $data['params'] : '';

			// Save custom details
		$customDetails = isset($data['userparams']) ? $data['userparams'] : '';
		unset($data['userparams']);

		if ($customDetails) {
			$params = array_merge($params, $customDetails);
		}

		$user->bind($data);

		if ($preference) {
			$user->setPreferences($preference);
		}

		if ($params) {
			$user->setParams($params);
		}

		$state = $user->save();

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_USER_HAS_BEEN_UPDATED', 'user', [
			'userLink' => 'index.php?option=com_payplans&view=user&layout=form&id=' . $user->getId(),
			'userName' => $user->getUsername()
		]);

		$this->info->set('COM_PP_USER_SAVED_SUCCESS', 'success');
		
		$task = $this->getTask();

		if ($task == 'apply') {
			$active = $this->input->get('activeTab', '');
			
			return $this->redirectToView('user', 'form', 'id=' . $user->getId() . '&activeTab=' . $active);
		}

		if ($task == 'save') {
			return $this->redirectToView('user');
		}
		
		return $this->redirectToView('user', 'form');
	}

	/**
	 * Applies a plan for a user
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function applyPlan()
	{
		$ids = $this->input->get('cid', '', 'default');
		$planId = $this->input->get('apply_plan_id', 0, 'int');

		if (!$planId) {
			$this->info->set('COM_PP_INVALID_PLAN', 'danger');
			return $this->redirectToView('user');
		}

		$plan = PP::plan($planId);
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$id = (int) $id;

			$user = PP::user($id);

			$order = $plan->subscribe($id);

			// Create an invoice for the order
			$invoice = $order->createInvoice($order->getSubscription());

			// Apply 100% discount
			$modifier = PP::modifier();

			$modifier->message =  'COM_PAYPLANS_APPLY_PLAN_ON_USER_MESSAGE';
			$modifier->invoice_id = $invoice->getId();
			$modifier->user_id = $invoice->getBuyer()->getId();
			$modifier->type = 'apply_plan';
			$modifier->amount = -100;
			$modifier->percentage = true;
			$modifier->frequency = PP_MODIFIER_FREQUENCY_ONE_TIME;
			$modifier->serial = PP_MODIFIER_FIXED_DISCOUNT;
			$modifier->save();

			$invoice->refresh()->save();

			// Create a transaction with 0 amount since the plan is applied by the admin
			$transaction = PP::transaction();
			$transaction->user_id = $invoice->getBuyer()->getId();
			$transaction->invoice_id = $invoice->getId();
			$transaction->amount = $invoice->getTotal();
			$transaction->message = 'COM_PAYPLANS_TRANSACTION_CREATED_FOR_APPLY_PLAN_TO_USER';
			$transaction->save();

			$actionlog->log('COM_PP_ACTIONLOGS_USER_ASSIGNED_PLANS', 'user', [
				'planTitle' => $plan->getTitle(),
				'planLink' => 'index.php?option=com_payplans&view=plan&layout=form&id=' . $plan->getId(),
				'userLink' => 'index.php?option=com_payplans&view=user&layout=form&id=' . $user->getId(),
				'userName' => $user->getUsername()
			]);
		}

		$message = 'COM_PP_SELECTED_PLAN_APPLIED_SUCCESS';

		$this->info->set($message, 'success');
		return $this->redirectToView('user');
	}

	/**
	 * Deletes a download request
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function deleteDownload()
	{
		$ids = $this->input->get('cid', [], 'array');
		$actionlog = PP::actionlog();

		if ($ids) {
			foreach ($ids as $id) {
				$table = PP::table('Download');
				$table->load($id);
				$table->delete();

				$user = PP::user($table->user_id);
				$actionlog->log('COM_PP_ACTIONLOGS_USER_DOWNLOAD_REQUEST_DELETED', 'user', [
					'userLink' => 'index.php?option=com_payplans&view=user&layout=form&id=' . $user->getId(),
					'userName' => $user->getUsername()
				]);
			}
		}

		$this->info->set('COM_PP_USER_DOWNLOAD_REQUEST_DELETED', 'success');
		
		$this->redirectToView('user', 'downloads');
	}

	/**
	 * Import user subscription via CSV
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function importCSV()
	{
		$planId = $this->input->get('plan', '', 'int');
		$limit = $this->input->get('limit', 50, 'int');
		
		$importSubscriptionStatus = $this->input->get('subscription_status', '', 'default');
		$importSubscriptionStartDate = $this->input->get('subscription_start_date', '', 'default');
		$importSubscriptionExpirationDate = $this->input->get('subscription_expiration_date', '', 'default');
		$importSubscriptionNote = $this->input->get('subscription_note', '', 'default');

		$file = $this->input->files->get('user_import_csv');

		$plan = PP::plan($planId);
		
		if (!$plan->getId()) {
			$this->info->set('COM_PP_INVALID_PLAN_ID', 'error');
			return $this->redirectToView('user', 'import');			
		}

		$csvData = PP::parseCSV($file['tmp_name'], false, false);

		// Skip this process if it doesn't return anything
		if (!$csvData) {
			$this->info->set('COM_PP_USER_IMPORT_INVALID_CSV_FILE', 'error');
			return $this->redirectToView('user', 'import');
		}

		$importCSVDirectory =  PP_MEDIA . '/import';
		$path = $importCSVDirectory . '/usersubscription.csv';

		if (!JFolder::exists($importCSVDirectory)) {
			JFolder::create($importCSVDirectory);
		} else {
			// Removed all files in the folder
			if (JFile::exists($path)) {
				JFile::delete($path);
			}
		}

		// Copy the file to tmp folder
		$state = JFile::copy($file['tmp_name'], $path);

		if (!$state) {
			return $this->view->exception('COM_PP_USER_IMPORT_INVALID_CSV_FILE', 'error');
		}

		return $this->view->call(__FUNCTION__, $planId, $csvData, $importSubscriptionStatus, $importSubscriptionStartDate, $importSubscriptionExpirationDate, $importSubscriptionNote);
	}	
}