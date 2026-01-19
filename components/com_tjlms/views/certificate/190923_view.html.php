<?php
/**
 * @version    SVN: <svn_id>
 * @package    Plg_System_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Certificate view
 *
 * @since  1.0.0
 */
class TjlmsViewCertificate extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		$this->userid = JFactory::getUser()->id;

		if ($this->userid)
		{
			require_once JPATH_SITE . '/components/com_tjlms/models/course.php';
			$this->tjlmsModelcourse = new TjlmsModelcourse;
			$this->comtjlmsHelper = new comtjlmsHelper;
			$model = $this->getModel();

			$input = JFactory::getApplication()->input;
			$courseId = $input->get('course_id', '0', 'INT');
			$this->course_id = $courseId;

			// Check if your has completed the course
			$this->isCompleted = $model->checkIfCourseCompleted($this->userid, $courseId);

			if (!$this->isCompleted)
			{
				JError::raiseWarning(500, JText::_('COM_TJLMS_CERTIFICATE_CONDITION_FAILED'));

				return false;
			}

			// Check if course certificate has expired
			$this->isExpired = $model->checkIfCourseCertExpired($this->userid, $courseId);

			if ($this->isExpired === true)
			{
				JError::raiseWarning(500, JText::_('COM_TJLMS_CERTIFICATE_EXPIRED'));

				return false;
			}

			$this->html = $this->tjlmsModelcourse->getcertificateHTML($courseId, $this->userid);
		}

		parent::display($tpl);
	}
}
