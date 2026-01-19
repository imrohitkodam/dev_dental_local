<?php
/**
* @version		1.0.0
* @package		MijoSQL
* @subpackage	MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceSQL www.joomace.net
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class MijosqlControllerQueries extends MijosqlController {

    public function __construct($default = array()) {
		parent::__construct($default);

        $this->_model = $this->getModel('Queries');
	}
	
    /*public function view() {
   		$view = $this->getView(ucfirst('Queries'), 'html');
		$view->setModel($this->_model, true);
		$view->view();
   	}*/

    public function edit() {
        JRequest::setVar('hidemainmenu', 1);

        $view = $this->getView(ucfirst('Queries'), 'edit');
        $view->setModel($this->_model, true);
        $view->display('edit');
   	}

    public function save() {
   		// Check for request forgeries
   		JRequest::checkToken() or jexit('Invalid Token');

        $post = JRequest::get('post');
        $post['query'] = base64_encode(JRequest::getVar('ja_query', '', 'post', 'string', JREQUEST_ALLOWRAW));
        unset($post['ja_query']);

        if ($this->_model->saveQuery($post)) {
            $msg = JText::_('COM_MIJOSQL_SAVE_TRUE');
        }
        else {
            $msg = JText::_('COM_MIJOSQL_SAVE_FALSE');
        }

   		$this->setRedirect('index.php?option=com_mijosql&controller=queries', $msg);
   	}

    public function cancel() {
   		// Check for request forgeries
   		JRequest::checkToken() or jexit('Invalid Token');

   		$this->setRedirect('index.php?option=com_mijosql&controller=queries');
   	}

    public function remove() {
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        $cid = JRequest::getVar('cid', array(), '', 'array');

        JArrayHelper::toInteger($cid);
        $msg = '';

        for ($i=0, $n=count($cid); $i < $n; $i++) {
            $query =& JTable::getInstance('Query', 'Table');

            if (!$query->delete($cid[$i])) {
                $msg .= $query->getError();
                $tom = "error";
            }
            else {
                $msg = JTEXT::_('COM_MIJOSQL_QUERY_DELETED');
                $tom = "";
            }
        }

        $this->setRedirect('index.php?option=com_mijosql&controller=queries', $msg, $tom);
    }
}