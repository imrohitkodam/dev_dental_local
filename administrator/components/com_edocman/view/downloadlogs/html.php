<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;
/**
 * View class for a list of Edocman.
 */
class EDocmanViewDownloadlogsHtml extends OSViewList
{
	protected function addToolbar()
	{	
		$user = JFactory::getUser();		
		JToolBarHelper::title(JText::_('EDOCMAN_DOWNLOAD_LOGS'), 'download');
		JToolBarHelper::custom('export', 'download', 'download', 'EDOCMAN_EXPORT_DOWNLOAD_LOG', false);
		if ($user->authorise('core.delete')) 
		{
			JToolBarHelper::custom('delete', 'delete', 'delete', 'Empty', false) ;
		}
		
		// We don't need toolbar in the modal window.
		if (version_compare(JVERSION, '3.0', 'ge')) {
			if ($this->getLayout() !== 'modal')
			{
				//EdocmanHelper::addSideBarmenus('downloadlogs');
				//$this->sidebar = JHtmlSidebar::render();
			}
		}
		//EDocmanHelperHtml::renderSubmenu('downloadlogs');
	}
}
