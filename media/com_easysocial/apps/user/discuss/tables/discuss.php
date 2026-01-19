<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ADMIN_ROOT . '/tables/post.php');

class DiscussTableDiscuss extends DiscussPost
{
	public function notify($isModerate = false, $isNew = true)
	{
		$my = ED::user();
		$config	= ED::getConfig();
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		// prepare email content and information.
		$category = ED::table('Category');
		$category->load($this->category_id);

		// For use within the emails.
		$emailData = array();
		$emailData['postTitle'] = $this->title;
		$emailData['postAuthor'] = $my->id ? $my->getName() : $this->poster_name;
		$emailData['postAuthorAvatar'] = $my->getAvatar();
		$emailData['postLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $this->id, false, true);

		$emailContent = $this->content;

		$emailContent = strip_tags($this->content);

		$emailContent = $this->trimEmail($emailContent);
		$emailData['attachments'] = '';
		$emailData['postContent'] = $emailContent;
		$emailData['post_id'] = $this->id;
		$emailData['cat_id'] = $this->category_id;
		$emailData['emailTemplate']	= 'email.subscription.site.new.php';
		$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_ASKED', $this->id, $this->title);

		// If this is a private post, do not notify anyone
		if (!$this->private && $category->canAccess()) {

			// Notify site subscribers
			if ($config->get('main_sitesubscription') && ($isNew) && $this->published == DISCUSS_ID_PUBLISHED && !$config->get('notify_all')) {
				ED::mailer()->notifySubscribers($emailData, array($my->user->email));
			}

			// Notify category subscribers
			if ($config->get('main_ed_categorysubscription') && ($isNew) && $this->published == DISCUSS_ID_PUBLISHED && !$config->get('notify_all')) {
				ED::mailer()->notifySubscribers($emailData, array($my->user->email));
			}

			// Notify EVERYBODY
			if ($config->get('notify_all')) {
				ED::mailer()->notifyAllMembers($emailData, array($my->user->email));
			}
		}

		// Notify admins and category moderators
		if ($isNew) {
			ED::mailer()->notifyAdministrators($emailData, array($my->user->email), $config->get('notify_admin'), $config->get('notify_moderator'));
		}
	}

	public function trimEmail($content)
	{
		$config	= ED::getConfig();

		if ($config->get('layout_editor') != 'bbcode') {
			// Remove img tags
			$content = strip_tags($content, '<p><div><table><tr><td><thead><tbody><br><br />');

			return $content;
		}

		if ($config->get('main_notification_max_length') > '0') {
			$content = $this->truncateContentByLength($content, '0', $config->get('main_notification_max_length'));
		}

		// Remove video codes from the e-mail since it will not appear on e-mails
		$content = ED::videos()->strip($content);

		return $content;
	}
}
