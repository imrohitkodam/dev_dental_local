<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'support_ticket.php');

class FSJ_FSSADDViewCanned_List extends FSJ_FSSADDViewCanned
{
	function display($tpl = NULL)
	{
		$this->getReplies();
		$this->loadTicket(JRequest::getVar('ticket'));
		$this->filterCanned(JRequest::getVar('message'));

		$this->_display();
	}	

	function loadTicket($ticket_id)
	{
		$this->ticket = new SupportTicket();
		$this->ticket->load($ticket_id, "force");
	}

	function filterCanned($message)
	{
		$result = array();

		if ($message == "admin")
		{
			if (!FSS_Permission::auth("fss.handler", "com_fss.support_admin"))
			$message = "user";
		}

		foreach ($this->canned as $canned)
		{
			if ($canned->usestatus)
			{
				$stauts = explode(";", $canned->statuslist);

				if (!in_array($this->ticket->ticket_status_id, $stauts)) continue;
			}

			if ($message == "admin" && $canned->showfor == 1) continue;
			if ($message == "user" && $canned->showfor == 2) continue;

			$result[] = $canned;
		}

		$this->canned = $result;

		//usort($this->canned, array($this, "cannedSort"));
	}
	
	private function cannedSort($a, $b)
	{
		if ($a->category != $b->category) return strcmp($a->category, $b->category);	
		return strcmp($a->title, $b->title);	
	}
		
	function getReplies()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__fsj_fssadd_canned WHERE state = 1 ORDER BY category, ordering";
		$db->setQuery($sql);

		$this->canned = $db->loadObjectList();
	}
}