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
jimport('joomla.application.component.model');

/**
 * Manageenrollments list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerManageenrollments extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 * @param   ARRAY   $config  Array
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'manageenrollments', $prefix = 'TjlmsModel',$config = Array())
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
	 * Change state of an item.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function publish()
	{
		$input = JFactory::getApplication()->input;
		$post = $input->post;

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
		$courseId = $input->get('course_id', '', 'INT');
		$courseParam = '';

		if ($courseId)
		{
			$courseParam = '&tmpl=component&course_id=' . $courseId;
		}

		// Get some variables from the request
		if (empty($cid))
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('manageenrollments');

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			$model->setItemState($cid, $value, $courseId);

			if ($value == 1)
			{
				$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
			}
			elseif ($value == 0)
			{
				$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
			}
			elseif ($value == 2)
			{
				$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
			}
			else
			{
				$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
			}

			$this->setMessage(JText::plural($ntext, count($cid)));
		}

		$this->setRedirect('index.php?option=com_tjlms&view=manageenrollments' . $courseParam, $msg);
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
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('manageenrollments');

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($count = $model->delete($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', $count));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=manageenrollments', false));
	}

	/**
	 * change Due date
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function updateAssignmentDate()
	{
		$mainframe = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$data = array();

		$courseId = $input->get('element_id', '', 'INT');
		$data['selectedcourse'] = array($courseId);

		$data['start_date'] = $input->get('start_date', '', 'DATE');
		$data['due_date'] = $input->get('due_date', '', 'DATE');
		$data['notify_user'] = $input->get('notify_user', '', 'INT');
		$data['type'] = 'assign';
		$data['element'] = 'com_tjlms.course';
		$data['element_id'] = $courseId;
		$data['recommend_friends'] = array($input->get('recommend_friends', '', 'INT'));
		$data['todo_id'] = 0;

		if ($input->get('todo_id', '', 'INT'))
		{
			$data['todo_id'] = $input->get('todo_id', '', 'INT');
		}

		$model = $this->getModel('manageenrollments');
		$res   = $model->updateAssignmentDate($data);

		if ($res)
		{
			// Add a message to the message queue
			$mainframe->enqueueMessage(JText::_('COM_TJLMS_ASSIGN_DUEDATE_CHANGE'), 'success');
		}
		else
		{
			// Add a message to the message queue
			$mainframe->enqueueMessage(JText::_('COM_TJLMS_ASSIGN_DUEDATE_CHANGE_FAILED'), 'error');
		}

		$rUrl = $input->get('rUrl', '', 'STRING');
		$rUrl = base64_decode($rUrl);

		if ($rUrl)
		{
			$this->setRedirect(JRoute::_($rUrl, false));
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=manageenrollments', false));
		}
	}
}
