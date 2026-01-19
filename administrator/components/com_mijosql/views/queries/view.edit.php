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

class MijosqlViewQueries extends MijosqlView {

	public function display($tpl = null) {
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_mijosql/assets/css/mijosql.css');
		
		// Toolbar
		JToolBarHelper::title(JText::_('MijoSQL'), 'mijosql');
		JToolBarHelper::save();
		JToolBarHelper::cancel();

		$this->row = $this->get('QueryData');
		
		parent::display($tpl);
	}
}