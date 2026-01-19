<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php
include(ACYMAILING_BACK.'views'.DS.'diagram'.DS.'view.html.php');

class FrontdiagramViewFrontdiagram extends DiagramViewDiagram{
	var $ctrl = 'frontdiagram';
    public $Itemid;

    function display($tpl = null){
		global $Itemid;
		$this->Itemid = $Itemid;
		parent::display($tpl);
	}
}

