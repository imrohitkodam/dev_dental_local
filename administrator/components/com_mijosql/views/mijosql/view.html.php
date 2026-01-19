<?php
/**
* @version		1.0.0
* @package		MijoSQL
* @subpackage	MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceSQL www.joomace.net
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

class MijosqlViewMijosql extends MijosqlView {

	public function display($tpl = null){
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_mijosql/assets/css/mijosql.css');
		
		// Toolbar
		JToolBarHelper::title(JText::_('MijoSQL').' - '.JText::_('COM_MIJOSQL_RUN_QUERY'), 'mijosql');
		
		if (MijosqlHelper::is30()) {
			JToolBarHelper::custom('run', 'play.png', 'play.png', JText::_('COM_MIJOSQL_RUN_QUERY'), false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('savequery', 'folder-close.png', 'folder-close.png', JText::_('COM_MIJOSQL_SAVE_QUERY'), false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('csv', 'upload.png', 'upload.png', JText::_('COM_MIJOSQL_EXPORT_CSV'), false);
		}
		else {
			JToolBarHelper::custom('run', 'run.png', 'run.png', JText::_('COM_MIJOSQL_RUN_QUERY'), false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('savequery', 'savequery.png', 'savequery.png', JText::_('COM_MIJOSQL_SAVE_QUERY'), false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('csv', 'csv.png', 'csv.png', JText::_('COM_MIJOSQL_EXPORT_CSV'), false);

		}
		
		// ACL
		if (version_compare(JVERSION,'1.6.0','ge') && JFactory::getUser()->authorise('core.admin', 'com_mijosql')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_mijosql', '550');
		}
		
		$this->data = $this->get('Data');
		$this->tables = $this->get('Tables');
		$this->prefix = $this->get('Prefix');
		
		parent::display($tpl);
	}
}