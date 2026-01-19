<?php
/**
 * @version     1.0.0
 * @package     com_tjlms
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Aniket <aniket_c@tekdi.net> - http://www.techjoomla.com
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * View class for a student_course_report of Tjlms.
 */
class TjlmsViewStudent_course_report extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app=JFactory::getApplication();
		$user=JFactory::getUser();

		// Validate user login
		if (! $user->id) {
			$app=JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_TMT_MESSAGE_LOGIN_FIRST'),'warning');
			return false;
		}

		$input=JFactory::getApplication()->input;
		$course_id = $input->get('course_id','0','INT');
		$student_id = $input->get('studid','0','INT');
		if( $course_id == 0 && $student_id == 0 )
		{
			$app=JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_TMT_MESSAGE_EXPECTED_VALUES_NOT_FOUND '),'warning');
			return false;
		}
		$model	= $this->getModel( 'student_course_report' );
		$tjlmscoursehelper = new tjlmsCoursesHelper();

		$this->StudentName = JFactory::getUser($student_id)->name;
		$this->CourseName = $tjlmscoursehelper->courseName($course_id);//'Joomla for Beginners';
//		$this->left_subsdays = 4;
		$this->CourseDetails = $model->getUserCourseDetails($course_id, $student_id );
		$progress = $model->getUserCourseProgress($course_id, $student_id );
		$this->Completed = 30;//$progress->complete;
		$this->Pending = 40;//$progress->pending;

		parent::display($tpl);
	}

}
?>
