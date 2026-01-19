<?php
/**
 * @author Joomla! Extensions Store
 * @package JMAP::modules::mod_jmap
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Module for sitemap footer navigation
 *
 * @author Joomla! Extensions Store
 * @package JMAP::modules::mod_jmap
 * @since 3.0
 */
jimport('joomla.filesystem.file');

// Include the syndicate functions only once
if($params->get('height_auto', 1)) {
	require_once __DIR__ . '/helper.php';
	ModJMapHelper::jmapInjectAutoHeightScript();
}

$scroll = htmlspecialchars($params->get('scrolling'));
$width	= htmlspecialchars($params->get('width'));
if(stripos($width, 'px') === false && stripos($width, '%') === false) {
	$width .= 'px';
}
$height = htmlspecialchars($params->get('height'));
$height = preg_replace('/(%|px)/i', '', $height);

$onLoad = $params->get('height_auto', 1) ? 'onload="jmapIFrameAutoHeight(\'jmap_sitemap_nav_' . $module->id . '\')"' : '';
$dataset = (int)$params->get('dataset', null);
$dataset = $dataset ? '&amp;dataset=' . $dataset : ''; 
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

// Check for multilanguage
$app = JFactory::getApplication();
$currentLanguageQueryString = null;
$currentSefLanguage = null;
if ($app->isSite()) {
	$multilangEnabled = $app->getLanguageFilter();
	$currentSefLanguage = $multilangEnabled ?  JFactory::getLanguage()->getLocale() : null;
	if(is_array($currentSefLanguage)) {
		$partialSef = explode('_', $currentSefLanguage[2]);
		$sefLang = array_shift($partialSef);
		$currentLanguageQueryString = '&amp;lang=' . $sefLang;
		$currentSefLanguage = $sefLang . '/';
	}
}

// Standard routing, full raw query string
$targetIFrameUrl = JUri::base() . 'index.php?option=com_jmap&amp;view=sitemap&amp;tmpl=component&amp;jmap_module=' . $module->id . $dataset . $currentLanguageQueryString;

// Legacy routing /en, /de, etc
if($params->get('legacy_routing', 0)) {
	// Try to check for an active htaccess file
	$index = null;
	// Joomla 3.2+ support
	if(method_exists($app, 'get')) {
		if(!$app->get ( 'sef_rewrite' )) {
			$index = 'index.php/';
		}
	}
	$targetIFrameUrl = JUri::base() . $index . $currentSefLanguage . '?option=com_jmap&amp;view=sitemap&amp;tmpl=component&amp;jmap_module=' . $module->id . $dataset;
}

if($params->get('module_rendering_mode', 'iframe') == 'iframe') {
	// Module iframe rendering
	require JModuleHelper::getLayoutPath('mod_jmap', $params->get('layout', 'default'));
} else {
	/**
	 * Component execute and fetch
	 * Load language files
	 * Auto loader setup
	 * Register autoloader prefix
	 */
	$jLang = JFactory::getLanguage ();
	$jLang->load ( 'com_jmap', JPATH_ROOT . '/components/com_jmap', 'en-GB', true, true );
	if ($jLang->getTag () != 'en-GB') {
		$jLang->load ( 'com_jmap', JPATH_SITE, null, true, false );
		$jLang->load ( 'com_jmap', JPATH_SITE . '/components/com_jmap', null, true, false );
	}
	
	require_once JPATH_ADMINISTRATOR . '/components/com_jmap/framework/loader.php';
	JMapLoader::setup();
	JMapLoader::registerPrefix('JMap', JPATH_ADMINISTRATOR . '/components/com_jmap/framework');
	
	// Instantiate model
	JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_jmap/models', 'JMapModel');
	$sitemapModel = JModelLegacy::getInstance('Sitemap', 'JMapModel', array('document_format'=>'html', 'jmap_module'=>$module->id));
	$sitemapModel->setState('format', 'html');
	$cparams = $sitemapModel->getComponentParams();
	$cparams->set('show_title', 0);
	
	require_once JPATH_ROOT . '/components/com_jmap/views/sitemap/view.html.php';
	$view = new JMapViewSitemap();
	$view->setModel($sitemapModel, true);
	$view->addTemplatePath(JPATH_ROOT . '/components/com_jmap/views/sitemap/tmpl');
	$contents = $view->display();
	
	echo $contents;
}