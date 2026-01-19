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

class PayplansControllerNotifications extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('notifications');
		
		$this->registerTask('saveFile', 'storeFile');
		$this->registerTask('applyFile', 'storeFile');
		$this->registerTask('save', 'store');
		$this->registerTask('apply', 'store');
		
		$this->registerTask('remove', 'delete');
	}

	/**
	 * Allows remote caller to delete an app
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', [], 'int');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$id = (int) $id;

			$app = PP::app($id);

			$title = $app->getTitle();

			if (!$app->canDelete()) {
				$this->info->set('COM_PP_NOTIFICATION_RULE_UNABLE_TO_DELETE', 'danger');
				return $this->redirectToView('notifications');
			}

			$actionlog->log('COM_PP_ACTIONLOGS_NOTIFICATIONS_DELETED', 'notifications', [
				'notificationTitle' => $title
			]);

			$app->delete();
		}

		$this->info->set('COM_PP_NOTIFICATION_RULE_DELETED_SUCCESSFULLY', 'success');

		return $this->redirectToView('notifications');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->redirectToView('notifications');
	}

	/**
	 * Saves a payment gateway
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$id = $this->input->get('id', 0, 'int');
		$data = $this->input->post->getArray();
		$coreParams = $this->input->get('core_params', [], 'array');
		$appParams = $this->input->get('app_params', [], 'array');

		// Checking of Pre expiry, post expiry , post activation and card abondonment time can not be life time
		if ($appParams['on_preexpiry'] === '000000000000' || $appParams['on_preexpiry_trial'] === '000000000000' || $appParams['on_postexpiry'] === '000000000000' || $appParams['on_postactivation'] === '000000000000' || $appParams['on_cart_abondonment'] === '000000000000') {

			$errorMessage = 'COM_PP_NOTIFICATION_'.strtoupper($appParams['when_to_email']).'_TIME_NEVER_LIFETIME';
			$this->info->set($errorMessage, 'danger');

			if ($id) {
				return $this->redirectToView('notifications', 'form', 'id=' . $id);
			}

			return $this->redirectToView('notifications', 'create');
		}

		if ($id) {
			$app = PP::app()->getAppInstance($id);

			$data['core_params'] = $app->collectCoreParams($data);
			$data['app_params']  = $app->collectAppParams($data);

			$actionString = 'COM_PP_ACTIONLOGS_NOTIFICATIONS_UPDATED';
		} else {
			$app = PP::app();
			$actionString = 'COM_PP_ACTIONLOGS_NOTIFICATIONS_CREATED';
		}

		// All notifications should have the group and type of "email"
		$data['group'] = 'email';
		$data['type'] = 'email';

		$app->bind($data);

		// Encode html contents
		$content = PP::normalize($appParams, 'content', '');

		if ($content) {
			$appParams['content'] = base64_encode($content);
		}

		$app->setCoreParams($coreParams);
		$app->setAppParams($appParams);

		try {
			$app->save();
		} catch (Exception $e) {
			$this->info->set($e->getMessage(), 'danger');

			return $this->redirectToView('notifications', 'form', 'id=' . $app->getId());
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'notifications', [
			'notificationTitle' => $app->getTitle(),
			'notificationLink' => 'index.php?option=com_payplans&view=notifications&layout=form&id=' . $app->getId()
		]);

		$this->info->set('COM_PP_NOTIFICATION_RULE_UPDATED_SUCCESSFULLY', 'success');

		if ($this->getTask() === 'apply') {
			return $this->redirectToView('notifications', 'form', 'id=' . $app->getId());
		}
		return $this->redirectToView('notifications');
	}

	/**
	 * Saves a new file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function storeFile()
	{
		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);
		$file = ltrim($file, '/');

		$source = $this->input->get('source', '', 'raw');

		$model = PP::model('Notifications');

		$path = $model->getOverridePath();

		if (!JFolder::exists($path)) {
			JFolder::create($path);
		}

		$storagePath = $path . '/' . $file;

		$state = JFile::write($storagePath, $source);

		$error = false;
		$message = 'COM_PP_NOTIFICATION_TEMPLATE_FILE_SAVED_SUCCESSFULLY';

		if (!$state) {
			$message = 'COM_PP_NOTIFICATION_TEMPLATE_FILE_NOT_SAVED_SUCCESSFULLY';
			$error = true;
		}

		$this->info->set($message, $error ? 'danger' : 'success');

		$task = $this->getTask();

		if ($task === 'applyFile') {
			return $this->redirectToView('notifications', 'editFile', 'file=' . urlencode($file));
		}

		return $this->redirectToView('notifications', 'templates');
	}
}
