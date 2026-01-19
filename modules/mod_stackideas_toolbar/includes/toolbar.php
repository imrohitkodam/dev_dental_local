<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/constant.php');

class FDT
{
	static $config = null;
	static $moduleId = null;

	// This is based on the key. Do not change the order.
	static $extensions = [
		'com_easysocial',
		'com_easydiscuss',
		'com_easyblog',
		'com_payplans',
		'com_komento'
	];

	/**
	 * Determines if any of the extensions exist on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function componentExists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$exists = self::getMainComponent() !== false;
		}

		return $exists;
	}

	/**
	 * Retrieve the module's settings/params.
	 * FDT::config()->get();
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function config()
	{
		return self::$config;
	}

	/**
	 * Creates a new instance of Foundry
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function fd()
	{
		static $fd = null;

		if (is_null($fd)) {
			$fd = new FoundryLibrary('com_foundry', '', '');
		}

		return $fd;
	}

	/**
	 * Determines if component file exists
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function exists($element, $type = 'extension')
	{
		if (!in_array($type, ['extension', 'library'])) {
			return false;
		}

		// Default
		if ($type === 'extension') {
			// Ensure it has 'com_' prefix first
			if (stristr($element, 'com_') === false) {
				$element = 'com_' . $element;
			}

			$file = JPATH_ADMINISTRATOR . '/components/' . $element . '/includes/' . str_replace('com_', '', $element) . '.php';
			$enabled = JComponentHelper::isEnabled($element);

			if (file_exists($file) && $enabled) {
				return true;
			}

			return false;
		}

		if ($type === 'library') {
			$file = JPATH_LIBRARIES . '/' . $element . '/' . $element . '.php';

			return file_exists($file);
		}

		return false;
	}

	/**
	 * Determines if foundry exists on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function foundryExists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$exists = false;
			$fileExists = FDT::exists('foundry', 'library');

			// Ensure that the plugin is also enabled
			$pluginEnabled = JPluginHelper::isEnabled('system', 'foundry');

			if ($fileExists && $pluginEnabled) {
				$exists = true;
			}
		}
		return $exists;
	}

	/**
	 * Retrieve the menu adapter.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getAdapter($component = false)
	{
		static $adapters = [];

		if (!$component) {
			$input = JFactory::getApplication()->input;
			$component = $input->get('option', '');
		}

		if (!isset($adapters[$component])) {
			$shortName = str_replace('com_', '', $component);

			$class = 'ToolbarAdapter' . ucfirst($shortName);
			$path = FDT_INCLUDES . '/adapter/' . strtolower($shortName) . '/' . strtolower($shortName) . '.php';

			$isComponentExists = self::exists($component);

			// If the adapter is not available, then will fallback to the global adapter
			if (!file_exists($path) || !$isComponentExists) {
				$class = 'ToolbarAdapterGlobal';
				$path = FDT_INCLUDES . '/adapter/global/global.php';
			}

			// Include the base adapter and menu library since we are going to need it anyway.
			require_once(FDT_ADAPTER . '/adapter.php');

			include_once($path);

			if (!class_exists($class)) {
				throw new Exception('Failed to load menu library ' . $class . ' for StackIdeas Toolbar');
			}

			$adapters[$component] = new $class();
		}

		return $adapters[$component];
	}

	/**
	 * Retrieves a list of extensions supported by the module
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getExtensions()
	{
		return self::$extensions;
	}

	/**
	 * Initialize scripts and stylesheets.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function initialize()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			// We need jQuery
			JHtml::_('jquery.framework');

			$app = JFactory::getApplication();
			$compile = $app->input->get('compileToolbar', false, 'bool');

			$scripts = FDT::scripts();

			if ($compile && FH::isSiteAdmin()) {
				$scripts->compile();
				return $app->redirect(JURI::root(), 'Toolbar module scripts compiled successfully. Remember to run <b>gulp minify</b> to minify');
			}

			$scripts->attach();

			$stylesheet = FDT::stylesheet();
			$stylesheet->attach();

			// Initialize foundry's library
			require_once(JPATH_LIBRARIES . '/foundry/foundry.php');

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Magic method to load static methods
	 *
	 * @since	1.0.0
	 * @access  public
	 */
	public static function __callStatic($name, $args)
	{
		$debug = isset($args[0]['debug']) ? true : false;

		FDT::load($name, $debug);

		$className = 'Toolbar' . ucfirst($name);

		if (!class_exists($className)) {
			throw new Exception('Invalid library ' . $className . ' provided for StackIdeas toolbar');
		}

		if (method_exists($className, 'factory')) {
			return call_user_func_array([$className, 'factory'], $args);
		}

		return new $className;
	}

	/**
	 * Loads the library file from the module
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function load($library, $debug = false)
	{
		static $loaded = [];

		if (!isset($loaded[$library])) {
			$lib = strtolower($library);
			$path = FDT_INCLUDES . '/' . $lib . '/' . $lib . '.php';

			if (!file_exists($path)) {

				$loaded[$library] = false;
				return $loaded[$library];
			}

			include_once($path);
			$loaded[$library] = true;
		}

		return $loaded[$library];
	}

	/**
	 * Retrieves the main component to tie this module with
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getMainComponent()
	{
		static $engine = null;

		if (is_null($engine)) {
			$engine = false;

			$extensions = self::getExtensions();

			foreach ($extensions as $extension) {
				if (FDT::exists($extension)) {
					$engine = $extension;
					return $engine;
				}
			}
		}

		return $engine;
	}

	/**
	 * Allows caller to define the params of the module
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function setConfig($params)
	{
		self::$config = $params;
	}

	/**
	 * Determines if toolbar enabled for specific component's page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function toolbarEnabled()
	{
		return self::getAdapter()->toolbarEnabled();
	}

	/**
	 * Retrieve the available extensions
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getAvailableExtensions()
	{
		static $available = null;

		if (is_null($available)) {
			$available = [];

			$extensions = self::getExtensions();

			foreach ($extensions as $key => $extension) {

				if (!FDT::exists($extension)) {
					continue;
				}

				$available[] = str_replace('com_', '', $extension);
			}
		}

		return $available;
	}

	/**
	 * Retrieve the module's appearance setting
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getAppearance() 
	{
		return FDT::config()->get('appearance', 'light');
	}

	/**
	 * Retrieve the module's theme setting
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getAccent() 
	{
		return FDT::config()->get('accent', 'si-theme-foundry');
	}

	/**
	 * Set the module id for the instance
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function setModuleId($id)
	{
		self::$moduleId = (int) $id;
	}

	/**
	 * Retrieve the module id
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getModuleId()
	{
		return self::$moduleId;
	}

	/**
	 * Determine what dropdown placement to use
	 *
	 * @since	1.0.6
	 * @access	public
	 */
	public static function renderDropdownPlacement()
	{
		$isRTL = FH::isRTL();
		$placement = $isRTL ? 'bottom-start' : 'bottom-end';

		// Currently only allow admin to configure this dropdown placement from the setting to avoid tippyjs miscalculation position #128
		if (FH::responsive()->isMobile()) {
			$placement = FDT::config()->get('dropdown_placement', 'bottom-end');

			// match back the original behavior without modify the setting
			if ($isRTL && $placement === 'bottom-end') {
				$placement = 'bottom-start';
			}
		}

		return $placement;
	}	
}