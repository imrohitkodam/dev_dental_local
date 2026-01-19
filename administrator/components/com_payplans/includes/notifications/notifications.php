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

class PPNotifications extends PayPlans
{
	private $table = null;

	public static function factory($appId)
	{
		return new self($appId);
	}

	public function __construct($appId)
	{
		$this->table = PP::table('App');
		$this->table->load($appId);

		$this->params = PP::registry($this->table->app_params);
	}

	/**
	 * Get the list of attachments that should be included in the email
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getAttachments()
	{
		$attachment = $this->params->get('attachment', '');
		
		if (!$attachment || $attachment == -1) {
			return array();
		}

		$model = PP::model('Notifications');
		$path = $model->getAttachmentsFolder() . '/'. $attachment;

		$attachments = [$path];
		return $attachments;
	}

	/**
	 * Get the list of carbon copy (CC emails)
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getCC()
	{
		$emails = $this->params->get('send_cc', '');

		if ($emails) {
			$emails = explode(',', $emails);
		}

		return $emails;
	}

	/**
	 * Get the list of blind carbon copy (BCC emails)
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getBCC()
	{
		$emails = $this->params->get('send_bcc', '');

		if ($emails) {
			$emails = explode(',', $emails);
		}

		return $emails;
	}

	/**
	 * Retrieve the invoice
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getPdfInvoice($object)
	{
		$invoice = $object;

		if ($object instanceof PPOrder) {
			$invoices = $object->getInvoices();
			$invoice = array_pop($invoices);
		}
		
		//get the invoice object from subscription
		if ($object instanceof PPSubscription) {
			$order = $object->getOrder();
			$invoices = $order->getInvoices();
			$invoice = array_pop($invoices);
		}
		
		// If we can't get the invoice, skip this altogether
		if (!($invoice instanceof PPInvoice)) {
			return false;
		}

		// Get the path to the pdf invoice file
		$pdf = PP::pdf($invoice);
		$pdf->generateFile();

		return $pdf;
	}

	/**
	 * Retrieves the subject used for e-mail
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getSubject($object)
	{
		// Retrieve the subject value from the app table
		$subject = $this->params->get('subject', '');

		// Translate subject 
		$subject = JText::_($subject);

		// Rewrite tokens on the subject level
		$subject = $this->replaceTokens($subject, $object);

		return $subject;
	}

	/**
	 * Retrieves the subject used for e-mail
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getEmailContents($object)
	{
		$useArticle = $this->isUsingJoomlaArticle();
		$useCustomContent = $this->isUsingCustomContent();

		// Process contents for article or custom contents
		if ($useCustomContent || $useArticle) {

			// Retrieve custom content source
			$contents = $this->params->get('content', '');
			$contents = base64_decode($contents);

			// Retrieve Joomla article content source
			if ($useArticle) {

				$articleId = $this->params->get('choose_joomla_article');

				if ($articleId) {

					$article = JTable::getInstance('Content');
					$state = $article->load($articleId);

					// Determine if the site enable multilingual language
					if ($state && JLanguageAssociations::isEnabled()) {

						// Retrieve all the association article data e.g. English, French and etc
						$termsAssociated = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);

						// Determine the current site language
						$currentLang = JFactory::getLanguage()->getTag();

						// Only come inside this checking if the current site language not match with the selected article language
						// And see whether this tearmAssociated got detect got other association article or not
						if (isset($termsAssociated) && $currentLang !== $article->language && array_key_exists($currentLang, $termsAssociated)) {

							foreach ($termsAssociated as $term) {

								// Retrieve the associated article id
								if ($term->language == $currentLang) {
									$articleId = explode(':', $term->id);
									$articleId = $articleId[0];
									break;
								}
							}
						}

						// Reload the new associated article id
						$state = $article->load($articleId);
					}

					// Only assign the Joomla article content here if the article exist
					if ($state) {
						$contents = $article->introtext . $article->fulltext;
					}
				}
			}

			// Replace the token to proper value
			$contents = $this->replaceTokens($contents, $object);

			return $contents;
		}

		// Process template files
		$model = PP::model('Notifications');

		$templateFile = $this->params->get('choose_template');

		// Default path
		$path = $model->getFolder() . '/' . $templateFile . '.php';

		$overrideExists = $this->isOverrideExists($templateFile);

		if ($overrideExists) {
			$overridePath = $this->getOverridePath($templateFile);
			$path = $overridePath;
		}

		// Ensure that the final file really exists
		$exists = JFile::exists($path);

		if (!$exists) {
			return false;
		}

		ob_start();
		include($path);
		$contents = ob_get_contents();
		ob_end_clean();

		$contents = $this->replaceTokens($contents, $object);

		return $contents;
	}

	/**
	 * Retrieves the e-mail template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getEmailTemplate($contents, $intro = '')
	{
		$theme = PP::themes();
		$theme->set('intro', '');
		$theme->set('contents', $contents);
		$output = $theme->output('site/emails/template');

		return $output;
	}

	/**
	 * Retrieves the override path for a given template file
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getOverridePath($file)
	{
		$model = PP::model('Notifications');

		// Determine if the email template file has already been overriden.
		$path = $model->getOverridePath($file . '.php');

		return $path;
	}

	/**
	 * Determines if we should be using a template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isUsingCustomContent()
	{
		$template = $this->params->get('email_template', 'custom') == 'custom' ? true : false;

		return $template;
	}

	/**
	 * Determines if we should be use Joomla article
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isUsingJoomlaArticle()
	{
		$template = $this->params->get('email_template', 'custom') == 'choose_joomla_article' ? true : false;

		return $template;
	}

	/**
	 * Determines if the template contents should be html
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isUsingHtml()
	{
		$html = $this->params->get('html_format', true) ? true : false;

		return $html;
	}

	/**
	 * Determines if the template override exists
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isOverrideExists($file)
	{
		// Determine if the email template file has already been overriden.
		$path = $this->getOverridePath($file);
		$exists = JFile::exists($path);

		return $exists;
	}

	/**
	 * Previews a notification template
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function preview()
	{
		$contents = $this->getEmailContents('');

		// Simulate how this works when it is really sending so that they both look exactly the same
		$theme = PP::themes();
		$theme->set('intro', '');
		$theme->set('contents', $contents);
		$output = $theme->output('site/emails/template');

		return $output;
	}

	/**
	 * Replace tokens with the proper values
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function replaceTokens($content, $object)
	{
		$rewriter = PP::rewriter();
		$result = $rewriter->rewrite($content, $object);

		return $result;
	}

	/**
	 * Sends out e-mail. Object could be a subscription or an invoice
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function send($object)
	{
		$user = $object->getBuyer(true);

		// Retrieve the language tag from the user
		$userLang = PP::getUserLanguage($user);

		// Load user's preferred language file.
		$language = JFactory::getLanguage();
		$language->load(PP_ID, PP_JOOMLA, $userLang, true);

		$subject = $this->getSubject($object);
		$recipient = $user->getEmail();
		$cc = $this->getCC();
		$bcc = $this->getBCC();

		$attachments = $this->getAttachments();
		$sendInvoice = $this->shouldIncludeInvoice();
		$pdfInvoice = false;

		if ($sendInvoice) {
			$pdfInvoice = $this->getPdfInvoice($object);

			if ($pdfInvoice !== false) {
				$attachments = array_merge($attachments, [$pdfInvoice->getFilePath()]);
			}
		}
		
		$contents = $this->getEmailContents($object);

		$html = $this->isUsingHtml();

		if (!$html) {
			$contents = strip_tags($contents);
		}

		// Try to send
		$mailer = PP::mailer();
		$state = $mailer->send($recipient, $subject, 'emails/custom/blank', ['contents' => $contents], $attachments, $cc, $bcc, true, $userLang);

		// For logging purposes
		$log = [
			'user_id' => $user->getId(),
			'subject' => $subject,
			'body' => $contents
		];

		// We should delete the file to save space.
		// If the user need to retrieve the file in the future,
		// he can just go to the dashboard and download it.
		if ($pdfInvoice) {
			$pdfInvoice->delete();
		}

		if ($state == false || $state instanceof Exception) {
			PPLog::log(PPLogger::LEVEL_INFO, JText::_('COM_PAYPLANS_EMAIL_SENDING_FAILED'), $this, $log, 'PayplansAppEmailFormatter', '', true);
			return false;
		}

		// Otherwise we assume sending was success
		PPLog::log(PPLogger::LEVEL_INFO, JText::_('COM_PAYPLANS_EMAIL_SEND_SUCCESSFULLY'), $this, $log, 'PayplansAppEmailFormatter');
		return true;
	}

	/**
	 * Determines if we should include invoice in the attachments
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function shouldIncludeInvoice()
	{
		$send = (bool) $this->params->get('send_invoice', false);
		$config = PP::config();
		$enabled = $config->get('enable_pdf_invoice');

		if ($send && $enabled) {
			return true;
		}

		return false;
	}
}