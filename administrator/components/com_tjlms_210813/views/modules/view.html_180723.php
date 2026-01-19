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
jimport('joomla.filesystem.folder');
/**
 * View class for a list of Tjlms.
 *
 * @since  1.0.0
 */
class TjlmsViewModules extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$model	= $this->getModel('modules');

		$layout = JFactory::getApplication()->input->getCmd('layout', 'default');
		$this->state		= $this->get('State');
		$lesson_id = $input->get('lesson_id', '0', 'INT');

		if ($layout == 'selectassociatefiles')
		{
			$allAssociatedFiles = $model->getallAssociatedFiles($lesson_id);
			$this->allAssociatedFiles = $allAssociatedFiles;
		}
		else
		{
			TjlmsHelper::addSubmenu('courses');

			// Get current course info
			$this->course_id	=	$courseId = $input->get('course_id', '', 'INT');

			if (!$this->course_id && $lesson_id)
			{
				$this->course_id  = TjlmsHelper::getLessonCourse($lesson_id);
			}

			$this->CourseInfo = $model->getPresentCourseInfo($this->course_id);

			if (!$this->course_id || empty($this->CourseInfo))
			{
				JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

				return false;
			}

			$canManageMaterial	= TjlmsHelper::canManageCourseMaterial($this->course_id, null, $this->CourseInfo->created_by);

			if (!$canManageMaterial)
			{
				JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

				return false;
			}

			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');

			// Check for errors.
			if (count($errors = $this->get('Errors')))
			{
				throw new Exception(implode("\n", $errors));
			}

			$this->params = JComponentHelper::getParams('com_tjlms');

			// Get modules and lessons details for a particlular module.

			$this->moduleData = $model->getModuleData();

			/*quiz start*/
			$this->quiz_init = 0;

			if (JFolder::exists(JPATH_SITE . '/components/com_tmt'))
			{
				$this->quiz_init = 1;
				require_once JPATH_SITE . '/components/com_tmt/helpers/' . 'tests.php';
				$tmtTestsHelper = new tmtTestsHelper;
				$this->one_quiz = $tmtTestsHelper->getUserTests(JFactory::getUser()->id);
			}

			/*quiz ends*/

			// $this->videoSubFormats = $model->getallSubFormats('video');
			$this->model = $model;

			$this->lessonform	=	$model->getLessonForm();

			$this->addToolbar();

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
	 * @since   1.0
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/tjlms.php';

		$state	= $this->get('State');

		if (JVERSION > 3.0)
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_TJMODULES'), 'list');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_TJMODULES'), 'modules.png');
		}

		$toolbar = JToolbar::getInstance('toolbar');

		$button = '<a href="' . JUri::base() . 'index.php?option=com_tjlms&view=courses" class="btn btn-small">
						<span class="icon-arrow-left-2"></span>' . JText::_('COM_TJLMS_BACK_TO_COURSES_BTN') . '</a>';
		$toolbar->appendButton('Custom', $button);

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/module';
	}
}
