<?php
/**
 * @package     OS
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2014 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Item View Class. This class is used to display details information of an item
 * or display form allow add/editing items
 *
 * @package     OS
 * @subpackage  View
 * @since       1.0
 */
class OSViewItem extends OSViewHtml
{

	/**
	 * The model state.
	 *
	 * @var OSModelState
	 */
	protected $state;

	/**
	 * The record which is being added/edited
	 *
	 * @var Object
	 */
	protected $item;

	/**
	 * The array which keeps list of "list" options which will be displayed on the form
	 *
	 * @var Array
	 */
	protected $lists;

	/**
	 * Hold actions which can be performed
	 * @var JObject
	 */

	protected $canDo;

	/**
	 * Method to display the view
	 *
	 * @see OSViewHtml::display()
	 */
	public function display()
	{
		$this->prepareView();
		parent::display();
	}

	/**
	 * Method to prepare all the data for the view before it is displayed
	 */
	protected function prepareView()
	{
		$this->state = $this->model->getState();
		$this->item = $this->model->getData();
		if ($this->isAdminView)
		{

			$this->form = $this->model->getForm();
			$this->addToolbar();
		}

	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$user = JFactory::getUser();
		$helperClass = $this->classPrefix . 'Helper';
		if (is_callable($helperClass . '::getActions'))
		{
			$canDo = call_user_func(array($helperClass, 'getActions'), $this->name, $this->state);
		}
		else
		{
			$canDo = call_user_func(array('OSHelper', 'getActions'), $this->option, $this->name, $this->state);
		}
		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}
		if ($this->item->id)
		{
			$toolbarTitle = $this->languagePrefix . '_' . $this->name . '_EDIT';
		}
		else
		{
			$toolbarTitle = $this->languagePrefix . '_' . $this->name . '_NEW';
		}
		JToolBarHelper::title(JText::_(strtoupper($toolbarTitle)));
		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create') || count(EDocmanHelper::getAuthorisedCategories('core.create'))))
		{

			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create') || count(EDocmanHelper::getAuthorisedCategories('core.create'))))
		{
			JToolBarHelper::custom('save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if ($this->item->id && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('save2copy');
		}

		if ($this->item->id)
		{
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		}
		else
		{
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');
		}
		$this->canDo = $canDo;
	}
}
