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

class PayplansControllerLimitsubscription extends PayPlansController
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

			$actionlog->log('COM_PP_ACTIONLOGS_LIMITSUBSCRIPTION_DELETED', 'limitsubscription', array(
				'limitSubTitle' => $title
			));
		}

		$this->info->set('COM_PP_LIMITSUBSCRIPTION_DELETED_SUCCESS', 'success');
		return $this->redirectToView('limitsubscription');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=limitsubscription');
	}

	/**
	 * Saves limit subscription
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
			return $this->redirectToView('limitsubscription', 'form');
		}

		$app = PP::app($id);
		$app->bind($data);

		// Since we know this is for limitsubscription, we can set the type here
		$app->type = 'limitsubscription';
		$app->group = 'core';

		$coreParams = new JRegistry($data['core_params']);
		$app->setCoreParams($data['core_params']);

		$appParams = new JRegistry($data['app_params']);
		$app->setAppParams($data['app_params']);

		$state = $app->save();

		// Newly created app
		$message = 'COM_PP_LIMITSUBSCRIPTION_CREATED_SUCCESS';
		$actionString = 'COM_PP_ACTIONLOGS_LIMITSUBSCRIPTION_CREATED';

		if ($state === false) {
			$this->info->set('COM_PP_LIMITSUBSCRIPTION_SAVED_FAILED', 'danger');

			return $this->redirectToView('limitsubscription', 'form');
		}

		// Edit existing app
		if ($id) {
			$message = 'COM_PP_LIMITSUBSCRIPTION_EDIT_SUCCESS';
			$actionString = 'COM_PP_ACTIONLOGS_LIMITSUBSCRIPTION_UPDATED';
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'limitsubscription', array(
				'limitSubTitle' => $app->getTitle(),
				'limitSubLink' => 'index.php?option=com_payplans&view=limitsubscription&layout=form&id=' . $app->getId()
		));

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->redirectToView('limitsubscription', 'form', 'id=' . $app->getId());
		}

		if ($task == 'saveNew') {
			return $this->redirectToView('limitsubscription', 'form');
		}

		return $this->redirectToView('limitsubscription');
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
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_LIMITSUBSCRIPTION_PUBLISHED' : 'COM_PP_ACTIONLOGS_LIMITSUBSCRIPTION_UNPUBLISHED';

		foreach ($ids as $id) {
			$table = PP::table('App');
			$table->load($id);

			$table->$task();

			$actionlog->log($actionString, 'limitsubscription', array(
					'limitSubTitle' => $table->title,
					'limitSubLink' => 'index.php?option=com_payplans&view=limitsubscription&layout=form&id=' . $table->app_id
			));
		}

		$message = $task == 'publish' ? 'COM_PP_ITEM_PUBLISHED_SUCCESSFULLY' : 'COM_PP_ITEM_UNPUBLISHED_SUCCESSFULLY';

		$this->info->set($message, 'success');
		return $this->redirectToView('limitsubscription');
	}
}