<?php
/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	PayPlans-Installer
* @contact 		support+payplans@readybytes.in
*/

// No direct access.
defined('_JEXEC') or die;

if(!defined('PPINSTALLER_LOADED')){
	define('PPINSTALLER_LOADED', true);
}
else {
	return true;
}

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// Others required library
jimport('joomla.installer.installer');
jimport('joomla.filesystem.archive');



if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}

$version = new JVersion();

if($version->RELEASE==='1.6') define('PPINSTALLER_16', true);
if($version->RELEASE==='1.7') define('PPINSTALLER_17', true);
if($version->RELEASE==='2.5') define('PPINSTALLER_25', true);
if($version->RELEASE >= '3.0') define('PPINSTALLER_30', true);

define('PPINSTALLER_VERSION','3.3');
define('PPINSTALLER_PAYPLANS_VERSION','3.3');
define('PPINSTALLER_PAYPLANS_REVISION','2');

define('PPINSTALLER_PATH',		__DIR__); 

define('PPINSTALLER_HELPER_PATH',		PPINSTALLER_PATH.DS.'helpers');
define('PPINSTALLER_MODELS_PATH',		PPINSTALLER_PATH.DS.'models');
define('PPINSTALLER_LOGGER_PATH',		JPATH_ROOT.DS.'tmp'.DS.'ppinstaller_logs');
define('PPINSTALLER_MIGRATER_PATH',		PPINSTALLER_PATH.DS.'models'.DS.'migrate');
define('PPINSTALLER_CONTROLLERS_PATH',	PPINSTALLER_PATH.DS.'controllers');
define('PPINSTALLER_VIEWS_PATH',		PPINSTALLER_PATH.DS.'views');
define('PPINSTALLER_PRECHECK_PATH',		PPINSTALLER_PATH.DS.'precheck');

// Defing Compression type
define('PPINSTALLER_COMPRESSION_TYPE', 'zip');


// media files
define('PPINSTALLER_MEDIA',	JURI::base().'components'.DS.'com_ppinstaller'.DS.'assets');
define('PPINSTALLER_URL_MEDIA',	JURI::base().'components/com_ppinstaller/assets');
define('PPINSTALLER_STYLE',	PPINSTALLER_URL_MEDIA.'/css/');
define('PPINSTALLER_JS',	PPINSTALLER_URL_MEDIA.'/js/');
define('PPINSTALLER_IMG',	PPINSTALLER_URL_MEDIA.'/images/');

// Error Constant 
define('PPINSTALLER_SUCCESS_LEVEL',		0);
define('PPINSTALLER_WARNING_LEVEL', 	10);
define('PPINSTALLER_ERROR_LEVEL', 		20);
define('PPINSTALLER_CRITICAL_LEVEL',	30);

//Migration Constant
define('PPINSTALLER_BACKUP_CREATED',		10);
define('PPINSTALLER_MIGRATION_START', 		20);
define('PPINSTALLER_MIGRATION_IN_PROCESS',	30);
define('PPINSTALLER_MIGRATION_SUCCESS', 	100);

// General purpose const
define('PPINSTALLER_LIMIT', 			500);
define('PPINSTALLER_CRITICAL_LIMIT', 	100);
define('PPINSTALLER_PATCH_LIMIT', 		20);
// name of session variable
define('PPINSTALLER_REQUIRED_PATCHES', 	'required_patches');	

//load controller
require_once dirname(__FILE__).DS.'controller.php';

$files	=	JFolder::files(PPINSTALLER_CONTROLLERS_PATH,".php$");
foreach($files as $file ){
	$className 	= 'PpinstallerController'.JFile::stripExt($file);
	JLoader::register($className, PPINSTALLER_CONTROLLERS_PATH.DS.$file);
}

require_once dirname(__FILE__).DS.'view.php';
$files	=	JFolder::files(PPINSTALLER_VIEWS_PATH,".php$");
foreach($files as $file ){
	$className 	= 'PpinstallerView'.JFile::stripExt($file);
	JLoader::register($className, PPINSTALLER_VIEWS_PATH.DS.$file);
}

// Load helper files	
$files	=	JFolder::files(PPINSTALLER_HELPER_PATH,".php$");
foreach($files as $file ){
	$className 	= 'PpinstallerHelper'.JFile::stripExt($file);
	JLoader::register($className, PPINSTALLER_HELPER_PATH.DS.$file);
}

// Load Models files
$files	=	JFolder::files(PPINSTALLER_MODELS_PATH,".php$");
foreach($files as $file ){
	$className 	= 'PpinstallerModel'.JFile::stripExt($file);
	JLoader::register($className, PPINSTALLER_MODELS_PATH.DS.$file);
}

JLoader::register('PpinstallerModelBase', PPINSTALLER_MODELS_PATH.DS.'config.php');


//IMP:: use after loaded helper file
$major_version	 = PpinstallerHelperUtils::version_level(PPINSTALLER_PAYPLANS_VERSION , 'major'); 
$minior_version  = PpinstallerHelperUtils::version_level(PPINSTALLER_PAYPLANS_VERSION , 'minor');

define('PPINSTALLER_PAYPLANS_KIT_SUFFIX', $major_version.$minior_version);
JLoader::register('PpinstallerAjaxResponse', PPINSTALLER_HELPER_PATH.DS.'response.php');

define('PPINSTALLER_TMP_PATH',	JPATH_ROOT.DS.'tmp');


//URL to communitcate with S3
define('PPINSTALLER_PPAPPSERVER_URL','http://pub.readybytes.net/ppinstaller/server.json');
define('PPINSTALLER_RELEASE_FILE_URL','http://pub.readybytes.net/ppinstaller/releases.json');
define('PPINSTALLER_INSTRUCTIONS_FILE_URL','http://pub.readybytes.net/ppinstaller/instructions.html');
define('PPINSTALLER_KITS_URL', 'http://pub.readybytes.net/ppinstaller/kits');
