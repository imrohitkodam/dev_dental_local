<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (C) 2007 - 2017 Extly, CB. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// Load F0F
if (!defined('F0F_INCLUDED'))
{
	include_once JPATH_LIBRARIES . '/f0f/include.php';
}

if (!defined('F0F_INCLUDED'))
{
	JFactory::getApplication()->enqueueMessage(
		'Your Extly Framework installation is broken; please re-install. Alternatively, extract the installation archive and copy the f0f directory inside your site\'s libraries directory.',
		'error'
	);
}

if (!defined('EXTLY_VERSION'))
{
	/**
	 * @name EXTLY_VERSION
	 */
	define('EXTLY_VERSION', '5.5.1');

	// CSS Styling
	define('EXTLY_BASE', '5_4_0');

	defined('DS') || define('DS', DIRECTORY_SEPARATOR);
	defined('EPATH_LIBRARY') || define('EPATH_LIBRARY', JPATH_LIBRARIES . '/extly');
	defined('EJSON_START') || define('EJSON_START', '@EXTLYSTART@');
	defined('EJSON_END') || define('EJSON_END', '@EXTLYEND@');

	defined('EXTLY_J3') || define('EXTLY_J3', (version_compare(JVERSION, '3.0', 'gt')));
	defined('EXTLY_J35') || define('EXTLY_J35', (version_compare(JVERSION, '3.5', 'ge')));
	defined('EXTLY_J25') || define('EXTLY_J25', !EXTLY_J3);
}

JLoader::register('Extly', EPATH_LIBRARY . '/core/extly.php');
JLoader::register('ExtlyController', EPATH_LIBRARY . '/core/econtroller.php');
JLoader::register('ETable', EPATH_LIBRARY . '/core/etable.php');
JLoader::register('ELog', EPATH_LIBRARY . '/core/elog.php');
JLoader::register('EParameter', EPATH_LIBRARY . '/core/eparameter.php');

JLoader::register('EForm', EPATH_LIBRARY . '/form/eform.php');

JLoader::register('EHtmlGrid', EPATH_LIBRARY . '/html/egrid.php');
JLoader::register('EHtml', EPATH_LIBRARY . '/html/ehtml.php');
JLoader::register('EHtmlFormbehavior', EPATH_LIBRARY . '/html/formbehavior.php');
JLoader::register('EHtmlSelect', EPATH_LIBRARY . '/html/html/eselect.php');

JLoader::register('EExtensionHelper', EPATH_LIBRARY . '/helpers/extension.php');
JLoader::register('ExtlyModelExtensions', EPATH_LIBRARY . '/models/extensions.php');
JLoader::register('ExtlyTableExtension', EPATH_LIBRARY . '/tables/extension.php');

JLoader::register('EDbProxyHelper', EPATH_LIBRARY . '/helpers/dbproxy.php');

/**
 * This is the base class for the Extlyframework.
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class Extlyframework
{
}
