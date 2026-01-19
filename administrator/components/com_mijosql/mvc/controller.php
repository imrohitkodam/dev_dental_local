<?php
/*
* @package		MijoSQL
* @copyright	2009-2012 Mijosoft LLC, mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.controller');

if (!class_exists('MijosoftController')) {
    if (interface_exists('JController')) {
        abstract class MijosoftController extends JControllerLegacy {}
    }
    else {
        class MijosoftController extends JController {}
    }
}

class MijosqlController extends MijosoftController {

	public function __construct($default = array()) {
		parent::__construct($default);

        $this->_db = JFactory::getDBO();

        $this->_table = MijosqlHelper::getVar('tbl');
        $this->_query = MijosqlHelper::getVar('qry');

		$this->registerTask('add', 'edit');
		$this->registerTask('new', 'edit');
	}

    public function display($cachable = false, $urlparams = false) {
		$controller = JRequest::getWord('controller', 'mijosql');
		JRequest::setVar('view', $controller);

		parent::display($cachable, $urlparams);
	}
	
    public function edit() {
        JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('view', 'edit');
		JRequest::setVar('edit', true);

		parent::display();
	}
}
