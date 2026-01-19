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

/**
 * View class for a list of Tjlms.
 *
 * @since  1.0.0
 */
class TjlmsViewCourses extends JViewLegacy
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
		$user 		= JFactory::getUser();
		$userId 	= $user->get('id');

		JLoader::register('TjlmsHelper', JPATH_ADMINISTRATOR . '/components/com_tjlms/helpers/tjlms.php');

		$this->canDo = TjlmsHelper::getActions();
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$canManageEnroll = TjlmsHelper::canManageEnrollment();

		if ($this->canDo->get('core.delete')
			|| $this->canDo->get('core.edit')
			|| $this->canDo->get('core.edit.state')
			|| $this->canDo->get('core.manage.material')
			|| $canManageEnroll)
		{
			// Will allow user to access view list
		}
		elseif ($this->canDo->get('core.create'))
		{
			// If only create access. Should remove created_by field
			$this->filterForm->removeField('created_by', 'filter');

			$this->state->set('filter.created_by', $userId);
		}
		else
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjlmsHelper::addSubmenu('courses');

		$comtjlmsHelper = new comtjlmsHelper;

		// Get component params
		$this->tjlmsparams = $comtjlmsHelper->getcomponetsParams('com_tjlms');

		// Get Item ID for the link
		$linkOfFrontendDashboard = 'index.php?option=com_tjlms&view=teacher_report';
		$this->teacherCourseDashboardItemid = $comtjlmsHelper->getitemid($linkOfFrontendDashboard);
		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

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
		$state = $this->get('State');

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_COURSES'), 'book');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_COURSES'), 'courses.png');
		}

		if ($this->canDo->get('core.create'))
		{
			JToolBarHelper::addNew('course.add', 'JTOOLBAR_NEW');
		}

		if (($this->canDo->get('core.create') || $this->canDo->get('core.edit')) && isset($this->items[0]))
		{
			JToolBarHelper::editList('course.edit', 'JTOOLBAR_EDIT');
		}

		if (($this->canDo->get('core.create') || $this->canDo->get('core.edit.state')))
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('courses.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('courses.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($this->canDo->get('core.edit.state'))
		{
			JToolBarHelper::custom('courses.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}

		if ($state->get('filter.state') == -2 && ($this->canDo->get('core.create') || $this->canDo->get('core.delete')))
		{
			JToolBarHelper::deleteList('COM_TJLMS_COURSES_DELETE_MSG', 'courses.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($this->canDo->get('core.create') || $this->canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('courses.trash', 'JTOOLBAR_TRASH');
		}

		if ($this->canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_tjlms');
		}
	}

	/**
	 * Function use to get all sort fileds
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.created_by' => JText::_('COM_TJLMS_COURSES_CREATED_BY'),
			'a.catid' => JText::_('COM_TJLMS_COURSES_CAT_ID'),
			'a.title' => JText::_('COM_TJLMS_COURSES_TITLE'),
			'a.start_date' => JText::_('COM_TJLMS_COURSES_START_DATE'),
			'a.type' => JText::_('COM_TJLMS_COURSES_TYPE'),
		);
	}
}
