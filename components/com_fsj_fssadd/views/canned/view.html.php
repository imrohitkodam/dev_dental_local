<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'helper.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'task.php');

class FSJ_FSSADDViewCanned extends FSSView
{
	function display($tpl = NULL)
	{
		$layout = FSS_Input::getCmd('layout', 'list');	
		$layout = preg_replace("/[^a-z0-9\_]/", '', $layout);
		
		$file = JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'layout.' . $layout . '.php';
		if (!file_exists($file))
		{
			$file = JPATH_SITE.DS.'components'.DS.'com_fsj_fssadd'.DS.'views'.DS.'canned'.DS.'layout.list.php';
			$layout = "list";
		}
		require_once($file);
		
		$class_name = "FSJ_FSSADDViewCanned_" . $layout;
		
		$layout_handler = new $class_name();
		$layout_handler->setLayout($layout);
		$layout_handler->_models = $this->_models;
		$layout_handler->_defaultModel = $this->_defaultModel;
		if (!$layout_handler->init()) return false;

		$layout_handler->display();
	}
	
	public function getName()
	{
		$this->_name = "canned";
		return $this->_name;
	}
	
	function _display($tpl = NULL)
	{
		parent::display($tpl);	
	}
	
	function init()
	{
		if (Task_Helper::HandleTasks($this)) return false;
		
		return true;
	}
}
