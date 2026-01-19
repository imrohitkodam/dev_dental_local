<?php
/**
 * @version     1.0.0
 * @package     com_tmt
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <contact@techjoomla.com> - http://techjoomla.com
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Test controller class.
 */
class TmtControllerTestForm extends TmtController
{

	/**
	 * Method to save posted item data and redirect to the edit form.
	 *
	 * @since	1.0
	 */
	public function apply()
	{

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app    = JFactory::getApplication();
		$model = $this->getModel('TestForm', 'TmtModel');

		// Get all form data
		$data=JFactory::getApplication()->input->post;

		//print_r($data);die;

		// *Important - get non-jform data for all non-jform fields
		$cid=$data->get('cid','','array');
		$reviewers_hidden=$data->get('reviewers_hidden','','array');

		// Get all jform data
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// * Important when checkboxes are unchecked.
		if(! isset($data['notify_candidate_passed']))
		{
			$data['notify_candidate_passed'] = 0;
		}

		if(! isset($data['notify_candidate_failed']))
		{
			$data['notify_candidate_failed'] = 0;
		}

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// *Important - pass on non-jform data to model
		$data['cid']=$cid;
		$data['reviewers_hidden']=$reviewers_hidden;

		// Check for errors.
		if ($data === false) {

			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_tmt.edit.test.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_tmt.edit.test.id');
			$this->setRedirect(JRoute::_('index.php?option=com_tmt&view=testform&id='.$id, false));
			return false;
		}

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false) {

			// Save the data in the session.
			$app->setUserState('com_tmt.edit.test.data', $data);

			// Redirect back to the edit screen.
			$id = (int)$app->getUserState('com_tmt.edit.test.id');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_tmt&view=testform&id='.$id, false));
			return false;
		}

		// Check in the profile.
		if ($return) {
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_tmt.edit.test.id', null);

		// Redirect to the edit screen.
		$tmtFrontendHelper=new tmtFrontendHelper();
		$itemid=$tmtFrontendHelper->getItemId('index.php?option=com_tmt&view=testform');
		$redirect=JRoute::_('index.php?option=com_tmt&view=testform&id='.$return.'&Itemid='.$itemid,false);
		$msg= JText::_('COM_TMT_MESSAGE_SAVE_TEST');
		$this->setRedirect($redirect,$msg);

		// Flush the data from the session.
		$app->setUserState('com_tmt.edit.test.data', null);
	}

	/**
	 * Method to save posted item data and redirect tests list
	 *
	 * @since	1.0
	 */
	public function save()
	{

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app= JFactory::getApplication();
		$model = $this->getModel('TestForm', 'TmtModel');

		// Get all form data
		$data=JFactory::getApplication()->input->post;

		// *Important - get non-jform data for all non-jform fields
		$cid=$data->get('cid','','array');
		$reviewers_hidden=$data->get('reviewers_hidden','','array');

		// Get all jform data
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// *Important - pass on non-jform data to model
		$data['cid']=$cid;
		$data['reviewers_hidden']=$reviewers_hidden;

		// Check for errors.
		if ($data === false) {

			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_tmt.edit.test.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_tmt.edit.test.id');
			$this->setRedirect(JRoute::_('index.php?option=com_tmt&view=testform&id='.$id, false));
			return false;
		}

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false) {

			// Save the data in the session.
			$app->setUserState('com_tmt.edit.test.data', $data);

			// Redirect back to the edit screen.
			$id = (int)$app->getUserState('com_tmt.edit.test.id');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_tmt&view=testform&id='.$id, false));
			return false;
		}

		// Check in the profile.
		if ($return) {
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_tmt.edit.test.id', null);

		// Redirect to the list screen.
		$tmtFrontendHelper=new tmtFrontendHelper();
		$itemid=$tmtFrontendHelper->getItemId('index.php?option=com_tmt&view=tests');
		$redirect=JRoute::_('index.php?option=com_tmt&view=tests&Itemid='.$itemid,false);
		$msg= JText::_('COM_TMT_MESSAGE_SAVE_TEST');
		$this->setRedirect($redirect,$msg);

		// Flush the data from the session.
		$app->setUserState('com_tmt.edit.test.data', null);
	}

	/**
	 * Method for redirecting to the list view
	 *
	 * @since	1.0
	 */
	function cancel()
	{
		$tmtFrontendHelper=new tmtFrontendHelper();
		$itemid=$tmtFrontendHelper->getItemId('index.php?option=com_tmt&view=tests');
		$redirect=JRoute::_('index.php?option=com_tmt&view=tests&Itemid='.$itemid,false);
		$msg= JText::_('COM_TMT_MESSAGE_CANCEL');
		$this->setRedirect($redirect,$msg);
	}

	/**
	 * Method to get questions based on rules posted using ajax
	 *
	 * @since	1.0
	 */
	function fetchQuestions()
	{
		$data=JFactory::getApplication()->input->post;
		$model=$this->getModel('TestForm', 'TmtModel');
		$fetchQuestions=$model->fetchQuestions($data);

		// Output the response as JSON
		header('Content-type: application/json');
		echo json_encode($fetchQuestions);
		jexit();
	}

}
