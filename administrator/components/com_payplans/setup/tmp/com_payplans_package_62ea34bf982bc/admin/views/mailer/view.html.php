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

class PayPlansViewMailer extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('mailer');
	}

	public function display($tpl = null)
	{
		$this->heading('Emails');
		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/emails/email-templates');

		JToolbarHelper::deleteList(JText::_('COM_PP_PLAN_DELETE_EMAILS_CONFIRMATION'), 'mailer.delete', JText::_('COM_PP_RESET_DEFAULT'));
		
		$model = PP::model('Emails');
		$files = $model->getFiles();

		$this->set('files', $files);

		return parent::display('mailer/default/default');
	}

	/**
	 * Renders the template file editing form
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function edit($tpl = null)
	{
		$this->heading('Notification Templates');
		$this->hideSidebar();
		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/emails/email-templates');

		JToolbarHelper::apply('mailer.apply');
		JToolbarHelper::save('mailer.save');
		JToolbarHelper::cancel('mailer.cancel');

		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);

		$model = PP::model('Emails');
		$absolutePath = $model->getFolder() . $file;

		$file = $model->getTemplate($absolutePath, true);

		// Always use codemirror
		$editor = PPCompat::getEditor('codemirror');

		$this->set('editor', $editor);
		$this->set('data', $file);

		return parent::display('mailer/edit/default');
	}

	/**
	 * Previews an e-mail template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function preview()
	{
		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);

		$model = PP::model('Emails');
		$file = $model->getFolder() . $file;

		$template = $model->getTemplate($file);

		$namespace = 'emails' . $template->relative;
		$namespace = str_ireplace('.php', '', $namespace);
		
		$mailer = PP::mailer();
		$preview = $mailer->getPreview($namespace);

		echo $preview;exit;
	}
}