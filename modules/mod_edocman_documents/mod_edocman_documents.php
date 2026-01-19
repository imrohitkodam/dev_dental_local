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
require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';
require_once JPATH_ROOT.'/components/com_edocman/helper/file.class.php' ;
EDocmanHelper::loadLanguage();
$user   = JFactory::getUser();
$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EdocmanHelper::getItemid();
}
$moduleclass_sfx			= htmlspecialchars($params->get('moduleclass_sfx'));
$numberDocuments			= $params->get('number_documents', 6);
$categoryIds				= $params->get('category_ids', '');
$type						= $params->get('module_type', 1);
$linkType					= $params->get('link_type', 0);
$includeChildrenCategories  = $params->get('include_children', 0);
$newpage					= $params->get('newpage',0);
$showdownload               = $params->get('showdownload',1);
$showfilesize               = $params->get('showfilesize',1);
if($newpage == 1){
	$target = "target='_blank'";
}else{
	$target = "";
}
$config						= EDocmanHelper::getConfig();
$document                   = JFactory::getDocument();
$document->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/font.css');
JLoader::register('EDocmanModelCategory', JPATH_ROOT . '/components/com_edocman/model/category.php');
$model = OSModel::getInstance('Category', 'EDocmanModel')
	->reset()
	//->filter_category_ids($categoryIds)
	->limitstart(0)
	->limit($numberDocuments)
	->filter_order_Dir('DESC');
$model->set('filter_category_ids',$categoryIds);
if ($includeChildrenCategories)
{
	$model->setIncludeChildren(true);
}

switch ($type)
{
	case 1:
		$model->filter_order('tbl.created_time');
		$layout = 'lastest';
		break;
	case 2:
		$model->filter_order('tbl.hits');
		$layout = 'top_hits';
		break;
	case 3:
		$model->filter_order('tbl.downloads');
		$layout = 'top_downloads';
		break;
	case 4:
		$model->filter_order('tbl.title');
		$model->filter_order_Dir('asc');
		$layout = 'alphabetical';
		break;
    case 5:
        $model->filter_order('tbl.ordering');
        $model->filter_order_Dir('asc');
        $layout = 'ordering';
        break;
}
$rows        = $model->getData();
$extsForView = explode(',', $config->exts_for_view);
if ($linkType)
{
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row              = $rows[$i];
		$row->canDownload = $user->authorise('edocman.download', 'com_edocman.document.' . $row->id);
		if ($linkType == 2)
		{
			// Check to see whether users can view the documentation
			$fileName = $row->filename;
			$fileExt  = strtolower(JFile::getExt($fileName));
			if (in_array($fileExt, $extsForView))
			{
				$row->canView = 1;
			}
			else
			{
				$row->canView = 0;
			}
		}
	}
}
require(JModuleHelper::getLayoutPath('mod_edocman_documents', $layout));
?>