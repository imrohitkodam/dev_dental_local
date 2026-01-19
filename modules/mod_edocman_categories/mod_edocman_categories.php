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
require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
require_once __DIR__ . '/helper.php';
EDocmanHelper::loadLanguage();

$numberCategories = $params->get('number_categories', 0);
$show_sub		  = $params->get('show_sub',1);
JLoader::register('EDocmanModelCategories', JPATH_ROOT . '/components/com_edocman/model/categories.php');
$app			  = JFactory::getApplication();
$db				  = JFactory::getDbo();
$user			  = JFactory::getUser();
$usergroup        = $user->groups;
$usergroupArr     = array();
$usergroupSql     = "";
if(count($usergroup) > 0){
    foreach ($usergroup as $group){
        $usergroupArr[] = " (groups='$group' OR groups LIKE '$group,%' OR groups LIKE '%,$group,%' OR groups LIKE '%,$group') ";
    }
    $usergroupSql = implode(" OR ",$usergroupArr);
    $usergroupSql = " tbl.id in (Select item_id from #__edocman_levels where $usergroupSql) ";
    $usergroupSql = " OR (tbl.user_ids = '' AND tbl.accesspicker = '1' AND $usergroupSql ) ";
}
$userId			  = $user->get('id');
$config			  = EDocmanHelper::getConfig();
$query			  = $db->getQuery(true);

$CategoriesId     = $params->get('edocmancategory',0);
$arrayId = array();
if($CategoriesId){
	$arrayId[] = $CategoriesId;
    ModEdocmanCategoriesHelper::getCategories($CategoriesId,$arrayId);
}
$query->select('id, title, parent_id')
    ->from('#__edocman_categories AS tbl')
    ->where('published = 1');
	if (!$user->authorise('core.admin'))
	{
		$query->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
	}
    $query->order('ordering');
if(count($arrayId)){
        $query->where("id IN (".implode(',',$arrayId).")");
}
$parentId = 0;
$catId    = 0;

$db->setQuery($query);
$rows     = $db->loadObjectList();

$children = array();
// first pass - collect children
if (count($rows))
{
    foreach ($rows as $v)
    {
		if($v->id == $CategoriesId){
			$v->parent_id=0;
		}
        $pt   = $v->parent_id;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
    }
}

if($show_sub == 1){
	$max_level  = 9999;
	$level		= 0;
}else{
	$max_level  = 1;
	$level		= 1;
}
$list      = JHtml::_('menu.treerecurse', $parentId, '', array(), $children, $max_level,$level);

if($numberCategories > 0){
    $list = array_slice($list,0,$numberCategories);
}

$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EDocmanHelper::getItemid();
}
require(JModuleHelper::getLayoutPath('mod_edocman_categories', 'default'));
?>