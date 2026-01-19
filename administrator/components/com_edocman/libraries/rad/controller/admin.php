<?php
/**
 * Admin Controller Class, implement basic tasks which is used when developing component from admin
 *
 * @author      Ossolution Team
 * @package     OS
 * @subpackage  Controller
 */
defined('_JEXEC') or die();

class OSControllerAdmin extends OSController
{
	/**
	 * The URL view item variable.
	 *
	 * @var string
	 */
	protected $viewItem;

	/**
	 * The URL view list variable.
	 *
	 * @var string
	 */
	protected $viewList;

	/**
	 * The context for the session storage
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * Constructor
	 *
	 * @param OSInput $input  The controller input
	 * @param array    $config An optional associative array of configuration settings.
	 */
	public function __construct(OSInput $input = null, array $config = array())
	{
		parent::__construct($input, $config);

		if (isset($config['view_item']))
		{
			$this->viewItem = $config['view_item'];
		}
		else
		{
			$this->viewItem = $this->name;
		}

		if (isset($config['view_list']))
		{
			$this->viewList = $config['view_list'];
		}
		else
		{
			$this->viewList = OSInflector::pluralize($this->viewItem);
		}

		$this->context = $this->option . '.' . $this->name;

		// Register tasks mapping
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('archive', 'publish');
		$this->registerTask('trash', 'publish');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');
	}

	/**
	 * Display Form allows adding a new record
	 */
	public function add()
	{
		if ($this->allowAdd($this->input->getData()))
		{
			// Clear the record edit information from the session.
			$this->app->setUserState($this->context . '.data', null);
			$this->input->set('view', $this->viewItem);
			$this->display();
		}
		else
		{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}
	}

