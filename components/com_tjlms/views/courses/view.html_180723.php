<?php
/**
 * @package    LMS_Shika
 * @copyright  Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * view of courses
 *
 * @since  1.0
 */
class TjlmsViewcourses extends JViewLegacy
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
		$app = JFactory::getApplication();
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		$model = $this->getModel();
		$input = JFactory::getApplication()->input;
		$this->tjlmsFrontendHelper = new comtjlmsHelper;
		$this->tjlmsCoursesHelper = new tjlmsCoursesHelper;
		$this->ol_user = JFactory::getUser();
		$this->tjlmsparams = $this->tjlmsFrontendHelper->getcomponetsParams('com_tjlms');
		$this->currency = $this->tjlmsparams->get('currency');
		$this->currency_symbol = $this->tjlmsparams->get('currency_symbol');
		$this->allow_paid_courses = $this->tjlmsparams->get('allow_paid_courses', '0', 'INT');
		$this->course_image_upload_path = $this->tjlmsparams->get('course_image_upload_path', '', 'STRING');
		$cat_id = $input->get('cat_id', '', 'STRING');
		$layouttobeshown = $input->get('show', 'pin', 'STRING');

		$course_cat = $input->get('course_cat', '', 'INT');
		$state = $model->getStatusCategory($course_cat);

		$params = $app->getParams();
		$this->course_images_size = $params->get('course_images_size', 'S_');

		if ($course_cat && $state == 0)
		{
			return false;
		}

		if (!$input->get('course_cat_filter', '', 'STRING'))
		{
			$input->set('course_cat_filter', $cat_id);
		}

		// Course categories
		$this->course_cats = $this->get('TjlmsCats');

		// Check if user is admin
		$this->ifuseradmin = $this->tjlmsFrontendHelper->checkAdmin($this->ol_user);

		// $grades	= $this->get('CourseGrades');

		// $this->assignRef('grades', $grades);
		$allcourses_url = $this->tjlmsFrontendHelper->tjlmsRoute('index.php?option=com_tjlms&view=courses');
		$cat_url = $this->tjlmsFrontendHelper->tjlmsRoute('index.php?option=com_tjlms&view=courses');
		$this->layout = JFactory::getApplication()->input->get('layout', 'default');

		if ($this->layout == 'my' || $this->layout == 'liked')
		{
			// Validate user login.
			if (!$this->ol_user->id)
			{
				$msg = JText::_('COM_TJLMS_MESSAGE_LOGIN_FIRST');

				// Get current url.
				$current = JUri::getInstance()->toString();
				$url = base64_encode($current);
				$app->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . $url, false), $msg);
			}
			else
			{
				// $this->items = $model->getData(1);
			}

			$allcourses_url .= "&layout=my";
			$cat_url .= "&layout=my";
		}

		/* else
		{
		$this->items = $model->getData(0);
		}*/

		$this->allcourses_url = JRoute::_($allcourses_url, false);
		$this->cat_url = $cat_url;
		$this->pagination = $this->get('Pagination');

		// Get Menu params : Which layout to be shown
		$state = $this->get('State');
		$this->menuparams = $state->params;
		parent::display($tpl);
	}
}
