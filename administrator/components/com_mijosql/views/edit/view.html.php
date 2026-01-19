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

class MijosqlViewEdit extends MijosqlView {

	public function display($tpl = null) {
		$db = JFactory::getDbo();
		
		$task = JRequest::getCmd('task');
		$table = MijosqlHelper::getVar('tbl');
        $query = MijosqlHelper::getVar('qry');
		$id = JRequest::getInt('id', JRequest::getInt('id', null, 'post'), 'get');
		$key = JRequest::getCmd('key', JRequest::getCmd('key', null, 'post'), 'get');
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_mijosql/assets/css/mijosql.css');
		
		// Toolbar
		JToolBarHelper::title(JText::_('MijoSQL') .': <small><small> '. $table.' [ '.$key.' = '.$id.' ]' .' </small></small>', 'mijosql');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
       
		if ($task == 'edit') {
			$fld_value = '$value = $this->rows[$this->id][$field];';
		}
		else {
			$fld_value = '$value = "";';
		}
		
		list($rows, $last_key_vol) = $this->get('Data');
		
		$this->task = $task;
		$this->id = $id;
		$this->key = $key;
		$this->table = $table;
		$this->query = $query;
		$this->fld_value = $fld_value;
		$this->last_key_vol = $last_key_vol;
		$this->rows = $rows;
		
		$fields = $this->get('Fields');
		if (!MijosqlHelper::is30()) {
			$fields = $fields[$this->table];
		}
		
		$this->fields = $fields;
		
		parent::display($tpl);
	}
}