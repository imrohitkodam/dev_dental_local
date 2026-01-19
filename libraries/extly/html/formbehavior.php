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

/**
 * Utility class for form related behaviors
 *
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 * @since       3.0
 */
abstract class EHtmlFormbehavior
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the Chosen JavaScript framework and supporting CSS into the document head
	 *
	 * If debugging mode is on an uncompressed version of Chosen is included for easier debugging.
	 *
	 * @param   string  $selector  Class for Chosen elements.
	 * @param   mixed   $debug     Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function chosen($selector = '.advandedSelect', $debug = null)
	{
		if (isset(self::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		// Add chosen.jquery.js language strings
		JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
		JText::script('JGLOBAL_SELECT_AN_OPTION');
		JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}

		$document = JFactory::getDocument();
		$url = JUri::root();
		$document->addStyleSheet($url . 'libraries/extly/css/chosen.css');

		self::$loaded[__METHOD__][$selector] = true;

		return;
	}
}
