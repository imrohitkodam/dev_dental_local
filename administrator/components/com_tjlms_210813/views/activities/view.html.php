<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('techjoomla.tjtoolbar.button.csvexport');


/**
 * View class for a list of Tjlms.
 *
 * @since  1.0.0
 */
class TjlmsViewActivities extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$userId = JFactory::getUser()->id;
		$this->userid = $userId;

		if ($userId)
		{
			$canDo = TjlmsHelper::getActions();

			if (!$canDo->get('view.activities'))
			{
				JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));

				return false;
			}

			$app = JFactory::getApplication();
			$this->comtjlmstrackingHelper = new comtjlmstrackingHelper;
			$this->state = $this->get('State');
			$this->items = $this->get('Items');
			$this->pagination = $this->get('Pagination');
			$this->filterForm = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');

			$this->allUsers = $this->get('AllUsers');

			$this->userFilter[] = JHTML::_('select.option', '', JText::_('COM_TJLMS_SELONE_USER'));

			foreach ($this->allUsers as $eachUser)
			{
				$this->userFilter[] = JHTML::_('select.option', $eachUser->id, $eachUser->name);
			}

			$this->addToolbar();

			TjlmsHelper::addSubmenu('activities');

			if (JVERSION >= '3.0')
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  Toolbar instance
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/tjlms.php';

		JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_ACTIVITIES'), 'list');
		$bar = JToolBar::getInstance('toolbar');

		if (!empty($this->items))
		{
			$message = array();
			$message['success'] = JText::_("COM_TJLMS_EXPORT_FILE_SUCCESS");
			$message['error'] = JText::_("COM_TJLMS_EXPORT_FILE_ERROR");
			$message['inprogress'] = JText::_("COM_TJLMS_EXPORT_FILE_NOTICE");
			$message['text'] = JText::_("COM_TJLMS_EXPORT_TOOLBAR_TITLE");

			$bar->appendButton('CsvExport', $message);
		}

		$this->extra_sidebar = '';
	}
}
