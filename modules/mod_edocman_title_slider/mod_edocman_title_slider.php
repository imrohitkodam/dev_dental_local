<?php
/**
 * Edocman title slider
 *
 * @package 	Edocman title slider
 * @subpackage 	Edocman title slider
 * @version   	1.0
 * @author    	Dang Thuc Dam
 * @copyright 	Copyright (C) 2010 - 2016 www.gopiplus.com, LLC
 * @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * http://www.joomdonation.com/
 */
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once(dirname(__FILE__).'/helper.php');
require_once(JPATH_ROOT.'/components/com_edocman/helper/helper.php');
require_once(JPATH_ROOT.'/components/com_edocman/helper/route.php');

// Load article list from the database
$items = modEdocmanSlider::getDocumentList($params);

// Load JQuery and cycle javascript.
modEdocmanSlider::loadScripts($params);

// Load slider in the page.
require(JModuleHelper::getLayoutPath('mod_edocman_title_slider'));
?>