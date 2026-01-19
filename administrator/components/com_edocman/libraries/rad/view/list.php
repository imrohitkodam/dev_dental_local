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
 * View List class, used to render list of records from frontend or backend of your component
 *
 * @package      OS
 * @subpackage   View
 * @since        1.0
 */
class OSViewList extends OSViewHtml
{

	/**
	 * The model state
	 *
	 * @var OSModelState
	 */
	protected $state;

	/**
	 * List of records which will be displayed
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var JPagination
	 */
	protected $pagination;

	/**
	 * The array which keeps list of "list" options which will used to display as the filter on the list
	 *
	 * @var Array
	 */
	protected $lists = array();

	/**
	 * Method to instantiate the view.
	 *
	 * @param   array $config The configuration data for the view
	 *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to display a view
	 *
	 * @see OSViewHtml::display()
	 */
	public function display()
	{
		$this->prepareView();
		parent::display();
	}

	/**
	 * Prepare the view before it is displayed
	 *
	 */
	protected function prepareView()
	{
		$this->state      = $this->model->getState();
		$this->items      = $this->model->getData();
		$this->pagination = $this->model->getPagination();
		if ($this->isAdminView)
		{
			$this->lists['filter_state']    = str_replace('class="inputbox"', 'class="input-medium"', JHtml::_('grid.state', $this->state->filter_state));
			$this->lists['filter_access']   = JHtml::_('access.level', 'filter_access', $this->state->filter_access, ' class="input-medium" onchange="submit();"', true);
			$this->lists['filter_language'] = JHtml::_('select.genericlist', JHtml::_('contentlanguage.existing', true, true), 'filter_language',
				' class="input-medium" onchange="submit();" ', 'value', 'text', $this->state->filter_language);

			$helperClass = $this->classPrefix . 'Helper';
			if (is_callable($helperClass . '::addSubmenus'))
			{
				call_user_func(array($helperClass, 'addSubmenus'), $this->name);
			}
			else
			{
				call_user_func(array('OSHelper', 'addSubmenus'), $this->option, $this->name);
			}
			EDocmanHelperHtml::renderSubmenu($this->name);
			$this->addToolbar();
		}
	}

	/**
	 * Method to add toolbar buttons
	 *
	 */
	protected function addToolbar()
	{
		$helperClass = $this->classPrefix . 'Helper';
		if (is_callable($helperClass . '::getActions'))
		{
			$canDo = call_user_func(array($helperClass, 'getActions'), $this->name, $this->state);
		}
		else
		{
			$canDo = call_user_func(array('OSHelper', 'getActions'), $this->option, $this->name, $this->state);
		}
		JToolBarHelper::title(JText::_(strtoupper($this->languagePrefix . '_MANAGE_' . $this->name)), 'link ' . $this->name);

		if (in_array($this->name, array('categories', 'documents')))
		{
			if ($canDo->get('core.create') || count(EDocmanHelper::getAuthorisedCategories('core.create')))
			{
				JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
			}
		}
		else
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
			}
		}

		if (($canDo->get('core.edit') || $canDo->get('core.edit.own')) && isset($this->items[0]))
		{
			JToolBarHelper::editList('edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.delete') && isset($this->items[0]))
		{
			JToolBarHelper::deleteList(JText::_($this->languagePrefix . '_DELETE_CONFIRM'), 'delete');
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->published) || isset($this->items[0]->state))
			{
				JToolbarHelper::publish('publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
			if (isset($this->items[0]->checked_out))
			{
				JToolbarHelper::checkin('checkin');
			}
		}
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences($this->option);
		}
	}
}
