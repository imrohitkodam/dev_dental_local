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

class PPMailer extends PayPlans
{
	private $preview = false;

	/**
	 * Sends e-mail out using the mailer in Joomla
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function send($recipient, $subject, $namespace, $params = [], $attachments = [], $cc = [], $bcc = [], $html = true, $userLanguage = "")
	{
		$subject = JText::_($subject);
		$contents = $this->getContents($namespace, $params);


		$jconfig = PP::jconfig();
		$fromName = $jconfig->get('fromname');

		// GEt site email from payplans settings
		$from = $this->config->get('mail_from_email', '');
		if(!$from) {
			$from = $jconfig->get('mailfrom');
		}

		$replyTo = $jconfig->get('replyto', $from);

		$mailer = JFactory::getMailer();
		$mailer->setSubject($subject);
		$mailer->setSender(array($from, $fromName));
		$mailer->addReplyTo($replyTo, $fromName);
		$mailer->setBody($contents);
		$mailer->IsHTML($html);

		// set the user prefered language
		if ($userLanguage) {
			$mailer->setLanguage($userLanguage);
		}

		// Carbon Copy (CC)
		if (is_array($cc) && $cc) {
			foreach ($cc as $address) {
				$mailer->addCC($address);
			}
		}

		// Blind Carbon Copy (BCC)
		if (is_array($bcc) && $bcc) {
			foreach ($bcc as $address) {
				$mailer->addBCC($address);
			}
		}

		// Insert attachments
		if ($attachments) {
			foreach ($attachments as $attachment) {
				$mailer->addAttachment($attachment);
			}
		} 
			
		$mailer->addRecipient($recipient);
		$state = $mailer->send();

		return $state;
	}

	/**
	 * Retrieves the list of site admin e-mails
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAdminEmails()
	{
		static $emails = null;

		if (is_null($emails)) {
			$model = PP::model('User');
			$admins = $model->getSiteAdmins(true);
			
			$emails = [];

			if ($admins) {
				foreach ($admins as $admin) {
					$emails[] = $admin->getEmail();
				}
			}
		}

		return $emails;
	}

	/**
	 * Get contents of e-mail template
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getContents($namespace, $vars = [])
	{
		$path = $this->resolve($namespace);

		extract($vars);

		ob_start();
		include($path);
		$contents = ob_get_contents();
		ob_end_clean();

		// If there is an intro set, we'll get the introtext
		$intro = PP::normalize($vars, 'intro', '');
		$outerFrame = PP::normalize($vars, 'outerFrame', 1);

		$theme = PP::themes();
		$theme->set('intro', $intro);
		$theme->set('outerFrame', $outerFrame);
		$theme->set('contents', $contents);
		$output = $theme->output('site/emails/template');

		return $output;
	}

	/**
	 * Generates the preview of an e-mail template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getPreview($namespace)
	{
		$this->preview = true;
			
		$path = $this->resolve($namespace);

		ob_start();
		include($path);
		$contents = ob_get_contents();
		ob_end_clean();

		// If there is an intro set, we'll get the introtext
		$vars = [];
		$intro = PP::normalize($vars, 'intro', '');
		$outerFrame = PP::normalize($vars, 'outerFrame', 1);

		$theme = PP::themes();
		$theme->set('intro', $intro);
		$theme->set('outerFrame', $outerFrame);
		$theme->set('contents', $contents);
		$output = $theme->output('site/emails/template');

		return $output;
	}

	/**
	 * Determines if the current rendering is preview
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isPreview()
	{
		return $this->preview;
	}

	/**
	 * Internal method to render the header of an e-mail template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function renderButton($link, $text)
	{
		$theme = PP::themes();
		$theme->set('link', $link);
		$theme->set('text', $text);

		$output = $theme->output('site/emails/structure/button');

		return $output;
	}

	/**
	 * Internal method to render the header of an e-mail template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function renderHeader($title, $subtitle = '')
	{
		$theme = PP::themes();
		$theme->set('title', $title);
		$theme->set('subtitle', $subtitle);

		$output = $theme->output('site/emails/structure/header');

		return $output;
	}

	/**
	 * Resolves the namespace
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function resolve($namespace, $extension = 'php')
	{
		$parts = explode('/', $namespace);

		if (count($parts) == 1) {
			return $parts;
		}

		$allowed = [
			'plugins',
			'emails'
		];

		if (!in_array($parts[0], $allowed)) {
			return $namespace;
		}

		$path = JPATH_ROOT;

		// Check for template overrides
		$currentTemplate = PP::getJoomlaTemplate();
		$overridePath = JPATH_ROOT . '/templates/' . $currentTemplate . '/html/com_payplans';

		// Plugin theme files resolver
		if ($parts[0] == 'plugins') {

			// Get the group and element of the plugin
			$path .= '/plugins/' . $parts[1] . '/' . $parts[2];

			unset($parts[0], $parts[1], $parts[2]);
			
			$path .= '/tmpl/' . implode('/', $parts) . '.' . $extension;
		}

		// Default theme name
		$defaultThemeName = 'wireframe';

		// Emails
		if ($parts[0] == 'emails') {

			$fileName = implode('/', $parts) . '.' . $extension;

			$overrideFilePath = $overridePath . '/' . $fileName;
			$overrideExists = JFile::exists($overrideFilePath);

			$path = JPATH_ROOT . '/components/com_payplans/themes/' . $defaultThemeName . '/' . $fileName;

			if ($overrideExists) {
				$path = $overrideFilePath;
			}
		}

		return $path;
	}
}