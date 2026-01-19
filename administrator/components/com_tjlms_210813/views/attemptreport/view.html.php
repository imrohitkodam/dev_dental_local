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
class TjlmsViewAttemptreport extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');

		$this->pagination	= $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->filterForm->removeField('lesonformat', 'filter');
		$this->activeFilters = $this->get('ActiveFilters');

		$input = JFactory::getApplication()->input;

		$this->tjlmsparams = JComponentHelper::getParams('com_tjlms');
		$this->adminKey = $this->tjlmsparams->get('admin_key_review_answersheet', 'abcd1234', 'STRING');
		$this->adminKey = md5($this->adminKey);

		// Call helper function
		$TjlmsHelper = new TjlmsHelper;
		$TjlmsHelper->getLanguageConstant();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjlmsHelper::addSubmenu('attemptreport');

		$this->addToolbar();

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/tjlms.php';
		$user      = JFactory::getUser();
		$canCreate = $user->authorise('core.create', 'com_tjlms');
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_ATTEMPT_REPORT'), 'list');

		if (!empty($this->items) && $canCreate === true && $user->id)
		{
			$message = array();
			$message['success'] = JText::_("COM_TJLMS_EXPORT_FILE_SUCCESS");
			$message['error'] = JText::_("COM_TJLMS_EXPORT_FILE_ERROR");
			$message['inprogress'] = JText::_("COM_TJLMS_EXPORT_FILE_NOTICE");
			$message['text'] = JText::_("COM_TJLMS_EXPORT_TOOLBAR_TITLE");

			$bar->appendButton('CsvExport', $message);
		}

		// Show trash and delete for components that uses the state field
		JToolBarHelper::deleteList(JText::_('COM_TJLMS_SURE_DELETE'), 'attemptreport.delete', 'JTOOLBAR_DELETE');

		$this->extra_sidebar = '';
	}
}
