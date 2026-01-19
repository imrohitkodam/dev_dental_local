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

define('_JEXEC', 1);

// No direct access.
defined('_JEXEC') or die;

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..' );

if (file_exists(JPATH_BASE . '/defines.php'))
{
	include_once JPATH_BASE . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$MROOTPATH = dirname(dirname(__FILE__)); // root path of this module.
define('MROOTPATH', str_replace("\\", '/', $MROOTPATH));
$JROOTPATH = dirname(dirname(dirname(dirname(__FILE__))));
define('JROOTPATH', $JROOTPATH); // root path of Joomla.

$app = JFactory::getApplication('site');
$app->initialise();

class ILanguage extends JLanguage{
	/**
	 * Constructor activating the default information of the language.
	 *
	 * @param   string   $lang   The language
	 * @param   boolean  $debug  Indicates if language debugging is enabled.
	 *
	 * @since   11.1
	 */
	public function __construct($lang = null, $debug = false)
	{
		$this->strings = array();

		if ($lang == null)
		{
			$lang = $this->default;
		}

		$this->setLanguage($lang);
		$this->setDebug($debug);

		$filename = JROOTPATH . "/language/overrides/$lang.override.ini";
		$filename = str_replace("\\", '/', $filename);

		if (file_exists($filename) && $contents = $this->parse($filename))
		{
			if (is_array($contents))
			{
				// Sort the underlying heap by key values to optimize merging
				ksort($contents, SORT_STRING);
				$this->override = $contents;
			}

			unset($contents);
		}

		// Look for a language specific localise class
		$class = str_replace('-', '_', $lang . 'Localise');
		$paths = array();

		if (defined('JROOTPATH'))
		{
			// Note: Manual indexing to enforce load order.
			$paths[0] = JROOTPATH . "/language/overrides/$lang.localise.php";
			$paths[2] = JROOTPATH . "/language/$lang/$lang.localise.php";
		}

		if (false && defined('JPATH_ADMINISTRATOR'))
		{
			// Note: Manual indexing to enforce load order.
			$paths[1] = JPATH_ADMINISTRATOR . "/language/overrides/$lang.localise.php";
			$paths[3] = JPATH_ADMINISTRATOR . "/language/$lang/$lang.localise.php";
		}

		ksort($paths);
		$path = reset($paths);

		while (!class_exists($class) && $path)
		{
			if (file_exists($path))
			{
				require_once $path;
			}

			$path = next($paths);
		}

		if (class_exists($class))
		{
			/* Class exists. Try to find
			 * -a transliterate method,
			 * -a getPluralSuffixes method,
			 * -a getIgnoredSearchWords method
			 * -a getLowerLimitSearchWord method
			 * -a getUpperLimitSearchWord method
			 * -a getSearchDisplayCharactersNumber method
			 */
			if (method_exists($class, 'transliterate'))
			{
				$this->transliterator = array($class, 'transliterate');
			}

			if (method_exists($class, 'getPluralSuffixes'))
			{
				$this->pluralSuffixesCallback = array($class, 'getPluralSuffixes');
			}

			if (method_exists($class, 'getIgnoredSearchWords'))
			{
				$this->ignoredSearchWordsCallback = array($class, 'getIgnoredSearchWords');
			}

			if (method_exists($class, 'getLowerLimitSearchWord'))
			{
				$this->lowerLimitSearchWordCallback = array($class, 'getLowerLimitSearchWord');
			}

			if (method_exists($class, 'getUpperLimitSearchWord'))
			{
				$this->upperLimitSearchWordCallback = array($class, 'getUpperLimitSearchWord');
			}

			if (method_exists($class, 'getSearchDisplayedCharactersNumber'))
			{
				$this->searchDisplayedCharactersNumberCallback = array($class, 'getSearchDisplayedCharactersNumber');
			}
		}

		$this->load();
	}
	
	/**
	 * Loads a single language file and appends the results to the existing strings
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded.
	 * @param   string   $basePath   The basepath to use.
	 * @param   string   $lang       The language to load, default null for the current language.
	 * @param   boolean  $reload     Flag that will force a language to be reloaded if set to true.
	 * @param   boolean  $default    Flag that force the default language to be loaded if the current does not exist.
	 *
	 * @return  boolean  True if the file has successfully loaded.
	 *
	 * @since   11.1
	 */
	public function load($extension = 'joomla', $basePath = JPATH_BASE, $lang = null, $reload = false, $default = true)
	{
		// Load the default language first if we're not debugging and a non-default language is requested to be loaded
		// with $default set to true
		if (!$this->debug && ($lang != $this->default) && $default)
		{
			$this->load($extension, $basePath, $this->default, false, true);
		}

		if (!$lang)
		{
			$lang = $this->lang;
		}

		$path = self::getLanguagePath($basePath, $lang);

		$internal = $extension == 'joomla' || $extension == '';
		$filename = $internal ? $lang : $lang . '.' . $extension;
		$filename = "$path/$filename.ini";

		if (isset($this->paths[$extension][$filename]) && !$reload)
		{
			// This file has already been tested for loading.
			$result = $this->paths[$extension][$filename];
		}
		else
		{
			//var_dump($filename, file_exists($filename));echo "<br />";
			// Load the language file
			$result = $this->loadLanguage($filename, $extension);

			// Check whether there was a problem with loading the file
			if ($result === false && $default)
			{
				// No strings, so either file doesn't exist or the file is invalid
				$oldFilename = $filename;

				// Check the standard file name
				$path = self::getLanguagePath($basePath, $this->default);
				$filename = $internal ? $this->default : $this->default . '.' . $extension;
				$filename = "$path/$filename.ini";

				// If the one we tried is different than the new name, try again
				if ($oldFilename != $filename)
				{
					$result = $this->loadLanguage($filename, $extension, false);
				}
			}
		}
		return $result;
	}
}

//$lang = JFactory::getLanguage();
$lparams = JComponentHelper::getParams('com_languages');
$lang = new ILanguage($lparams->get('site'));
$lang->load('mod_edocmancalendar', MROOTPATH);

/*$extension = 'mod_edocmancalendar';
$basePath = JPATH_ROOT;
$lang = JFactory::getLanguage();

$return = $lang->load(strtolower($extension), $basePath, null, false, true);
if(!$return) {
	$lang->load(strtolower($extension), JPATH_MODULES . '/' . $extension, null, false, true);
}
echo get_class($lang);
*/

$document = JFactory::getDocument();

require_once JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_modules'.DS.'models'.DS.'module.php';

$modModel = JModelLegacy::getInstance('Module', 'ModulesModel', array('ignore_request' => true));

$mid = JRequest::getInt('mid');

$mymodule = $modModel->getItem($mid);

$myparams = new JRegistry;
$myparams->loadArray($mymodule->params);
$myparams->mid = $mid;

$module = JModuleHelper::getModule('mod_edocmancalendar');

$registry = new JRegistry;
$registry->loadString($module->params);
$registry->merge($myparams);
$registry->set('mid', $mid);
$registry->set('ajaxed', 1);

$module->params = $registry->toString();

$renderer	= $document->loadRenderer('module');
echo $renderer->render($module, array('style' => 'none'));
