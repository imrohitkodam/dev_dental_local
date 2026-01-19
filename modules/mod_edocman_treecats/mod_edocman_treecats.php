<?php
/**
 * @version        1.0
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

error_reporting(E_ERROR || E_PARSE || E_CORE_ERROR);
require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
require_once __DIR__ . '/helper.php';
$document		  = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/modules/mod_edocman_treecats/assets/style.css');
$document->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/font.css');
EDocmanHelper::loadLanguage();

$show_sub		  = $params->get('show_sub',1);
JLoader::register('EDocmanModelCategories', JPATH_ROOT . '/components/com_edocman/model/categories.php');
$app			  = JFactory::getApplication();
$db				  = JFactory::getDbo();
$user			  = JFactory::getUser();
$usergroup        = $user->groups;
$usergroupArr     = array();
$usergroupSql     = "";
if(count($usergroup) > 0)
{
    foreach ($usergroup as $group)
    {
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

$category_id	  = JFactory::getApplication()->input->getInt('id',0);
$parentArr		  = array();
ModEdocmanTreeCatsHelper::getParentCategory($category_id, $parentArr);
$list = ModEdocmanTreeCatsHelper::getCategories($CategoriesId, $usergroupSql , $returnCategories );

$query->select('level')->from('#__edocman_categories')->order('level desc');
$db->setQuery($query);
$max_level = $db->loadResult();

$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EDocmanHelper::getItemid();
}
require(JModuleHelper::getLayoutPath('mod_edocman_treecats', 'default'));
?>