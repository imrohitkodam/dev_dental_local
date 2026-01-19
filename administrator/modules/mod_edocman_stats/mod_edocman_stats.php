<?php
/**
 * @version    1.0
 * @package    Edocman
 * @author     Ossolution https://www.joomdonation.com
 * @copyright  Copyright (c) 2012 - 2018 Ossolution Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

$user = JFactory::getUser();

require_once (dirname(__FILE__).DS.'helper.php');

if ($params->get('latestItems', 1))
{
	$latestItems = modEdocmanStatsHelper::getLatestItems();
}
if ($params->get('popularItems', 1))
{
	$popularItems = modEdocmanStatsHelper::getPopularItems();
}
if ($params->get('mostDownloadItems', 1))
{
	$mostDownloadedItems = modEdocmanStatsHelper::getMostDownloadItems();
}
if ($params->get('statistics', 1))
{
	$statistics = modEdocmanStatsHelper::getStatistics();
}

require (JModuleHelper::getLayoutPath('mod_edocman_stats'));
