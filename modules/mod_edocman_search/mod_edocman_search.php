<?php
/**
 * @version        1.7.2
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

error_reporting(0);
require_once(dirname(__FILE__) . '/helper.php');
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemId				= $params->get('item_id');
if (!$itemId)
{
	$itemId			= EDocmanHelper::getItemid();
}
$default_category_id = $params->get('default_category_id',0);
$input_style		= $params->get('input_style','input-medium');
$file_type          = $params->get('file_type','');
if($file_type != ""){
	$file_type_array = explode(",",$file_type);
}
EDocmanHelper::loadLanguage();

$input				= JFactory::getApplication()->input;
$categoryId			= $input->getInt('filter_category_id', 0);
$text				= $input->getString('filter_search');
$defaultText		= JText::_('EDOCMAN_SEARCH_WORD');
if (!$text)
{
	$text			= $defaultText;
}
$text				= htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
$layout				= $params->get('layout_type', 'default');
$show_category		= $params->get('show_category',0);
$view				= $input->getString('view','');
if($view == "search"){
	$filter_filetype    = $input->getString('filter_filetype','string','');
	if($filter_filetype != ""){
		$filter_filetype = explode("-",$filter_filetype);
	}
	$fileType			= $input->get('fileType',$filter_filetype,'array');
}else{
	$fileType			= $input->get('fileType',$file_type_array,'array');
}
require(JModuleHelper::getLayoutPath('mod_edocman_search'));