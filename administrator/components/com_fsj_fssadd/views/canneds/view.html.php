<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
jimport('joomla.utilities.date');

class fsj_fssaddViewcanneds extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{	
		// if Joomla 2.5
		if (!FSJ_Helper::IsJ3())
			JHTML::addIncludePath(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'html'.DS.'joomla25');
		
		// add stylesheets and css for the search bar at the top
		FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.searchtools.js');
		FSJ_Page::Style('administrator/components/com_fsj_fssadd/assets/css/fsj_fssadd.less');

		$doc = JFactory::getDocument();
		$script = "
			function fsj_init_search_tools()
			{
				try {
					jQuery('#adminForm').searchtools();
				} catch (e) { }
			}
			
			jQuery(document).ready(function() {
				setTimeout('fsj_init_search_tools()', 500);
			});
		";
		$doc->addScriptDeclaration($script);





		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}


		$this->loadFilters();

		$this->setupOrdering();

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		if (FSJ_Helper::IsJ3())
		{
			$option = str_replace("com_","",JRequest::getVar('option'));
			if (JRequest::getVar('tmpl') != 'component')
				fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', $option), 'JHtmlSidebar');
				
			$this->sidebar = JHtmlSidebar::render();
		} else {
			$option = str_replace("com_","",JRequest::getVar('option'));
			
			jimport('fsj_core.lib.j25.sidebar');
			jimport('fsj_core.lib.j25.layout');
			jimport('fsj_core.lib.j25.layout_base');
			jimport('fsj_core.lib.j25.layout_file');

			fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', $option), 'JHtmlSidebar');
			
			$this->sidebar = JHtmlSidebar::render();
		}
			
		parent::display($tpl);
		
	}

	protected function loadFilters()
	{
		$db = JFactory::getDBO();
		$this->filters = array();
		
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= fsj_fssaddHelper::getActions($this->state->get('filter.category_id'));
		$user		= JFactory::getUser();
		
		$mainframe = JFactory::getApplication();
		$default = str_replace("com_fsj_","",JRequest::getVar('option'));
		if ($default == "main")
		{
			$admin_com = $mainframe->getUserState( "com_fsj_main.admin_com", $default );
			
			$lang = JFactory::getLanguage();
			$lang->load("com_fsj_" . $admin_com);

		} else {
			$admin_com = $default;
		}
	
		$icon = 'fssadd_canned';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_fssadd/assets/images/{$icon}-48.png); }\n";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		if (JRequest::getVar('option') == "com_fsj_main")
		{
			JToolBarHelper::title(JText::_('COM_FSJ_'.$admin_com.'_SHORT' ). ": " . JText::_('COM_fsj_fssadd_ITEMS_fssadd_cannedS' ), $icon);
		} else {
			JToolBarHelper::title(JText::_(JRequest::getVar('option').'_SHORT' ). ": " . JText::_('COM_fsj_fssadd_ITEMS_fssadd_cannedS' ), $icon);
		}


		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_fsj_fssadd', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('canned.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('canned.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('canneds.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('canneds.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			JToolBarHelper::divider();
			JToolBarHelper::archiveList('canneds.archive');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'canneds.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('canneds.trash');
					}

		if ($canDo->get('core.admin') && JRequest::getVar('tmpl') == "" && !FSJ_Helper::IsJ3()) {
			JToolBarHelper::divider();
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Popup', 'options', "FSJ_ADMIN_COMPONENT_SETTINGS", 'index.php?option=com_fsj_main&admin_com='.$admin_com.'&view=settings&tmpl=component', 875, 550, 0, 0, '');
		}

	}
	
	function setupOrdering()
	{
		$orders = array();
		
									$orders['a.title ASC'] = JText::_('JGLOBAL_TITLE') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.title DESC'] = JText::_('JGLOBAL_TITLE') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																			
											$orders['a.category ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_CATEGORY') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.category DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_CATEGORY') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																																																																																																																																												
											$orders['a.field_count ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_COUNT') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.field_count DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_COUNT') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																				
											$orders['a.showfor ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_SHOWFOR') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.showfor DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_SHOWFOR') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																													$orders['a.ordering ASC'] = JText::_('JGRID_HEADING_ORDERING') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.ordering DESC'] = JText::_('JGRID_HEADING_ORDERING') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
												$orders['a.state ASC'] = JText::_('JSTATUS') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.state DESC'] = JText::_('JSTATUS') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
												$orders['access_level ASC'] = JText::_('JGRID_HEADING_ACCESS') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['access_level DESC'] = JText::_('JGRID_HEADING_ACCESS') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
												$orders['language ASC'] = JText::_('JGRID_HEADING_LANGUAGE') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['language DESC'] = JText::_('JGRID_HEADING_LANGUAGE') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
												$orders['a.id ASC'] = JText::_('JGRID_HEADING_ID') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.id DESC'] = JText::_('JGRID_HEADING_ID') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
							
		$this->orderings = $orders;	
	}
}
