<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBINSTALLER
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// If file is already included
if(defined('RBINSTALLER_SITE_DEFINED')){
	return;
}

define('RBINSTALLER_COMPONENT_NAME', 'rbinstaller');


// define versions
define('RBINSTALLER_VERSION', '1.4.6');

//shared paths
define('RBINSTALLER_PATH_CORE',				dirname(__FILE__));

// back-end
define('RBINSTALLER_PATH_ADMIN', 				JPATH_ADMINISTRATOR.'/components/com_'.RBINSTALLER_COMPONENT_NAME);
define('RBINSTALLER_PATH_ADMIN_CONTROLLER',		RBINSTALLER_PATH_ADMIN.'/controllers');
define('RBINSTALLER_PATH_ADMIN_VIEW',			RBINSTALLER_PATH_ADMIN.'/views');
define('RBINSTALLER_PATH_ADMIN_TEMPLATE',		RBINSTALLER_PATH_ADMIN.'/templates');
define('RBINSTALLER_PATH_SITE_TEMPLATE',		RBINSTALLER_PATH_ADMIN.'/templates');
define('RBINSTALLER_PATH_CORE_MEDIA',			dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/media/com_'.RBINSTALLER_COMPONENT_NAME);
define('RBINSTALLER_PATH_CORE_MEDIA_DATA',		RBINSTALLER_PATH_CORE_MEDIA.'/data');

// object to identify extension, create once, so same can be consumed by constructors
Rb_Extension::getInstance(RBINSTALLER_COMPONENT_NAME, array('prefix_css'=>'rbinstaller'));