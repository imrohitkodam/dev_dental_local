<?php
/**
* @version		1.0.0
* @package		MijoSQL
* @subpackage	MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceSQL www.joomace.net
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class MijosqlViewQueries extends MijosqlView {

	function display($tpl = null) {
        $mainframe = JFactory::getApplication();
        $option = JRequest::getCmd('option');
		$document = JFactory::getDocument();
  		$document->addStyleSheet('components/com_mijosql/assets/css/mijosql.css');
		
		JToolBarHelper::title(JText::_('MijoSQL').' - '.JText::_('COM_MIJOSQL_SAVED_QUERIES'), 'mijosql');
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		
        // ACL
        if (version_compare(JVERSION,'1.6.0','ge') && JFactory::getUser()->authorise('core.admin', 'com_mijosql')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_mijosql', '550');
        }
	
		$this->mainframe = JFactory::getApplication();
		$this->option = JRequest::getWord('option');

		$filter_order		= $mainframe->getUserStateFromRequest($option.'.queries.filter_order',		'filter_order',		'title',	'string');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'.queries.filter_order_Dir',	'filter_order_Dir',	'',			'word');
		$search				= $mainframe->getUserStateFromRequest($option.'.queries.search',			'search',			'',			'string');

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->lists = $lists;
		$this->items = $this->get('Data');
		$this->pagination = $this->get('Pagination');

		parent::display($tpl);
	}
}