<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

// Include main engine
$engine = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
$exists = JFile::exists($engine);

if (!$exists) {
	return;
}

// Include the engine file.
require_once($engine);

// Load up helper file
require_once(__DIR__ . '/helper.php');

$currentUrl = EasySocialModStreamAnywhereHelper::getCurrentRawUrl();

// always remove the trailing slash
$currentUrl = $currentUrl === '/' ? $currentUrl : rtrim($currentUrl,"/");

// Get the current logged in user object
$my = ES::user();

$lib = ES::modules($module);
$lib->renderComponentScripts();

// Module settings
$total = $params->get('total', 10);

// stream options
$options = array('anywhereId' => $currentUrl, 'customlimit' => $total, 'guest' => true, 'ignoreUser' => true);

// Get the layout to use.
$stream = ES::stream();
$stream->get($options);

if ($my->id && $params->get('story_form', true)) {
	$story = ES::story(SOCIAL_TYPE_USER);
	$story->setTarget($my->id);
	$story->setAnywhereId($currentUrl);
	$stream->story = $story;
}

$readmoreURL = '';
$readmoreText = '';

if ($my->id) {
	$readmoreURL = ESR::dashboard(array(), false);
	$readmoreText = 'MOD_EASYSOCIAL_STREAM_ANYWHERE_GOTO_DASHBOARD';
} else {
	$readmoreURL = ESR::login(array(), false);
	$readmoreText = 'MOD_EASYSOCIAL_STREAM_LOGIN';
}

require($lib->getLayout());
