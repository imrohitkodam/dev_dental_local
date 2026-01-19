<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

include_once __DIR__.DIRECTORY_SEPARATOR.'router'.DIRECTORY_SEPARATOR.'base.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'router'.DIRECTORY_SEPARATOR.'router.php';

function AcymailingBuildRoute(&$query)
{
    $router = new AcymailingRouter();

    return $router->build($query);
}

function AcymailingParseRoute($segments)
{
    $router = new AcymailingRouter();

    return $router->parse($segments);
}

