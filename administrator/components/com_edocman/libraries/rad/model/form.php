<?php
/**
 * @package     Joomla
 * @subpackage  OS.ModelForm
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

class OSModelForm extends OSModelItem
{
	/**
	 * Array of form objects.
	 *
	 * @var array
	 */
	protected $forms = array();

	/**
	 * Constructor.
	 *
	 * @param array $config An optional associative array of configuration settings.
	 *
	 * @see OSModel
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param array   $data     Data for the form.
	 *
	 * @param boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return mixed A JForm object on success, false on failure
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm($this->option . '.' . $this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		// We don't allows change ordering on the form
		$form->setFieldAttribute('ordering', 'filter', 'unset');

		if (empty($data))
		{
			$data = $this->loadFormData();
		}
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param JForm  $form  The form to validate against.
	 *
	 * @param array  $data  The data to validate.
	 *
	 * @param string $group The name of the field group to validate.
	 *
	 * @return mixed Array of filtered data if valid, false otherwise.
	 */
	public function validate($form, $data, $group = null)
	{
		// Filter and validate the form data.
		$data   = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param integer $pk The numeric id of the primary key.
	 *
	 * @return boolean False on failure or error, true otherwise.
	 */
	public function checkin($pk = null)
	{
		// Get an instance of the row to checkin.
		$table = $this->getTable();
		if (!$table->load($pk))
		{
			$this->setError($table->getError());

			return false;
		}

		// If there is no checked_out or checked_out_time field, just return true.
		if (!property_exists($table, 'checked_out') || !property_exists($table, 'checked_out_time'))
		{
			return true;
		}

		// Check if this is the user having previously checked out the row.
		if (!$this->canCheckin($table))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));

			return false;
		}

		// Attempt to check the row in.
		if (!$table->checkin($pk))
		{
			$this->setError($table->getError());

			return false;
		}


		return true;
	}

	/**
	 * Method to check-out a row for editing.
	 *
	 * @param integer $pk The numeric id of the primary key.
	 *
	 * @return boolean False on failure or error, true otherwise.
	 */
	public function checkout($pk = null)
	{
		// Get an instance of the row to checkout.
		$table = $this->getTable();
		if (!$table->load($pk))
		{
			$this->setError($table->getError());

			return false;
		}

		// Check if this is the user having previously checked out the row.
		if (!$this->canCheckin($table))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKOUT_USER_MISMATCH'));

			return false;
		}


		// Attempt to check the row out.
		if (!$table->checkout(JFactory::getUser()->get('id'), $pk))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}

	/**
	 * Method to test whether a record can be changed state by the current user.
	 *
	 * @param object $record A record object.
	 *
	 * @return boolean True if allowed to change the state of the record. Defaults to the permission for the component.
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', $this->option);
	}

	/**
	 * Method to test whether a record can be checked in by the current user.
	 *
	 * @param object $record A record object.
	 *
	 * @return boolean True if allowed to check in the record.
	 */
	protected function canCheckin($record)
	{
		$user = JFactory::getUser();
		if ($record->checked_out > 0 && $record->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method to test whether a record can be checked out by the current user.
	 *
	 * @param object $record A record object.
	 *
	 * @return boolean True if allowed to checkout the record.
	 */
	protected function canCheckout($record)
	{
		$user = JFactory::getUser();
		if ($record->checked_out > 0 && $record->checked_out != $user->get('id'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method to get a form object.
	 *
	 * @param string  $name    The name of the form.
	 *
	 * @param string  $source  The form source. Can be XML string if file flag is set to false.
	 *
	 * @param array   $options Optional array of options for the form creation.
	 *
	 * @param boolean $clear   Optional argument to force load a new form.
	 *
	 * @param bool    $xpath   An optional xpath to search for the fields.
	 *
	 * @return mixed JForm object on success, False on error.
	 *
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = \Joomla\Utilities\ArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->forms[$hash]) && !$clear)
		{
			return $this->forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/model/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/model/fields');

		try
		{
			$form = JForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Store the form for later.
		$this->forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return array The default data is an empty array.
	 *
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->option . '.' . $this->name . '.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param JForm  $form  A JForm object.
	 *
	 * @param mixed  $data  The data expected for the form.
	 *
	 * @param string $group The name of the plugin group to import (defaults to "content").
	 *
	 *
	 * @return void
	 *
	 * @see JFormField
	 * @throws Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$app = JFactory::getApplication();

		// Trigger the form preparation event.
		$results = $app->triggerEvent('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.

		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			//$error = $dispatcher->getError();
            $error = JError::getError();
			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
	}
}
