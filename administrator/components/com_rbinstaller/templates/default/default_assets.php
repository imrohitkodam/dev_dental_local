<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Back-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

Rb_HelperTemplate::loadSetupEnv();

Rb_HelperTemplate::loadMedia(array('jquery', 'bootstrap', 'rb', 'font-awesome', 'angular'));
Rb_Html::script('com_rbinstaller/admin.js');
Rb_Html::script('com_rbinstaller/angular/ui-router.js');
Rb_Html::script('com_rbinstaller/angular/cookie.js');
Rb_Html::script('com_rbinstaller/angular/config.js');
Rb_Html::script('com_rbinstaller/angular/controller.js');
Rb_Html::script('com_rbinstaller/angular/filters.js');