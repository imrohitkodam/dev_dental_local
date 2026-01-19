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

jimport('joomla.application.component.view');

/**
 * View class for a edit of Tjlms course.
 *
 * @since  1.0.0
 */
class TjlmsViewModule extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();

		// $isNew		= ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo		= TjlmsHelper::getActions();

		if (JVERSION > 3.0)
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_TJMODULE_ADD'), 'pencil-2');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_TJMODULE_EDIT'), 'module.png');
		}

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{
			// JToolBarHelper::apply('module.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('module.save', 'JTOOLBAR_SAVE');
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('module.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('module.cancel', 'JTOOLBAR_CLOSE');
		}

		$this->toolbarHTML = JToolbar::getInstance('toolbar')->render('toolbar');
	}
}
