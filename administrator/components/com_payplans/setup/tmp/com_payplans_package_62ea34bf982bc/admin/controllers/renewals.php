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

class PayplansControllerRenewals extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('plans');

		$this->registerTask('save', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('apply', 'save');

		$this->registerTask('close', 'cancel');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
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
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$app = PP::app((int) $id);
			$title = $app->getTitle();
			$app->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_RENEWALS_DELETED', 'renewals', [
				'renewalTitle' => $title
			]);
		}

		$this->info->set('COM_PP_RENEWAL_DELETED_SUCCESS', 'success');
		return $this->redirectToView('renewals');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=renewals');
	}

	/**
	 * Saves a subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('app_id', 0, 'int');
		$data = $this->input->post->getArray();

		if (empty($data['title'])) {
			$this->info->set('COM_PP_TITLE_REQUIRED', 'danger');
			return $this->redirectToView('renewals', 'form');
		}

		$app = PP::app($id);
		$app->bind($data);

		// Since we know this is for upgrades, we can set the type here
		$app->type = 'renewal';
		$app->type = 'core';

		$coreParams = new JRegistry($data['core_params']);
		$app->setCoreParams($data['core_params']);

		// No need to save app params as it's core app
		//$appParams = new JRegistry($data['app_params']);
		//$app->setAppParams($data['app_params']);

		$state = $app->save();

		$message = 'COM_PP_RENEWAL_CREATED_SUCCESS';
		$actionString = 'COM_PP_ACTIONLOGS_RENEWALS_CREATED';

		if ($state === false) {
			$this->info->set('COM_PP_RENEWAL_SAVED_FAILED', 'danger');

			return $this->redirectToView('renewals', 'form');
		}

		if ($id) {
			$message = 'COM_PP_RENEWAL_SAVED_SUCCESS';
			$actionString = 'COM_PP_ACTIONLOGS_RENEWALS_UPDATED';
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'renewals', [
			'renewalTitle' => $app->getTitle(),
			'renewalLink' => 'index.php?option=com_payplans&view=renewals&layout=form&id=' . $app->getId()
		]);

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->redirectToView('renewals', 'form', 'id=' . $app->getId());
		}

		if ($task == 'saveNew') {
			return $this->redirectToView('renewals', 'form');
		}
		return $this->redirectToView('renewals');
	}


	/**
	 * Allow caller to toggle published state
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function togglePublish()
	{
		$ids = $this->input->get('cid', 0, 'int');

		$task = $this->getTask();

		$actionlog = PP::actionlog();
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_RENEWALS_PUBLISHED' : 'COM_PP_ACTIONLOGS_RENEWALS_UNPUBLISHED';

		foreach ($ids as $id) {
			$table = PP::table('App');
			$table->load($id);

			$table->$task();

			$actionlog->log($actionString, 'renewals', [
				'renewalTitle' => $table->title,
				'renewalLink' => 'index.php?option=com_payplans&view=renewals&layout=form&id=' . $table->app_id
			]);
		}

		$message = $task == 'publish' ? 'COM_PP_ITEM_PUBLISHED_SUCCESSFULLY' : 'COM_PP_ITEM_UNPUBLISHED_SUCCESSFULLY';

		$this->info->set($message, 'success');
		return $this->redirectToView('renewals');
	}

}
