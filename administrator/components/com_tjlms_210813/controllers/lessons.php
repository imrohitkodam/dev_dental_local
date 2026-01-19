<?php
/**
 * @package    Tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.folder');
use Joomla\Utilities\ArrayHelper;

/**
 * Lessons list controller class.
 *
 * @since  1.0
 */
class TjlmsControllerLessons extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.0
	 */
	public function getModel($name = 'lesson', $prefix = 'TjlmsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array( 'ignore_request' => true ));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
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
	 * Function used to remove associated files
	 *
	 * @return  JSON
	 *
	 * @since  1.0
	 */
	public function removeAssocFiles()
	{
		$input = JFactory::getApplication()->input;
		$mediaId = $input->get('media_id', '0', 'INT');
		$lessonId = $input->get('lesson_id', '0', 'INT');
		$model = $this->getModel('lessons');
		$removeAssocFiles = $model->removeAssocFiles($mediaId, $lessonId);
		echo json_encode($removeAssocFiles);
		jexit();
	}

	/**
	 * Method to change the statuses a list of items.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function delete()
	{
		// Check for request forgeries
		\JSession::checkToken() or die(\JText::_('JINVALID_TOKEN'));

		if ($this->removeAttemptedLessonsFromList())
		{
			parent::delete();
		}
	}

	/**
	 * Method to change the statuses a list of items.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	private function removeAttemptedLessonsFromList()
	{
		$cid = $this->input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$model = $this->getModel('managelessons');

		$usedLessons = $model->getAttemptedLessons($cid);

		if (!empty($usedLessons))
		{
			$result = array_diff($usedLessons, $cid);

			$lessonTitles = $model->getLessonTitles($usedLessons);
			$this->setMessage(JText::sprintf("COM_TJLMS_LESSONLIST_CANNOT_CHANGE_STATE", implode(",", $lessonTitles)), 'info');

			if (!empty($result))
			{
				$this->input->set('cid', $result);

				return true;
			}
			else
			{
				$this->setRedirect(\JRoute::_('index.php?option=com_tjlms&view=managelessons', false));

				return false;
			}
		}

		return true;
	}
}
