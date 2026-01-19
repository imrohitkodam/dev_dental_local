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

class PayplansControllerMailer extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('mailer');
		
		$this->registerTask('save', 'store');
		$this->registerTask('apply', 'store');
	}

	/**
	 * Resets a list of email template files to it's original state
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function delete()
	{
		$files = $this->input->get('cid', [], 'default');

		if (!$files) {
			return $this->redirectToView('mailer');
		}

		$model = PP::model("Emails");

		foreach ($files as $file) {

			$file = base64_decode($file);
			$path = $model->getOverrideFolder($file);

			JFile::delete($path);
		}

		// Get the current editor
		$this->info->set('COM_PP_EMAIL_RESET_TO_DEFAULT_SUCCESS', 'success');

		return $this->redirectToView('mailer');
	}

	/**
	 * Saves an email template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function store()
	{
		// Get the contents of the email template
		$contents = $this->input->get('source', '', 'raw');
		
		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);

		$model = PP::model('Emails');
		$path = $model->getOverrideFolder($file);

		JFile::write($path, $contents);

		$this->info->set('COM_PP_EMAILS_TEMPLATE_FILE_SAVED_SUCCESSFULLY', 'success');

		return $this->redirectToView('mailer');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->redirectToView('mailer');
	}
}
