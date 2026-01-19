<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/ 
defined('_JEXEC') or die;

class FSS_GUIPlugin_Form_Canned_Admin extends FSS_Plugin_GUI
{
	var $title = "Form Based Canned Replies - Handlers";
	var $description = "Integration of the form based canned replies system for ticket handlers";

	function adminTicketReplyBar($ticket)
	{ 
		$this->loadSettings();

		if (isset($this->settings->main->enable) && $this->settings->main->enable == "0") return;

		$lang = JFactory::getLanguage();
		$lang->load("com_fsj_fssadd");

		require_once(JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'view.html.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'layout.list.php');

		$list = new FSJ_FSSADDViewCanned_List();
		$list->getReplies();
		$list->loadTicket($ticket->id);
		$list->filterCanned("admin");

		if (is_array($list->canned) && count($list->canned) > 0)
		{

			$label = JText::_('CANNED_FORM_REPLY');
			if (!empty($this->settings->main->text) && $this->settings->main->text != "") $label = JText::_($this->settings->main->text);

			$class = "btn btn-default";
			if (!empty($this->settings->main->btnclass) && $this->settings->main->btnclass != "") $class = JText::_($this->settings->main->btnclass);

			$html[] = "\n<a href='" . JRoute::_('index.php?option=com_fsj_fssadd&view=canned&tmpl=component&message=admin&ticket=' . $ticket->id) . "' class='show_modal_iframe {$class}'><i class='icon-list'></i> " . $label . "</a>";

			$styles = array();

			if (!empty($this->settings->main->hidereply) && $this->settings->main->hidereply) $styles[] = ".fss_main a.post_reply { display: none; }";
			if (!empty($this->settings->main->hideprivate) && $this->settings->main->hideprivate) $styles[] = ".fss_main a.post_private { display: none; }";

			if (count($styles) > 0) $html[] = "<style>\n" . implode("\n", $styles) . "</style>\n";

			echo implode("\n", $html);
		}
	}

	function adminCannedDropdown($ticket, $editor)
	{
		$this->loadSettings();

		if (isset($this->settings->main->inlist) && $this->settings->main->inlist == "0") return;

		if (!is_object($ticket)) return;
		if ($ticket->id < 1) return;

		$lang = JFactory::getLanguage();
		$lang->load("com_fsj_fssadd");

		require_once(JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'view.html.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'layout.list.php');

		$list = new FSJ_FSSADDViewCanned_List();
		$list->getReplies();
		$list->loadTicket($ticket->id);
		$list->filterCanned("admin");

		if (is_array($list->canned) && count($list->canned) > 0)
		{
			$html = array();

			foreach ($list->canned as $canned)
			{
				$canned->category = ltrim($canned->category, "0123456789 ");
				if ($canned->category != "") continue;

				$url = JRoute::_('index.php?option=com_fsj_fssadd&view=canned&layout=form&tmpl=component&canned=' . $canned->id . "&ticket=" . $ticket->id . "&message=admin&insert=" . $editor);
				$html[] = '<li><a class="show_modal_iframe" href="' . $url . '">'.JText::_($canned->title) . '</a></li>';
			}

			$category = "xxxxxxx";
			$in_li = false;

			foreach ($list->canned as $canned)
			{
				$canned->category = ltrim($canned->category, "0123456789 ");
				if ($canned->category == "") continue;

				if ($canned->category != $category)
				{
					if ($in_li) $html[] = "</ul></li>";

					$html[] = '<li class="dropdown-submenu pull-left"><a>'.JText::_($canned->category) . '</a>';
					$html[] = '<ul class="dropdown-menu">';

					$category = $canned->category;
					$in_li = true;
				}

				$url = JRoute::_('index.php?option=com_fsj_fssadd&view=canned&layout=form&tmpl=component&canned=' . $canned->id . "&ticket=" . $ticket->id . "&message=admin&insert=" . $editor);
				$html[] = '<li><a class="show_modal_iframe" href="' . $url . '">'.JText::_($canned->title) . '</a></li>';
			}

			if ($in_li) $html[] = "</ul></li>";

			return implode("\n", $html);
		}

		return null;
	}
}