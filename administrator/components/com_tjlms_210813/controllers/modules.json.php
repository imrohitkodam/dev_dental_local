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

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.controlleradmin');

/**
 * Modules list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerModules extends JControllerAdmin
{
	/**
	 * Method to get HTML of a sub format for a lesson format.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getSubFormatHTML()
	{
		$input      = JFactory::getApplication()->input;
		$format     = $input->get('lesson_format', '', 'STRING');
		$sub_format = $input->get('lesson_subformat', '', 'STRING');
		$lesson_id     = $input->get('lesson_id', '', 'INT');
		$form_id     = $input->get('form_id', '', 'STRING');

		if ($lesson_id)
		{
			require_once JPATH_SITE . '/components/com_tjlms/models/assessments.php';
			$this->TjlmsModelAssessments = new TjlmsModelAssessments;
			$model               = $this->getModel('modules');
			$subformat['result'] = 1;
			$subformat['html']   = $model->getallSubFormats_HTML($lesson_id, $format, $sub_format, $form_id);
			$subformat['assessment']   = $this->TjlmsModelAssessments->getAssessmentValue($format, $sub_format);
		}
		else
		{
			$subformat['result'] = 0;
			$subformat['html']   = '';
		}

		if (($sub_format == 'quiz' || $sub_format == 'exercise' || $sub_format == 'feedback') && isset($lesson_id))
		{
			$subformat['scripts'] = $this->getScriptFile();
		}

		$result = json_encode($subformat);
		echo $result;
		jexit();
	}

	/**
	 * Method to the get script file and append to AJAX request.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getScriptFile()
	{
		return $scriptFile = array(JURI::root() . 'administrator/components/com_tmt/assets/js/tmt.js');
	}

	/**
	 * Method to get sub formats of a lesson format.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public function getSubFormats()
	{
		$input  = JFactory::getApplication()->input;
		$format = $input->get('lesson_format', '', 'STRING');

		if ($format)
		{
			$model               = $this->getModel('modules');
			$subformat['result'] = 1;
			$subformat['html']   = $model->getallSubFormats($format);
		}
		else
		{
			$subformat['result'] = 0;
			$subformat['html']   = '';
		}

		$result = json_encode($subformat);
		echo $result;
		jexit();
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
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
	 * Save ordering. Use when sorting is done using drap and drop
	 *
	 * @return  JSON
	 *
	 * @since  1.0.0
	 */
	public function sortModuleLessons()
	{
		$input = JFactory::getApplication()->input;
		$post = $input->post;

		// Get course ID
		$moduleId = $post->get('moduleId', 0, 'INT');
		$courseId = $post->get('courseId', 0, 'INT');
		$lessons = $post->get('lessons', array(), "ARRAY");

		$model = $this->getModel('Lesson', 'TjlmsModel');
		$lessonTable = $model->getTable();

		try
		{
			foreach ($lessons as $ind => $lid)
			{
				$lessonTable->load($lid);
				$lessonTable->id = $lid;
				$lessonTable->mod_id = $moduleId;
				$lessonTable->course_id = $courseId;
				$lessonTable->ordering = $ind;
				$lessonTable->store();
			}

			echo new JResponseJson(1);
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	/**
	 * Save ordering. Use when sorting is done using drap and drop
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function sortModules()
	{
		$input = JFactory::getApplication()->input;
		$post = $input->post;

		$courseId = $post->get('courseId', 0, 'INT');
		$modules = $post->get('modules', array(), "ARRAY");

		$model = $this->getModel('Module', 'TjlmsModel');
		$moduleTable = $model->getTable();

		try
		{
			foreach ($modules as $ind => $mid)
			{
				$moduleTable->load($mid);
				$moduleTable->id = $mid;
				$moduleTable->course_id = $courseId;
				$moduleTable->ordering = $ind;
				$moduleTable->store();
			}

			echo new JResponseJson(1);
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	/**
	 * Function used to delet the module of a particular course
	 *
	 * @return true/false
	 *
	 * @since  1.0.0
	 **/
	public function deleteModule()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$input    = JFactory::getApplication()->input;
		$moduleId = $input->get('moduleId', 0, 'INT');

		try
		{
			$model      = $this->getModel('modules');
			$model->deleteModule($moduleId);

			$errors = $model->getErrors();

			if (!empty($errors))
			{
				$msg = JText::_('COM_TJLMS_MODULE_DELETE_ERROR');
				echo new JResponseJson(0, $msg, true);
			}
			else
			{
				$msg = JText::_('COM_TJLMS_MODULE_DELETED_SUCCESS_MESSAGE');
				echo new JResponseJson(1, $msg);
			}
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	/**
	 * Function used to delet the module of a particular course
	 *
	 * @return true/false
	 *
	 * @since  1.0.0
	 **/
	public function deleteLesson()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$input    = JFactory::getApplication()->input;
		$lessonId = $input->get('lessonId', 0, 'INT');
		$moduleId = $input->get('moduleId', 0, 'INT');
		$courseId = $input->get('courseId', 0, 'INT');

		try
		{
			$model      = $this->getModel('module');
			$model->deleteLesson($lessonId, $moduleId, $courseId);

			$errors = $model->getErrors();

			if (!empty($errors))
			{
				$msg = JText::_('COM_TJLMS_LESSON_DELETE_ERROR');
				echo new JResponseJson(0, $msg, true);
			}
			else
			{
				$msg = JText::_('COM_TJLMS_LESSON_DELETED_SUCCESS_MESSAGE');
				echo new JResponseJson(1, $msg);
			}
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	/**
	 * Function used to chage the state of the module
	 *
	 * @return JSON
	 *
	 * @since  1.0.0
	 **/
	public function changeState()
	{
		$input      = JFactory::getApplication()->input;
		$moduleId   = $input->post->get('mod_id', 0, 'INT');
		$state      = $input->post->get('state', 0, 'INT');

		try
		{
			$model      = $this->getModel('modules');
			$ret = $model->changeState($moduleId, $state);
			$msg  = ($state == 1) ? JText::_("COM_TJLMS_MODULE_PUBLISHED_SUCCESSFULLY") : JText::_("COM_TJLMS_MODULE_UNPUBLISHED_SUCCESSFULLY");

			echo new JResponseJson($ret, $msg);
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	/**
	 * Function used to save the module
	 *
	 * @return true/false
	 *
	 * @since  1.0.0
	 **/
	public function saveModule()
	{
		// Check for request forgeries.
		if (Session::checkToken())
		{
			$app = Factory::getApplication();
			$data   = $this->input->post->get('tjlms_module', array(), 'ARRAY');

			try
			{
				$model = $this->getModel('module');
				$form = $model->getForm();
				$data = $model->validate($form, $data);

				if ($data)
				{
					$data['moduleImage']   = $this->input->files->get('jform', '', 'Array');

					$model->save($data);
				}

				$errors = $model->getErrors();

				if (!empty($errors))
				{
					// Push up to three validation messages out to the user.
					for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
					{
						if ($errors[$i] instanceof Exception)
						{
							$code  = $errors[$i]->getCode();
							$msg[] = $errors[$i]->getMessage();
						}
						else
						{
							$msg[] = $errors[$i];
						}
					}

					$errormsg = JText::_('COM_TJLMS_MODULE_SAVE_ERROR') . " : " . implode("\n", $msg);
					echo new JsonResponse(0, $errormsg, true);
					$app->close();
				}
				else
				{
					$moduleId = ($data['id'])?$data['id']:$model->getState('module.id');

					$moduleData = $model->getItem($moduleId);
					$msg = Text::_('COM_TJLMS_MODULE_UPDATED_SUCCESSFULLY');

					echo new JsonResponse($moduleData, $msg);

					$app->close();
				}
			}
			catch (Exception $e)
			{
				echo new JsonResponse($e);

				$app->close();
			}
		}

			echo new JsonResponse('', Text::_('COM_TJLMS_INVALID_TOKEN'), true);
	}

	/**
	 * Function used to delete the image and data from table
	 *
	 * @return 	mixed
	 *
	 * @since  1.3.5
	 **/
	public function deleteImage()
	{
		if (Session::checkToken())
		{
			$app = Factory::getApplication();
			$moduleId   = $this->input->post->get('moduleId', '', 'int');

			if (!empty($moduleId))
			{
				$model = $this->getModel('module');

				$model->deleteImage($moduleId);

				if ($model->getError())
				{
					echo new JsonResponse('', $model->getError(), true);

					$app->close();
				}

				echo new JsonResponse($moduleId, Text::_('COM_TJLMS_MODULE_UPDATED_SUCCESSFULLY'));

				$app->close();
			}
		}

		echo new JsonResponse('', Text::_('COM_TJLMS_INVALID_TOKEN'), true);
	}
}
