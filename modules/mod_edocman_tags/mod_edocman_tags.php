<?php
/**
 * @version        1.7.1
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

error_reporting(E_ERROR || E_PARSE || E_CORE_ERROR);
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
EDocmanHelper::loadLanguage();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_edocman_tags/asset/style.css');
$numberCategories = $params->get('number_tags', 5);
$layout           = $params->get('layouts', 'default');
$moduleclass      = $params->get('moduleclass_sfx', 'sfx');
$db				  = JFactory::getDbo();
$query			  = $db->getQuery(true);
$query->select('*')->from('#__edocman_tags');
$db->setQuery($query,0,$numberCategories);
$tags = $db->loadObjectList();
$filter_tag = JFactory::getApplication()->input->getString('filter_tag','');
//get Total Documents
$query->clear()->select('COUNT(document_id),tag_id')->from('#__edocman_document_tags')->group('tag_id');
$db->setQuery($query);
$result = $db->loadAssocList('tag_id','COUNT(document_id)');
$optionArr[] = JHTML::_('select.option', '', JText::_('Select Tag'));
if(count($tags)){
    foreach($tags AS $tag){
        $tag->total = (int)$result[$tag->id];
        $optionArr[] = JHTML::_('select.option', $tag->tag, $tag->tag.'('.$tag->total.')');
    }
}
$dropdown = JHTML::_('select.genericlist',$optionArr,'filter_tag','class="input-medium" onchange="this.form.submit();"','value','text',$filter_tag);
$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EDocmanHelper::getItemid();
}
require(JModuleHelper::getLayoutPath('mod_edocman_tags', $layout));
?>