<?php
/**
* @version		1.0.0
* @package		MijoSQL
* @subpackage	MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceSQL www.joomace.net
*
* Based on EasySQL Component
* @copyright (C) 2008 - 2011 Serebro All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.lurm.net
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class MijosqlControllerMijosql extends MijosqlController {

    public function __construct($default = array()) {
		parent::__construct($default);

        $this->_model = $this->getModel('mijosql');
	}

    public function run() {
   		// Check for request forgeries
   		//JRequest::checkToken() or jexit('Invalid Token');

		JRequest::setVar('view', 'mijosql');

		parent::display();
   	}
	
    public function csv() {
        ob_end_clean();

        $file_name = 'export_'.$this->_table.'.csv';

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Accept-Ranges: bytes');
        header('Content-Disposition: attachment; filename='.basename($file_name).';');
        header('Content-Type: text/plain; '._ISO);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Pragma: no-cache');

        echo $this->_model->exportToCsv($this->_query);

        jexit();
    }

    public function delete() {
   		// Check for request forgeries
   		//JRequest::checkToken() or jexit('Invalid Token');

        if ($this->_model->delete($this->_table)) {
            $msg = JText::_('COM_MIJOSQL_DELETE_TRUE');
        }
        else {
            $msg = JText::_('COM_MIJOSQL_DELETE_FALSE');
        }
		
		$vars = 'ja_tbl_g='.base64_encode($this->_table).'&ja_qry_g='.base64_encode($this->_query);

   		$this->setRedirect('index.php?option=com_mijosql&'.$vars, $msg);
   	}

    public function saveQuery() {
   		// Check for request forgeries
   		//JRequest::checkToken() or jexit('Invalid Token');

   		$this->setRedirect('index.php?option=com_mijosql&controller=queries&task=edit&ja_query='.base64_encode($this->_query));
   	}
}