	/**
	 * Display Form allows editing record
	 */
	public function edit()
	{
		$id = $this->input->getInt('id', 0);
		if (!$this->allowEdit(array('id' => $id)))
		{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}
		$model   = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));
		$row     = $model->getTable();
		$checkIn = property_exists($row, 'checked_out');
		// Checkout the record before allowing edit
		if ($checkIn && !$model->checkout($id))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}
		$this->holdEditId($this->context, $id);
		$this->app->setUserState($this->context . '.data', null);
		$this->input->set('view', $this->viewItem);
		$this->display();
	}

	/**
	 * Method to save a record.
	 *
	 * @return boolean True if successful, false otherwise.
	 *
	 */
	public function save()
	{
		// Check for request forgeries.
		$this->csrfProtection();
		$app     = $this->getApplication();
		$input   = $this->getInput();
		$model   = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));
		$row     = $model->getTable();
		$data    = $input->post->get('jform', array(), 'array');
		$id      = $input->getInt('id');
		$checkIn = property_exists($row, 'checked_out');
		$task    = $this->getTask();

		// Populate the record id.
		$data['id'] = $id;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			if ($checkIn && $model->checkin($data['id']) === false)
			{
				// Check-in failed. Go back to the item and display a notice.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');
				$this->setRedirect(JRoute::_($this->getViewItemUrl($id), false));

				return false;
			}
			// Reset the ID and then treat the request as for Apply.
			$data['id'] = 0;
			$task       = 'apply';
		}

		// Permission check.
		if (!$this->allowSave($data))
		{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}

		// Validate the posted form data.
		$form      = $model->getForm($data, false);
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($this->context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_($this->getViewItemUrl($id), false));

			return false;
		}

		// Set sanitized data back to input object
		$input->set('jform', $validData);

		// Attempt to save the data.
		try
		{
			$model->save($input);
		}
		catch (Exception $e)
		{
			$app->setUserState($this->context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $e->getMessage()), 'error');
			$this->setRedirect(JRoute::_($this->getViewItemUrl($id), false));

			return false;
		}
		$this->setMessage(JText::_('JLIB_APPLICATION' . ($id == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));
		$id = $input->getInt('id');

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Clear the record id and data from the session.
				$app->setUserState($this->context . '.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_($this->getViewItemUrl($id), false));
				break;

			case 'save2new':
				//Check in the current record
				if ($checkIn)
				{
					$model->checkin($id);
				}

				// Clear the record id and data from the session.
				$this->releaseEditId($this->context, $id);
				$app->setUserState($this->context . '.data', null);

				// Redirect to new item screen
				$this->setRedirect(JRoute::_($this->getViewItemUrl(), false));
				break;
			default:
				//Check in the record if needed
				if ($checkIn)
				{
					$model->checkin($id);
				}

				// Clear the record id and data from the session.
				$this->releaseEditId($this->context, $id);
				$app->setUserState($this->context . '.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_($this->getViewListUrl(), false));
				break;
		}

		return true;
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @return boolean True if access level checks pass, false otherwise.
	 *
	 */
	public function cancel()
	{
		$this->csrfProtection();
		$model   = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));
		$row     = $model->getTable();
		$checkIn = property_exists($row, 'checked_out');
		$id      = $this->input->getInt('id');

		// Attempt to check-in the current record.
		if ($id && $checkIn)
		{
			if ($model->checkin($id) === false)
			{
				// Check-in failed, go back to the record and display a notice.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');
				$this->setRedirect(JRoute::_($this->getViewItemUrl($id), false));

				return false;
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($this->context, $id);
		$this->app->setUserState($this->context . '.data', null);
		$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

		return true;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function save_order_ajax()
	{
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
        $pks    = \Joomla\Utilities\ArrayHelper::toInteger($pks);
        $order  = \Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		$this->app->close();
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return boolean True on success
	 *
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		$this->csrfProtection();

		// Get the input
		$cid    = $this->input->post->get('cid', array(), 'array');
		$order  = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
        $cid    = \Joomla\Utilities\ArrayHelper::toInteger($cid);
        $order  = \Joomla\Utilities\ArrayHelper::toInteger($order);

		// Perform permission check
		for ($i = 0, $n = count($cid); $i < $n; $i++)
		{
			if (!$this->allowEditState($cid[$i]))
			{
				unset($cid[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
			}
		}

		if (count($cid) == 0)
		{
			$this->setMessage(JText::_($this->languagePrefix . '_ERROR_NO_ITEMS_SELECTED'), 'warning');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}

		// Get the model
		$model = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));

		// Save the ordering
		$return = $model->saveorder($cid, $order);
		if ($return === false)
		{
			// Reorder failed
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()), 'error');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}
		else
		{
			// Reorder succeeded
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return true;
		}
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return boolean True on success
	 *
	 */
	public function reorder()
	{
		// Check for request forgeries.
		$this->csrfProtection();
		$cid = $this->input->post->get('cid', array(), 'array');
		for ($i = 0, $n = count($cid); $i < $n; $i++)
		{
			if (!$this->allowEditState($cid[$i]))
			{
				unset($cid[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
			}
		}

		if (count($cid) == 0)
		{
			$this->setMessage(JText::_($this->languagePrefix . '_ERROR_NO_ITEMS_SELECTED'), 'warning');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}

		$inc    = ($this->getTask() == 'orderup') ? -1 : 1;
		$model  = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));
		$return = $model->reorder($cid, $inc);
		if ($return === false)
		{
			// Reorder failed.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()), 'error');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return false;
		}
		else
		{
			// Reorder succeeded.
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED'), 'message');
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false));

			return true;
		}
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return boolean True on success
	 */
	public function checkin()
	{
		// Check for request forgeries.
		$this->csrfProtection();
		$cid = $this->input->post->get('cid', array(), 'array');
		$cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);

		$model  = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));
		$return = $model->checkin($cid);
		if ($return === false)
		{
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false), $message, 'error');

			return false;
		}
		else
		{
			// Checkin succeeded.
			$message = JText::plural($this->languagePrefix . '_N_ITEMS_CHECKED_IN', count($cid));
			$this->setRedirect(JRoute::_($this->getViewListUrl(), false), $message);

			return true;
		}
	}

	/**
	 * Removes an item.
	 *
	 * @return void
	 *
	 */
	public function delete()
	{
		// Check for request forgeries
		$this->csrfProtection();

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Sanitize the data
		$cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);

		// Check delete permission
		for ($i = 0, $n = count($cid); $i < $n; $i++)
		{
			if (!$this->allowDelete($cid[$i]))
			{
				unset($cid[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELTE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
			}
		}

		if (count($cid) < 1)
		{
			JLog::add(JText::_($this->languagePrefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural($this->languagePrefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
		}

		//Redirect user back to items manager screen after editing
		$this->setRedirect(JRoute::_($this->getViewListUrl(), false));
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return void
	 */
	public function publish()
	{
		// Check for request forgeries
		$this->csrfProtection();

		// Get items to publish from the request and sanitize the data
		$cid = $this->input->get('cid', array(), 'array');
		$cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);

		// Perform permission checking, make sure only allowed records can be changed state
		for ($i = 0, $n = count($cid); $i < $n; $i++)
		{
			if (!$this->allowEditState($cid[$i]))
			{
				unset($cid[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
			}
		}

		if (empty($cid))
		{
			JLog::add(JText::_($this->languagePrefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel($this->name, array('default_model_class' => 'OSModelAdmin', 'ignore_request' => true));
			$data  = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
			$task  = $this->getTask();
			$value = \Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');
			try
			{
				$model->publish($cid, $value);
				if ($value == 1)
				{
					$ntext = $this->languagePrefix . '_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->languagePrefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->languagePrefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->languagePrefix . '_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$extension    = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(JRoute::_($this->getViewListUrl() . $extensionURL, false));
	}

	/**
	 * Get url of the page which display list of records
	 *
	 * @return string
	 */
	public function getViewListUrl()
	{
		return 'index.php?option=' . $this->option . '&view=' . $this->viewList;
	}

	/**
	 * Get url of the page which allow adding/editing a record
	 *
	 * @param  int $id
	 *
	 * @return string
	 */
	public function getViewItemUrl($id = null)
	{
		$url = 'index.php?option=' . $this->option . '&view=' . $this->viewItem;
		if ($id)
		{
			$url .= '&id=' . $id;
		}

		return $url;
	}

	/**
	 * Method to add a record ID to the edit list.
	 *
	 * @param string  $context The context for the session storage.
	 *
	 * @param integer $id      The ID of the record to add to the edit list.
	 *
	 * @return void
	 */
	protected function holdEditId($context, $id)
	{
		$values = (array) $this->app->getUserState($context . '.id');
		// Add the id to the list if non-zero.
		if (!empty($id))
		{
			array_push($values, (int) $id);
			$values = array_unique($values);
			$this->app->setUserState($context . '.id', $values);
		}
	}

	/**
	 * Method to check whether an ID is in the edit list.
	 *
	 * @param string  $context The context for the session storage.
	 *
	 * @param integer $id      The ID of the record to add to the edit list.
	 *
	 * @return void
	 *
	 */
	protected function releaseEditId($context, $id)
	{
		$values = (array) $this->app->getUserState($context . '.id');
		// Do a strict search of the edit list values.
		$index = array_search((int) $id, $values, true);
		if (is_int($index))
		{
			unset($values[$index]);
			$this->app->setUserState($context . '.id', $values);
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param array $data An array of input data.
	 *
	 * @return boolean
	 *
	 */
	protected function allowAdd($data = array())
	{
		return JFactory::getUser()->authorise('core.create', $this->option);
	}

	/**
	 * Method to check if you can edit a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param array $data An array of input data.
	 *
	 * @return boolean
	 */
	protected function allowEdit($data = array())
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param array  $data An array of input data.
	 *
	 * @param string $key  The name of the key for the primary key.
	 *
	 * @return boolean
	 */
	protected function allowSave($data, $key = 'id')
	{
		$id = isset($data[$key]) ? $data[$key] : '0';

		if ($id)
		{
			return $this->allowEdit($data, $key);
		}
		else
		{
			return $this->allowAdd($data);
		}
	}

	/**
	 * Method to check whether the current user is allowed to delete a record
	 *
	 * @param int id Record ID
	 *
	 * @return boolean True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 */
	protected function allowDelete($id)
	{
		return JFactory::getUser()->authorise('core.delete', $this->option);
	}

	/**
	 * Method to check whether the current user can change status (publish, unpublish of a record)
	 *
	 * @param int $id Id of the record
	 *
	 * @return boolean True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 */
	protected function allowEditState($id)
	{
		return JFactory::getUser()->authorise('core.edit.state', $this->option);
	}
}
