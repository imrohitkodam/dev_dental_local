<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

// If Payplans System Plugin disabled then do nothing
$systemPlugin = JPluginHelper::isEnabled('system','payplans');
if (!$systemPlugin){
	return true;
}

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

if (!PP::isFoundryEnabled()) {
	return;
}

PP::initFoundry();

$planIds = $params->get('plan_ids', '');
$returnUrl = $params->get('return_url', '');

// Load up our library
$modules = PP::modules($module);

JLoader::register('ModPayplansPlanHelper', __DIR__ . '/helper.php');

// Define plan column in row
$columns = ModPayplansPlanHelper::getPlancolumns();

list($plans, $groups) = ModPayplansPlanHelper::getPlans($planIds);
$renderBadgeStyleCss = ModPayplansPlanHelper::renderBadgeStyleCss($plans, $groups);

// Set the cancel and return url shown on checkout page
if ($returnUrl) {
	$returnUrl = base64_encode(JRoute::_($returnUrl));
} else {
	// If return url not set the set the current url as return url
	$returnUrl = base64_encode(PPR::getCurrentURI());
}

$fd = PP::themes()->fd;

require_once JModuleHelper::getLayoutPath('mod_payplans_plan', 'default');