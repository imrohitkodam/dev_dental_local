<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// if RBInstaller already loaded, then do not load it again
if(defined('RBINSTALLER_CORE_LOADED')){
	return;
}

define('RBINSTALLER_CORE_LOADED', true);

// include defines
include_once dirname(__FILE__).'/defines.php';

//load core
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/base',		     '',		 'RBInstaller');

Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/models',		'Model',	 'RBInstaller');
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/models',		'Modelform', 'RBInstaller');

//html
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/html/html',		'Html',		 'RBInstaller');
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/html/fields',	'FormField', 'RBInstaller');

Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/tables',		'Table',	 'RBInstaller');
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/helpers',		'Helper',	 'RBInstaller');
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_CORE.'/compatibilities','Compatibility',	 'RBInstaller');

// admin
Rb_HelperLoader::addAutoLoadFolder(RBINSTALLER_PATH_ADMIN.'/controllers',	'Controller',		'RBInstallerAdmin');
Rb_HelperLoader::addAutoLoadViews(RBINSTALLER_PATH_ADMIN.'/views', RB_REQUEST_DOCUMENT_FORMAT, 'RBInstallerAdmin');

// include the event file so that events can be registered
require_once RBINSTALLER_PATH_CORE.'/base/event.php';