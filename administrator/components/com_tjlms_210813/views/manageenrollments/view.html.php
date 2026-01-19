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
class TjlmsViewManageenrollments extends JViewLegacy
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
		$input = JFactory::getApplication()->input;
		$course_id = $input->get('course_id', '', 'INT');

		$this->canDo 	= TjlmsHelper::getActions();
		$canEnroll		= false;

		$user   = JFactory::getUser();
		$userId = $user->id;

		if ($course_id)
		{
			$canEnroll = TjlmsHelper::canManageCourseEnrollment($course_id);
		}
		else
		{
			$canEnroll = TjlmsHelper::canManageEnrollment();

			// Own courses enrollment access
			if ($canEnroll === -1)
			{
				$this->state->set('filter.created_by', $userId);
			}
		}

		// Only Manager
		if ($canEnroll === -2)
		{
			$this->state->set('filter.subuserfilter', 1);
		}

		if (!$canEnroll)
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if ($course_id)
		{
			$tjlmsCoursesHelper = new TjlmsCoursesHelper;
			$this->courseInfo = $tjlmsCoursesHelper->getCourseColumn($course_id, 'title');
		}

		if ($canEnroll === -2)
		{
			$this->filterForm->removeField('subuserfilter', 'filter');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjlmsHelper::addSubmenu('manageenrollments');

		$comtjlmsHelper = new comtjlmsHelper;
		$linkOfStudentCourseReport = JUri::root() . 'index.php?option=com_tjlms&view=student_course_report';
		$this->studentCourseDashboardItemid = $comtjlmsHelper->getitemid($linkOfStudentCourseReport);

		$this->addToolbar();
		$this->sidebar = '';

		if (JVERSION < '3.0')
		{
			// Creating status filter.
			$sstatus = array();
			$sstatus[] = JHTML::_('select.option', '', JText::_('COM_TJLMS_SELONE_STATE'));
			$sstatus[] = JHTML::_('select.option', 1, JText::_('JPUBLISHED'));
			$sstatus[] = JHTML::_('select.option', 0, JText::_('JUNPUBLISHED'));
			$this->sstatus = $sstatus;

			// Creating status filter.
			$coursefilter = array();
			$coursefilter[] = JHTML::_('select.option', '', JText::_('COM_TJLMS_FILTER_SELECT_COURSE'));
			$allcourses = $this->get('AllCourses');

			foreach ($allcourses as $c)
			{
				$coursefilter[] = JHtml::_('select.option', $c->value, $c->text);
			}

			$this->coursefilter = $coursefilter;
		}

		if (!$course_id && JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
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

		$state	= $this->get('State');
		$canDo	= TjlmsHelper::getActions($state->get('filter.category_id'));

		// Get an instance of the Toolbar
		$toolbar = JToolbar::getInstance('toolbar');

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_MANAGEENROLLMENTS'), 'list');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_MANAGEENROLLMENTS'), 'manageenrollments.png');
		}

		if ($canDo->get('core.edit.state'))
		{
			// Add batch button manage enrollment view

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('manageenrollments.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('manageenrollments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);

				// Show trash and delete for components that uses the state field
				JToolBarHelper::deleteList(JText::_('COM_TJLMS_SURE_DELETE'), 'manageenrollments.delete', 'JTOOLBAR_DELETE');
			}
		}

		$button = '<a data-toggle="modal" href="#import" class="btn btn-small">
						<span class="icon-upload"></span>' . JText::_('COM_TJLMS_TITLE_MANAGEENROLLMENTS_IMPORT') . '</a>';

		$toolbar->appendButton('Custom', $button);
		$title = JText::_('COM_TJLMS_JTOOLBAR_BATCH_ASSIGN_TITLE');

		// Add batch button manage enrollment view
		$layout = new JLayoutFile('joomla.toolbar.batch');
		$dhtml = $layout->render(array('title' => $title));
		$toolbar->appendButton('Custom', $dhtml, 'batch');

		JHtml::script('administrator/components/com_tjlms/assets/js/tjlms_admin.js');

		$link = "'" . JUri::root() . "administrator/index.php?option=com_tjlms&view=enrolment&tmpl=component&selectedcourse[]=0" . "'";

		// Add New button manage enrollment view
		$toolbar->prependButton(
		'Custom', '<a class="modal btn btn-small btn-success"
		onclick="opentjlmsSqueezeBox(' . $link . ')">
		<span class="icon-new icon-white"></span>' . JText::_('COM_TJLMS_TITLE_MANAGEENROLLMENTS_NEW') . '</a>'
		);

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_tjlms');
		}

		// Set sidebar action - New in 3.0
		if (JVERSION >= '3.0')
		{
			JHtmlSidebar::setAction('index.php?option=com_tjlms&view=manageenrollments');
		}

		$this->extra_sidebar = '';
	}
}
