<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$lastMessage = $conversation->getLastMessage();
$message = '';

if ($lastMessage) {
	$message = ES::string()->parseBBCode($lastMessage->getIntro(), ['escape' => false, 'emoticons' => true, 'links' => false]);
	$message = preg_replace("/<br>/", "", $message);

	$attachments = $lastMessage->getAttachments();

	if (!empty($attachments)) {
		$message = '<i class="fdi fa fa-paperclip"></i>&nbsp;' . ($message ? $message : JText::_('COM_EASYSOCIAL_CONVERSATIONS_ATTACHMENTS'));
	}
}
?>
<?php echo $message; ?>