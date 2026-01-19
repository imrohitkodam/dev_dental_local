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

jimport('joomla.plugin.plugin');

/**
 * VERY IMP :
 * While adding functions into plugin, we should keep in mind
 * that all function not starting with _ (under-score), will be
 * added into plugins event functions. So while adding supportive
 * function, always start them with underscore
 */
class PPPlugins extends JPlugin
{
	private $templateVars = array();

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = PP::config();
		$this->my = JFactory::getUser();
		$this->info = PP::info();
		$this->theme = PP::themes();
		$this->initalize();

		$path = $this->getPluginPath() . '/' . $this->getName();
		$this->loadLanguage('', $path);
	}

	/**
	 * Attach a script for the plugin
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function attachScripts($files = array())
	{
		if (!$files) {
			return;
		}

		$path = $this->getAssetsPath();

		$lib = PP::scripts();

		foreach ($files as $file) {
			$file = $path . '/' . $file . '.js';

			$lib->addScript($file);
		}
	}

	/**
	 * Attach a script for the plugin
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function attachScriptContents($contents)
	{
		PP::scripts()->addInlineScripts($contents);
	}

	/**
	 * Retrieves an app's helper
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAppHelper()
	{
		static $helpers = null;

		$key = $this->getName();

		if (!isset($helpers[$key])) {
			$path = $this->getPluginPath();
			$path .= '/app/helper.php';

			if (!JFile::exists($path)) {
				$helpers[$key] = false;
				return $helpers[$key];
			}

			require_once($path);
			$className = 'PPHelper' . $this->getName();

			// Get the first available app
			$apps = $this->getAvailableApps();
			$app = PP::app();

			if ($apps) {
				$app = array_pop($apps);
			}

			$helpers[$key] = new $className($app->getAppParams(), $app);
		}

		return $helpers[$key];
	}

	/**
	 * Retrieves an app's helper
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAvailableApps()
	{
		$apps = PPHelperApp::getAvailableApps($this->getName());

		return $apps;
	}

	/**
	 * Retrieves the name of the plugin
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Retrieves the name of the plugin
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Retrieves the path to the plugin's app.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected function getAppPath()
	{
		// Since all of the apps are stored in the app folder, we can centralize this
		$path = $this->getPluginPath() . '/app';

		return $path;
	}

	/**
	 * All plugins should store their script files under /plugins/element/assets/
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected function getAssetsPath($uri = true)
	{
		$path = $this->getPluginPath();

		if ($uri) {
			$path = str_ireplace(JPATH_ROOT, rtrim(JURI::root(), '/'), $path);
		}

		$path .= '/assets';

		return $path;
	}

	/**
	 * Retrieves the path to the plugin
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected function getPluginPath()
	{
		$name = $this->getName();
		$type = $this->getType();

		$path = JPATH_PLUGINS . '/' . $type . '/' . $name;

		return $path;
	}

	/**
	 * Resolves an xhr request by sending the contents back
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function resolve()
	{
		if (!isset($this->ajax) || !$this->ajax) {
			return false;
		}

		return call_user_func_array(array($this->ajax, __FUNCTION__), func_get_args());
	}

	/**
	 * Load helpers for the module
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function html()
	{
		$theme = PP::themes();
		$args = func_get_args();

		$output = call_user_func_array(array($theme, 'html'), $args);

		return $output;
	}

	/**
	 * Provides assistance to the payment app to set variables which can be extracted with the @display method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function set($property, $value = NULL)
	{
		$this->templateVars[$property] = $value;
	}

	/**
	 * Determines if the plugin has template override
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function hasOverride($namespace)
	{
		$file = $this->getOverridePath() . '/' . $namespace . '.php';

		if (JFile::exists($file)) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the override path of the plugin
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function getOverridePath($relative = false)
	{
		$path = '';

		if (!$relative) {
			$path .= JPATH_ROOT;
		}

		$type = $this->getType();
		$name = $this->getName();

		$model = PP::model('themes');
		$currentSiteTemplate = $model->getCurrentJoomlaTemplate();

		$path .= '/templates/' . $currentSiteTemplate . '/html/plg_' . $type . '_' . $name;

		return $path;
	}

	/**
	 * Allows plugin to output plugin template
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function output($namespace)
	{
		$type = $this->getType();
		$name = $this->getName();

		$hasOverride = $this->hasOverride($namespace);
		$file = $this->getPluginPath() . '/tmpl/' . $namespace . '.php';

		$this->theme->setVars($this->templateVars);

		if ($hasOverride) {

			$output = '';
			$file = $this->getOverridePath() . '/' . $namespace . '.php';

			// check for the JavaScript file as well
			$jsFile = $this->getOverridePath() . '/' . $namespace . '.js';

			extract($this->templateVars);

			// Javascript content
			if (JFile::exists($jsFile)) {
				ob_start();
				include($jsFile);

				$contents = ob_get_contents();
				$output = '<script>' . $contents . '</script>';
				ob_end_clean();
			}

			ob_start();
			include($file);

			$contents = ob_get_contents();
			$output = $contents . $output;

			ob_end_clean();

			return $output;
		}

		$namespace = 'plugins:/' . $type . '/' . $name . '/' . $namespace;

		$output = $this->theme->output($namespace);

		return $output;
	}


	/**
	 * Method to retrieve PayPlans apps listing in app creation
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function onPPAppListing(&$items)
	{
		// check if this app has the app.json inside the folder or not.
		$path = $this->getPluginPath();

		// for pp apps, we see the app.json file
		$configPath = $path . '/app/app.json';

		if (JFile::exists($configPath)) {

			$contents = file_get_contents($configPath);
			$item = json_decode($contents);

			// // debug
			// ob_start();
			// echo $configPath . "\n\n";
			// var_dump($item);
			// echo "\n\n";
			// $raw = ob_get_contents();
			// ob_end_clean();
			
			// $logfile = JPATH_ROOT . '/tmp/log.txt';
			// JFile::append($logfile, $raw);
			// // debug end

			if (!is_null($item)) {
				$items[] = $item;
			}
		}
	}


	/**
	 * Method to retrieve PayPlans payment apps listing in payment method creation
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function onPPGatewayListing(&$items)
	{
		// check if this app has the app.json inside the folder or not.
		$path = $this->getPluginPath();

		// for pp payment apps, we see the gateway.json file
		$configPath = $path . '/app/gateway.json';

		if (JFile::exists($configPath)) {

			$contents = file_get_contents($configPath);
			$item = json_decode($contents);

			if (!is_null($item)) {
				$items[] = $item;
			}
		}
	}


	/**
	 * Method to retrieve PayPlans automatiom apps listing in automation creation page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function onPPAutomationListing(&$items)
	{
		// check if this app has the app.json inside the folder or not.
		$path = $this->getPluginPath();

		// for pp payment apps, we see the gateway.json file
		$configPath = $path . '/app/automation.json';

		if (JFile::exists($configPath)) {

			$contents = file_get_contents($configPath);
			$item = json_decode($contents);

			if (!is_null($item)) {
				$items[] = $item;
			}
		}
	}

	/**
	 * Legacy support. Use @initialize instead
	 *
	 * @deprecated	4.0.0
	 */
	protected function _initialize(Array $options = array()) { }

	/**
	 * Initializes the plugins. Method implementation could be implemented by child classes.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	protected function initalize($options= array())
	{
		// For backward compatibility with 3.6.5 and earlier
		$this->_initialize();
	}

	/**
	 * Plugin is available :
	 * If current plugin can be used ir-respective
	 * of conditions
	 */
	public function _isAvailable(Array $options= array())
	{}

	/**
	 * Plugin is available but check if
	 * It should be used for given conditions
	 */
	public function _isApplicable(Array $conditions= array())
	{}
}
