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

class fsj_fssaddViewcanned_fields extends JViewLegacy
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

		FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.iframe.child.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fsj/fsj.utils.js');
				
		JRequest::setVar('tmpl','component');
		JRequest::setVar('mode','inline');




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
		
	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD__FILTER_CANNED_ID') . ' -', 'id', 'title');
		
		
		
					
			$query = 'SELECT id, title FROM #__fsj_fssadd_canned WHERE 1 ';
			$sort = ' ORDER BY title';
			
						
			$query .= $sort;
			$db->setQuery($query);
			//echo "Filter Qry : $query<br>";
			$filter = array_merge($filter, $db->loadObjectList());
			
						
					
		$this->filters['canned_id'] = JHTML::_('select.genericlist',  $filter, 'filter_canned_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.canned_id'));

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
	
		$icon = 'fssadd_canned_field';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_fssadd/assets/images/{$icon}-48.png); }\n";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		if (JRequest::getVar('option') == "com_fsj_main")
		{
			JToolBarHelper::title(JText::_('COM_FSJ_'.$admin_com.'_SHORT' ). ": " . JText::_('COM_fsj_fssadd_ITEMS_fssadd_canned_fieldS' ), $icon);
		} else {
			JToolBarHelper::title(JText::_(JRequest::getVar('option').'_SHORT' ). ": " . JText::_('COM_fsj_fssadd_ITEMS_fssadd_canned_fieldS' ), $icon);
		}


		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_fsj_fssadd', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('canned_field.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('canned_field.edit');
		}

		if ($canDo->get('core.edit.state')) {

		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'canned_fields.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::deleteList('', 'canned_fields.delete', 'JACTION_DELETE');
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
																																																																			
											$orders['a.description ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_DESCRIPTION') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.description DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_DESCRIPTION') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																														
											$orders['lf33 ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_CANNED_ID') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['lf33 DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_CANNED_ID') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																
											$orders['a.tab ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_TAB') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.tab DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_TAB') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																
											$orders['a.fieldtype ASC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_FIELDTYPE') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.fieldtype DESC'] = JText::_('FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_FIELDTYPE') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																			$orders['a.ordering ASC'] = JText::_('JGRID_HEADING_ORDERING') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.ordering DESC'] = JText::_('JGRID_HEADING_ORDERING') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
												$orders['a.id ASC'] = JText::_('JGRID_HEADING_ID') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.id DESC'] = JText::_('JGRID_HEADING_ID') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
							
		$this->orderings = $orders;	
	}
}
