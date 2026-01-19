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

class operatorsinType extends acymailingClass{
	var $js = '';
    public $values;

    function __construct(){
		parent::__construct();

		$this->values = array();

		$this->values[] = acymailing_selectOption('IN', acymailing_translation('ACY_IN'));
		$this->values[] = acymailing_selectOption('NOT IN', acymailing_translation('ACY_NOT_IN'));

	}

	function display($map){
		return acymailing_select($this->values, $map, 'class="inputbox" size="1" style="width:120px;" '.$this->js, 'value', 'text');
	}

}

