<?php
/*------------------------------------------------------------------------
# mod_edocmancalendar - Edocman Calendar
# ------------------------------------------------------------------------
# author    Ossolution
# Copyright (C) 2018 www.joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.joomdonation.com
# Technical Support:  Forum - https://www.joomdonation.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
error_reporting(E_ERROR | E_PARSE);
// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
$cal = modEdocmanCalendarHelper::getCal($params);
require JModuleHelper::getLayoutPath('mod_edocmancalendar');