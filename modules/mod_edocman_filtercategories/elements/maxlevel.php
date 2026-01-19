<?php
/*------------------------------------------------------------------------
# maxlevel.php - mod_edocman_categories_search
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2017 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
class JFormFieldMaxlevel extends JFormField
{
	var	$_name = 'Maxlevel';
	function getInput()
	{    
		$typeArr[] = JHTML::_('select.option',0,'Any');
       	$db = JFactory::getDbo();
       	$db->setQuery("SELECT DISTINCT(level) FROM #__edocman_categories");
       	$LevelObjects = $db->loadColumn();
		foreach ($LevelObjects AS $LevelObject){
				$typeArr[] = JHTML::_('select.option',$LevelObject,$LevelObject);
		}
		return JHtml::_('select.genericlist',$typeArr, 'jform[params][maxlevel]', array(
		    'option.text.toHtml' => false ,
		    'option.value' => 'value', 
		    'option.text' => 'text', 
		    'list.attr' => ' class="input-large" ',
		    'list.select' => $this->value    		        		
		));	
	}
	
}