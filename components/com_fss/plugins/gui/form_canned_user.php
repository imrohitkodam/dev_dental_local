<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSS_GUIPlugin_Form_Canned_User extends FSS_Plugin_GUI
{
	var $title = "Form Based Canned Replies - User";
	var $description = "Integration of the form based canned replies system for users";

	function userTicketReplyBar($ticket)
	{
		return $this->doReplyBar($ticket['id']);
	}
	
	// new version with ticket as an object
	function userTicketReplyBar2($ticket)
	{
		return $this->doReplyBar($ticket->id);
	}
	
	function doReplyBar($ticketid)
	{
		$this->loadSettings();

		$lang = JFactory::getLanguage();
		$lang->load("com_fsj_fssadd");

		require_once(JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'view.html.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'layout.list.php');

		$list = new FSJ_FSSADDViewCanned_List();
		$list->getReplies();
		$list->loadTicket($ticketid);
		$list->filterCanned("user");

		if (is_array($list->canned) && count($list->canned) > 0)
		{
			$label = JText::_('CANNED_FORM_REPLY'); 
			if (!empty($this->settings->main->text) && $this->settings->main->text != "") $label = JText::_($this->settings->main->text);

			$class = "btn btn-default";
			if (!empty($this->settings->main->btnclass) && $this->settings->main->btnclass != "") $class = JText::_($this->settings->main->btnclass);

			$html = array();
			
			$html[] = "\n<a href='" . JRoute::_('index.php?option=com_fsj_fssadd&view=canned&tmpl=component&message=user&ticket=' . $ticketid) . "' class='show_modal_iframe {$class}'><i class='icon-list'></i> {$label}</a>";
			
			if (!empty($this->settings->main->hidereply) && $this->settings->main->hidereply) $html[] = "<style>.fss_main a.post_reply { display: none; }</style>";

			echo implode($html);
		}
	}
}