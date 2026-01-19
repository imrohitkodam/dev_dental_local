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

jimport('techjoomla.common');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Courses list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerAttemptreport extends JControllerAdmin
{
	/**
	 * Function used to change the lesson status
	 *
	 * @return  redirect
	 *
	 * @since   1.0.0
	 */
	public function changeLessonStatus()
	{
		$mainframe = Factory::getApplication();
		$input = $mainframe->input;
		$post = $input->post;
		$attempt_id = $post->get('attempt_id', '', 'INT');
		$lesson_status = $post->get('lesson_status', '', 'STRING');
		$usedAsPopupReport = $post->get('usedAsPopupReport', '0', 'INT');

		if (!empty($attempt_id))
		{
			$model      = $this->getModel('attemptreport');
			$data       = $model->updateAttemptData($attempt_id, '', $lesson_status);

			if ($data)
			{
				// Add a message to the message queue
				$model->updateCouserTrack($attempt_id);
				$mainframe->enqueueMessage(Text::_('COM_TJLMS_LESSON_STATUS_CHANGE'));
			}
			else
			{
				// Add a message to the message queue
				$mainframe->enqueueMessage(Text::_('COM_TJLMS_LESSON_STATUS_CHANGE_FAILED'), 'error');
			}

			$additionalParam = '';

			if ($usedAsPopupReport == 1)
			{
				$additionalParam = '&usedAsPopupReport=1&tmpl=component';
			}

			$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=attemptreport' . $additionalParam, false));
		}
	}

	/**
	 * Function used to change the attempt data
	 *
	 * @return  json
	 *
	 * @since   1.0.0
	 */
	public function updateAttemptData()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$input = Factory::getApplication()->input;
		$post = $input->post;
		$attempt_id = $post->get('attemptId', '', 'INT');
		$score = $post->get('score', '', 'INT');

		$data = 0;

		if (!empty($attempt_id))
		{
			$model      = $this->getModel('attemptreport');
			$data       = $model->updateAttemptData($attempt_id, $score);
		}

		echo json_encode($data, /** @scrutinizer ignore-type */ true);
		jexit();
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function delete()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = Factory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('attemptreport');

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			/** @scrutinizer ignore-deprecated */ JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(Text::/** @scrutinizer ignore-call */ plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=attemptreport', false));
	}
}
