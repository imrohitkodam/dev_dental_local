<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit an article.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */

require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjcftype.php');

class fsj_fssaddViewcanned_field extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		FSJ_Page::IncludeModal();

	FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.iframe.popup.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.iframe.inline.js');
		JRequest::setVar('tmpl','component');
	JRequest::setVar('mode','inline');

		if (FSJ_Helper::IsJ3())
			JHtml::_('formbehavior.chosen', 'select');

		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->canDo	= fsj_fssaddHelper::getActions($this->state->get('filter.category_id'));

		$this->form->state = $this->state;
	
																	
			$fieldtype_handler = new JFormFieldfsjcftype();
			$fieldtype_handler->_name = 'fieldtype';
			$fieldtype_handler->fsjcftype = json_decode("{\"paramfield\":\"params\",\"class\":\"chzn-done testclass\"}");
			$fieldtype_handler->Process($this->item);		

																				
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		
		$model = $this->getModel();

		$checkedOut	= false;

		$canDo = fsj_fssaddHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		
		$text = JText::_(($checkedOut ? 'FSJ_VIEW' : ($isNew ? 'FSJ_ADD' : 'FSJ_EDIT'))) . " " . JText::_('COM_fsj_fssadd_ITEMS_fssadd_canned_field');
		
		$this->main_section_text = $text;
		
		if ($this->item && $this->item->id > 0 && isset($this->item->title) && $this->item->title != "")
			$this->main_section_text .= " - " . $this->item->title;
		
		$mainframe = JFactory::getApplication();
		$default = str_replace("com_fsj_","",JRequest::getVar('option'));
		if ($default == "main")
		{
			$admin_com = $mainframe->getUserState( "com_fsj_main.admin_com", $default );
		} else {
			$admin_com = $default;
		}
		
		$icon = 'fssadd_canned_field';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_fssadd/assets/images/{$icon}-48.png); }";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		
		JToolBarHelper::title( JText::_('COM_FSJ_'.$admin_com.'_SHORT' ).': ' . $text, $icon);
		
		// Built the actions for new and existing records.

		$can_save = $model->canSave($this->item);

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_fsj_fssadd', 'core.create')) > 0)) {
			if ($can_save)
			{
				JToolBarHelper::apply('canned_field.apply');
				JToolBarHelper::save('canned_field.save');
				JToolBarHelper::save2new('canned_field.save2new');
			}
			JToolBarHelper::cancel('canned_field.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($can_save)
				{
					if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
				
						JToolBarHelper::apply('canned_field.apply');
						JToolBarHelper::save('canned_field.save');

						// We can save this record, but check the create permission to see if we can return to make a new one.
						if ($canDo->get('core.create')) {
							JToolBarHelper::save2new('canned_field.save2new');
						}
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('canned_field.save2copy');
			}

			JToolBarHelper::cancel('canned_field.cancel', 'JTOOLBAR_CLOSE');
		}
		
		

	}
}
