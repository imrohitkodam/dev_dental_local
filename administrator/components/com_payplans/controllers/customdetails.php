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

class PayplansControllerCustomDetails extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('save', 'store');
		$this->registerTask('savenew', 'store');
		$this->registerTask('apply', 'store');

		$this->registerTask('close', 'cancel');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Deletes subscription custom details
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', [], 'array');
		$type = '';
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$table = PP::table('CustomDetails');
			$table->load((int) $id);

			$type = $table->type;
			$title = $table->title;
			$table->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_CUSTOMDETAILS_DELETED_' . strtoupper($type), 'customdetails', [
				'customDetailsName' => $title
			]);
		}

		$this->info->set('COM_PP_CUSTOM_DETAILS_DELETED_SUCCESSFULLY', 'success');

		return $this->redirectToView($type, 'customdetails');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=user&layout=customdetails');
	}

	/**
	 * Saves the custom details for users
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$table = PP::table('CustomDetails');
		$data = $this->input->post->getArray();

		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', 'user', 'string');

		$table->load($id);
		$table->bind($data);

		if (!$table->title) {
			$this->info->set('COM_PP_CUSTOM_DETAILS_TITLE_REQUIRED', 'danger');
			return $this->redirectToView($type, 'customdetailsform');
		}
		$table->data = $this->input->get('data', '', 'raw');

		// To avoid any issues with escaping, we'll ensure that the data is properly encoded
		$table->data = base64_encode($table->data);

		$params = new JRegistry($this->input->get('params', [], 'raw'));
		$table->params = $params->toString();

		$actionString = JText::_('COM_PP_ACTIONLOGS_CUSTOMDETAILS_UPDATED_' . strtoupper($type));
		
		if (!$id) {
			$actionString = JText::_('COM_PP_ACTIONLOGS_CUSTOMDETAILS_CREATED_' . strtoupper($type));
			$table->created = PP::date()->toSql();
		}

		$table->store();

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'customdetails', [
			'customDetailsName' => $table->title,
			'customDetailsLink'	=> 'index.php?option=com_payplans&view=' . $type . '&layout=customdetailsform&id=' . $table->id		
		]);

		$task = $this->getTask();

		$this->info->set('COM_PP_CUSTOM_DETAILS_SAVED_SUCCESSFULLY', 'success');
		
		if ($task === 'save') {
			return $this->redirectToView($type, 'customdetails');
		}

		if ($task === 'saveNew') {
			return $this->redirectToView($type, 'customdetailsform');
		}

		return $this->redirectToView($type, 'customdetailsform', 'id=' . $table->id);
	}

	/**
	 * Publishes customdetails
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function togglePublish()
	{
		$ids = $this->input->get('cid', 0, 'int');
		$task = $this->getTask();
		$state = $task == 'publish' ? 1 : 0;
		$actionlog = PP::actionlog();
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_CUSTOMDETAILS_PUBLISHED_' : 'COM_PP_ACTIONLOGS_CUSTOMDETAILS_UNPUBLISHED_' ;

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = PP::table('CustomDetails');
			$table->load($id);
			$table->published = $state;
			$table->store();

			$actionlog->log($actionString . $table->type, 'customdetails', [
				'customDetailsName' => $table->title,
				'customDetailsLink'	=> 'index.php?option=com_payplans&view=' . $table->type . '&layout=customdetailsform&id=' . $table->id		
			]);
		}

		$message = JText::_('COM_PP_CUSTOM_DETAILS_UNPUBLISHED_SUCCESSFULLY');

		if ($task === 'publish') {
			$message = JText::_('COM_PP_CUSTOM_DETAILS_PUBLISHED_SUCCESSFULLY');
		}

		$this->info->set($message, 'success');
		
		$this->redirectReturnUrl();
		$this->redirectToView('user', 'customdetails');
	}
}