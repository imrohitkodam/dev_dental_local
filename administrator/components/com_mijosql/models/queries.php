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
defined('_JEXEC') or die( 'Restricted access' );

class MijosqlModelQueries extends MijosqlModel {

	var $_query = null;
	var $_data = null;
	var $_total = null;
	var $_pagination = null;

	function __construct() {
		parent::__construct();

		$this->mainframe = JFactory::getApplication();
		$this->option = JRequest::getWord('option');

		// Get the pagination request variables
		$limit		= $this->mainframe->getUserStateFromRequest($this->option.'.queries.limit', 'limit', $this->mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $this->mainframe->getUserStateFromRequest($this->option.'.queries.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($this->option.'.queries.limit', $limit);
		$this->setState($this->option.'.queries.limitstart', $limitstart);
		
		$this->_buildViewQuery();
	}
	
	function getData() {
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->_query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}
	
	function getTotal()	{
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_query);
		}

		return $this->_total;
	}

	function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($this->option.'.queries.limitstart'), $this->getState($this->option.'.queries.limit'));
		}

		return $this->_pagination;
	}
	
	function _buildViewQuery() {
		if (empty($this->_query)) {
			$where		= $this->_buildViewWhere();
			$orderby	= $this->_buildViewOrderBy();
			
			$this->_query = "SELECT * FROM #__mijosql_queries". $where . $orderby;
		}

		return $this->_query;
	}
	
	function _buildViewWhere() {
		$db	=& JFactory::getDBO();
		
		$search	= $this->mainframe->getUserStateFromRequest($this->option.'.queries.search', 'search', '', 'string');
		$search	= JString::strtolower($search);

		$where = array();

		if ($search) {
			$where[] = 'LOWER(title) LIKE '.$db->Quote('%'.$db->getEscaped($search, true).'%', false);
		}
		
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

		return $where;
	}

    function _buildViewOrderBy()	{
        $filter_order		= $this->mainframe->getUserStateFromRequest($this->option.'.queries.filter_order',		'filter_order',		'title',	'string');
        $filter_order_Dir	= $this->mainframe->getUserStateFromRequest($this->option.'.queries.filter_order_Dir',	'filter_order_Dir',	'',			'word');

        $orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

        return $orderby;
    }

    function getQueryData() {
        $query = JRequest::getString('ja_query', '', 'get');
        if (!empty($query)) {
            $data = new stdClass();
            $data->id = 0;
            $data->title = '';
            $data->query = base64_decode($query);
        }
        else {
            $cid = JRequest::getVar('cid', array(0), 'method', 'array');
            $id = $cid[0];

            $data =& JTable::getInstance('Query', 'Table');
            $data->load($id);

            $data->query = base64_decode($data->query);
        }

        return $data;
    }

    function saveQuery($post) {
        $row =& JTable::getInstance('Query', 'Table');

        // Bind the form fields to the web link table
        if (!$row->bind($post)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Make sure the web link table is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;

        return;
    }
}