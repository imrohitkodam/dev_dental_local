<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

class EDocmanModelDocument extends EDocmanModelCommonDocument
{

	public function __construct(array $config = array())
	{
		parent::__construct($config);
		$this->state->insert('catid', 'int', 0)->insert('tmpl', 'cmd', '');
	}

	/**
	 * Override loadData method to calculate number_created_days
	 */
	public function loadData()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$now   = JFactory::getDate()->toSql();
		$query->select(array('*', "DATEDIFF('$now', created_time) AS number_created_days", "DATEDIFF('$now', modified_time) AS number_updated_days"))
			->from($this->table)
			->where('id = ' . (int) $this->state->id);
		$db->setQuery($query);

		$this->data = $db->loadObject();
	}

	/**
	 * Increase hits for document
	 */
	function hitCounter()
	{
		$session      = JFactory::getSession();
		$viewedDocIds = $session->get('viewed_doc_ids', '');
		if ($viewedDocIds)
		{
			$viewedDocIds = explode(',', $viewedDocIds);
		}
		else
		{
			$viewedDocIds = array();
		}
		if (!in_array($this->data->id, $viewedDocIds))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->update('#__edocman_documents')
				->set('hits = hits + 1')
				->where('id=' . $this->data->id);
			$db->setQuery($query);
			$db->execute();
			$viewedDocIds[] = $this->data->id;
		}
		$viewedDocIds = implode(',', $viewedDocIds);
		$session->set('viewed_doc_ids', $viewedDocIds);
	}

	/**
	 * Store downloader information to database
	 *
	 * @param $documentId
	 * @param $data
	 *
	 * @return string
	 */
	function storeDownload($documentId, $data)
	{
		jimport('joomla.environment.browser');
		jimport('joomla.user.helper');

		$config             = EDocmanHelper::getConfig();
		$session = JFactory::getSession();
		if($config->onetime_collect){
			$name = $session->get('name');
			$email = $session->get('email');
			if($data['name'] == ""){
				$data['name'] = $session->get('name');
			}else{
				$session->set('name',$data['name']);
			}
			if($data['email'] == ""){
				$data['email'] = $session->get('email');
			}else{
				$session->set('email',$data['email']);
			}
		}

		$db  = JFactory::getDbo();
		$sql = 'SELECT * FROM #__edocman_documents WHERE id=' . $documentId;
		$db->setQuery($sql);
		$table = $db->loadObject();
		$sql   = 'UPDATE #__edocman_documents SET downloads=downloads + 1 WHERE id=' . $documentId;
		$db->setQuery($sql);
		$db->execute();

		
		$browser            = JBrowser::getInstance();
		$row                = $this->getTable('statistic', 'EDocmanTable');
		$row->document_id   = $documentId;
		$row->name          = $data['name'];
		$row->email         = $data['email'];
		$row->user_id       = 0;
		$row->user_ip       = @$_SERVER['REMOTE_ADDR'];
		$row->download_time = gmdate('Y-m-d H:i:s');
		$row->browser       = $browser->getBrowser();
		$row->os            = $browser->getPlatform();
		$row->download_code = JUserHelper::genRandomPassword(20);
		$row->store();
		// Send notification email to admin
		$fromName           = JFactory::getConfig()->get('fromname');
		$fromEmail          = JFactory::getConfig()->get('mailfrom');
		$subject            = $config->download_email_subject;
		$body               = nl2br($config->download_email_body);
		$username           = $data['name'];
		$name               = $data['name'];
		$email              = $data['email'];
		$userIp             = @$_SERVER['REMOTE_ADDR'];
		$documentTitle      = $table->title;
		$body               = str_replace('[USERNAME]', $username, $body);
		$body               = str_replace('[NAME]', $name, $body);
		$body               = str_replace('[USER_IP]', $userIp, $body);
		$body               = str_replace('[DOCUMENT_TITLE]', $documentTitle, $body);
		$body               = str_replace('[EMAIL]', $email, $body);
		$notificationEmails = trim($config->notification_emails);
		if (strlen($notificationEmails) < 5)
		{
			$notificationEmails = $fromEmail;
		}
		$notificationEmails = explode(',', $notificationEmails);
		$mailer             = JFactory::getMailer();
		$send_notify		= $session->get('send_notify',1);
		for ($i = 0, $n = count($notificationEmails); $i < $n; $i++)
		{
			$email = trim($notificationEmails[$i]);
			if ($email && $send_notify == 1)
			{
				$mailer->sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
				$mailer->ClearAllRecipients();
			}
		}
		$downloadUrl  = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . JRoute::_('index.php?option=com_edocman&task=document.download&id=' . $documentId . '&download_code=' . $row->download_code, false);
		$downloadLink = '<a href="' . $downloadUrl . '">' . JText::_('EDOCMAN_CLICK_TO_DOWNLOAD') . '</a>';
		// Send download link to user
		if ($config->download_type == 1)
		{
			$subject = $config->download_link_email_subject;
			$body    = nl2br($config->download_link_email_body);
			$subject = str_replace('[DOCUMENT_TITLE]', $documentTitle, $subject);
			$body    = str_replace('[USERNAME]', $username, $body);
			$body    = str_replace('[NAME]', $name, $body);
			$body    = str_replace('[USER_IP]', $userIp, $body);
			$body    = str_replace('[DOCUMENT_TITLE]', $documentTitle, $body);
			$body    = str_replace('[EMAIL]', $row->email, $body);
			$body    = str_replace('[DOWNLOAD_LINK]', $downloadLink, $body);
			$mailer->ClearAllRecipients();
			$mailer->sendMail($fromEmail, $fromName, $row->email, $subject, $body, 1);
		}

		// Return the message which will be displayed to end user
		if ($config->download_type == 1)
		{
			$message = $config->download_complete_message_send_download_link;
		}
		else
		{
			$message = $config->download_complete_message;
			$message .= '
				<iframe src="' . $downloadUrl . '" style="display:none;" id="download_frame"></iframe>
			';
		}

		$message = str_replace('[USERNAME]', $username, $message);
		$message = str_replace('[NAME]', $name, $message);
		$message = str_replace('[USER_IP]', $userIp, $message);
		$message = str_replace('[DOCUMENT_TITLE]', $documentTitle, $message);
		$message = str_replace('[EMAIL]', $row->email, $message);
		$message = str_replace('[DOWNLOAD_LINK]', $downloadLink, $message);

		return nl2br($message);
	}
}