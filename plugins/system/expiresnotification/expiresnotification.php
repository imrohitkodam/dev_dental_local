<?php
/**
 * @version               1.11.6
 * @package               Joomla
 * @subpackage            Edocman
 * @author                Tuan Pham Ngoc
 * @copyright             Copyright (C) 2011 - 2019 Ossolution Team
 * @license               GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class plgSystemExpiresnotification extends JPlugin
{
	public function onAfterRender()
	{
	    require_once JPATH_ROOT.'/components/com_edocman/helper/helper.php';
        $document_expires_notification = $this->params->get('document_expires_notification',1);
        if($document_expires_notification == 1)
        {
            $config                 = EDocmanHelper::getConfig();
            $notification_emails    = $config->notification_emails;
            $mailer                 = JFactory::getMailer();
            $config                 = JFactory::getConfig();
            $fromEmail              = $config->get('mailfrom');
            $fromName               = $config->get('fromname');
            $ndays                  = $this->params->get('ndays', 2);
            $subject                = $this->params->get('expires_email_subject', '');
            $body                   = $this->params->get('expires_email_body','');
            if($subject != "" && $body != "")
            {
                $db                 = JFactory::getDbo();
                $query              = $db->getQuery(true);
                $now                = JFactory::getDate()->toSql();
                $query->select(array('*', "DATEDIFF(publish_down, '$now') AS number_published"))
                    ->from('#__edocman_documents')
                    ->where('published = 1')
                    ->where('publish_down <> ""')
                    ->where('publish_down <> "0000-00-00 00:00:00"')
                    ->where("DATEDIFF(publish_down, '$now') <= ".$ndays)
                    ->where('send_reminder = 0');
                $db->setQuery($query);
                $rows               = $db->loadObjectList();
                if(count($rows))
                {
                    foreach($rows as $row)
                    {
                        $body1          = $body;
                        $subject1       = $subject;
                        $subject1       = str_replace("[DOCUMENT_TITLE]", $row->title, $subject1);
                        $body1          = str_replace("[PUBLISH_DOWN]", $row->publish_down, $body1);
                        $body1          = str_replace("[DOCUMENT_TITLE]", $row->title, $body1);
                        //send to notification emails
                        if(trim($notification_emails) != '')
                        {
                            $notification_emails = explode(",", trim($notification_emails));
                            if(count($notification_emails))
                            {
                                foreach($notification_emails as $email)
                                {
                                    $body1      = str_replace("[USER]", "Administrator", $body1);
                                    $mailer->sendMail($fromEmail, $fromName, trim($email), $subject1, $body1, 1);
                                    $mailer->clearAllRecipients();
                                }
                            }
                        }
                        $user_id        = $row->created_user_id;
                        //send to uploader
                        if($user_id > 0)
                        {
                            $user       = JFactory::getUser($user_id);
                            $body1      = str_replace("[USER]", $user->name, $body1);
                            $mailer->sendMail($fromEmail, $fromName, $user->email, $subject1, $body1, 1);
                            $mailer->clearAllRecipients();
                        }
                        //update send reminder
                        $db->setQuery("Update #__edocman_documents set send_reminder = '1' where id = '$row->id'");
                        $db->execute();
                    }
                }
            }
        }
		return true;
	}
}
