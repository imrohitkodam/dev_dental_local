<?php
/**
 * @version        1.0.0
 * @package        Joomla
 * @subpackage     Edocman
 * @author         
 * @copyright      Copyright (C) 2011 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

error_reporting(0);
$db = JFactory::getDbo();
$session  = JFactory::getSession();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_edocman_filtercategories/asset/style.css');
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
require_once JPATH_ROOT . '/components/com_edocman/helper/bootstrap.php';
$config = EDocmanHelper::getConfig();
$bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemId				= $params->get('item_id');
$maxlevel           = $params->get('maxlevel',0);
if (!$itemId)
{
	$itemId			= EDocmanHelper::getItemid();
}
$CatArr[] = JHTML::_('select.option','','Any');
$query = $db->getQuery(true);
$query->select("id AS value,title AS text")->from("#__edocman_categories")->where("level=1 AND published = 1");
$db->setQuery($query);
$listCategories = $db->loadObjectList();
$CatArr = array_merge($CatArr,$listCategories);
$listCat = JHtml::_('select.genericlist',$CatArr, 'categoriesId0','class="input-medium CategoriesId" data-level="0" onchange=getListCategories(this.value,this.getAttribute("data-level"))','value','text');
require(JModuleHelper::getLayoutPath('mod_edocman_filtercategories'));