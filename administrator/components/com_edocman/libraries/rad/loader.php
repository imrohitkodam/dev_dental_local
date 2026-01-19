<?php
defined('_JEXEC') or die();
JLoader::registerPrefix('OS', dirname(__FILE__));
JLoader::registerPrefix('EDocman', JPATH_BASE . '/components/com_edocman');
if (JFactory::getApplication()->isAdmin())
{
	JLoader::register('EDocmanHelper', JPATH_ROOT . '/components/com_edocman/helper/helper.php');
	JLoader::register('EDocmanHelperHtml', JPATH_ROOT . '/components/com_edocman/helper/html.php');
	JLoader::register('EDocmanHelperJquery', JPATH_ROOT . '/components/com_edocman/helper/jquery.php');
    JLoader::register('EDocmanHelperBootstrap', JPATH_ROOT . '/components/com_edocman/helper/bootstrap.php');
}
else
{
	JLoader::register('EDocmanTableDocument', JPATH_ROOT . '/administrator/components/com_edocman/table/document.php');
	JLoader::register('EDocmanTableCategory', JPATH_ROOT . '/administrator/components/com_edocman/table/category.php');
	JLoader::register('EDocmanModelUsers', JPATH_ADMINISTRATOR . '/components/com_edocman/model/users.php');
	JLoader::register('EDocmanModelDocuments', JPATH_ADMINISTRATOR . '/components/com_edocman/model/documents.php');
	JLoader::register('EDocmanModelCommonDocument', JPATH_ADMINISTRATOR . '/components/com_edocman/model/common/document.php');
}

if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
{
	$db = JFactory::getDbo();
	$db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
	$db->execute();
}