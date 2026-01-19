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

jimport('joomla.application.component.controlleradmin');

/**
 * Courses list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerCourses extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.0.0
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->text_prefix = 'COM_TJLMS_COURSES';
		$this->registerTask('unfeatured', 'featured');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 * @param   STRING  $config  model options
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'course', $prefix = 'TjlmsModel', $config = Array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $id     The validated data.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function postDeleteHook(JModelLegacy $model, $id = null)
	{
		// Get the model
		$model     = $this->getModel();
		$ifSuccess = $model->onafterCourseDelete($id);

		return $ifSuccess;
	}

	/**
	 * Method to toggle the featured setting of a list of courses.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user   = JFactory::getUser();
		$ids    = $this->input->get('cid', array(), 'array');
		$values = array('featured' => 1, 'unfeatured' => 0);
		$task   = $this->getTask();
		$value  = JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_tjlms.course.' . (int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}

			if ($value == 1)
			{
				$message = JText::plural('COM_TJLMS_COURSES_N_ITEMS_FEATURED', count($ids));
			}
			else
			{
				$message = JText::plural('COM_TJLMS_COURSES_N_ITEMS_UNFEATURED', count($ids));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=courses', false), $message);
	}
}
