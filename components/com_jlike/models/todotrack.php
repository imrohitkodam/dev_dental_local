<?php
/**
 * @package     JLike
 * @subpackage  com_jlike
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Language\Text;

/**
 * Jlike model.
 *
 * @since  __DEPLOY_VERSION__
 */
class JlikeModelTodoTrack extends FormModel
{
	/**
	 * Method to get the table
	 *
	 * @param   string  $type    Name of the JTable class
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $config  Optional configuration array for JTable object
	 *
	 * @return  JTable|boolean JTable if found, boolean false on failure
	 */
	public function getTable($type = 'TodoTrack', $prefix = 'JlikeTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_jlike/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to save
	 *
	 * @param   array  $data  data
	 *
	 * @return  boolean  True on success
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function save($data)
	{
		$userId = Factory::getUser()->id;

		if (!$userId)
		{
			return false;
		}

		$todoTable = Jlike::table('todos');

		if (!$todoTable->load(array('id' => $data['todo_id'])))
		{
			$this->setError(Text::_('PLG_API_JLIKE_NO_DATA_FOUND'));

			return;
		}

		$data['user_id'] = $userId;
		$table = $this->getTable();

		// If data available then unset the timestart and add the previous spent time
		if ($table->load(array('session_id' => $data['session_id'])))
		{
			unset($data['timestart']);
			$data['spent_time'] = $table->spent_time + $data['spent_time'];
		}

		// Save data in track table
		$result = $table->save($data);

		if ($result)
		{
			$todoData = array();

			// Save total spent time in todo table
			$todoData['id'] = $data['todo_id'];
			$todoData['spent_time'] = $this->getTotalTimeSpent($data['todo_id']);

			/* Mark todo complete
			 In future we will have multiple ways to mark todo completion
			 If Ideal time is set to zero and person visits the page - it should be marked as complete
			 */

			if ($todoTable->status != 'C')
			{
				if ($todoData['spent_time'] >= $todoTable->ideal_time)
				{
					$todoData['status'] = 'C';
				}
			}

			$recommendationFormModel = Jlike::model('RecommendationForm', array('ignore_request' => true));
			$recommendationFormModel->save($todoData);
		}
		else
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}

	/**
	 * Function to get Total Time spent on Content
	 *
	 * @param   INT  $todoId  todo id
	 *
	 * @return  integer sum of spent time.
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getTotalTimeSpent($todoId)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('SUM(spent_time)');
		$query->from($db->quoteName('#__jlike_todo_track'));
		$query->where($db->quoteName('todo_id') . ' = ' . (int) $todoId);

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
	}
}
