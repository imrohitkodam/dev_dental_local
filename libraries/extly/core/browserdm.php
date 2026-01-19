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

include_once 'dependencymanager.php';

/**
 * BrowserDependencyManager
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class BrowserDependencyManager extends DependencyManager
{
	const JS_BEGIN_COMMENT = "\n/* Extly module - Begin */";
	const JS_END_COMMENT = "/* Extly module - End */\n";

	const JS_JQ_INIT = <<<JS
	<!-- Extly Dependency Manager -->

		var allKeys, config = {
			baseUrl: '{DATA_URLBASE}',
			urlArgs: 'bust={DATA_VERSION}',
			shim: {},
			paths: {}
		};

		// Additional dependencies
		jQuery.each(xtDependencies, function(index, dependency) {
	  		config.shim[dependency.key] = dependency.value;
		});

		// Additional paths
		allKeys = [];
		jQuery.each(xtPaths, function(index, path) {
			allKeys.push(path.key);
	  		config.paths[path.key] = path.value;
		});

		// Require.js allows us to configure shortcut alias
		require.config(config);

		require(allKeys, function() {

			if (_.isFunction(window.postRequireHook))
			{
				postRequireHook();
			}

		});

JS;

	/**
	 * initApp.
	 *
	 * @param   string  $version          Param
	 * @param   string  $extensionmainjs  Param
	 * @param   array   $dependencies     {key2 => {key1, keyi}}
	 * @param   array   $paths            {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	void
	 */
	public function initApp($version = null, $extensionmainjs = null, $dependencies = array(), $paths = array())
	{
		parent::_initApp($version, $extensionmainjs, $dependencies, $paths);

		$this->_generateApp($dependencies, $paths);
	}

	/**
	 * addAppDependency.
	 *
	 * @param   string  $extensionmainjs  Param
	 * @param   array   &$dependencies    {key2 => {key1, keyi}}
	 * @param   array   &$paths           {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	string
	 */
	public function addAppDependency($extensionmainjs, &$dependencies = array(), &$paths = array())
	{
		$appName = parent::addAppDependency($extensionmainjs, $dependencies, $paths);

		$this->_generateApp($dependencies, $paths);
	}

	/**
	 * _generateApp.
	 *
	 * @param   array  $dependencies  {key2 => {key1, keyi}}
	 * @param   array  $paths         {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	void
	 */
	private function _generateApp($dependencies, $paths)
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration(self::JS_BEGIN_COMMENT);

		$listofdeps = array();

		foreach ($dependencies as $dependency_key => $dependency_value)
		{
			$this->_generateDeclaration($dependency_key, $dependency_value);
		}

		foreach ($paths as $path_key => $path_value)
		{
			$this->_generatePath($path_key, $path_value);
		}

		$document->addScriptDeclaration(self::JS_END_COMMENT);
	}

	/**
	 * initPlatform
	 *
	 * @param   array  &$dependencies  Param
	 * @param   array  &$paths         Param
	 *
	 * @return	void
	 */
	protected function initPlatform(&$dependencies, &$paths)
	{
		parent::initPlatform($dependencies, $paths);
		$this->_addRequire();
	}

	/**
	 * initPlatform
	 *
	 * @param   array  &$dependencies  Param
	 * @param   array  &$paths         Param
	 *
	 * @return	void
	 */
	protected function initPlatformNg(&$dependencies, &$paths)
	{
		parent::initPlatformNg($dependencies, $paths);
		$this->_addRequire();
	}

	/**
	 * _addRequire
	 *
	 * @return	void
	 */
	private function _addRequire()
	{
		$document = JFactory::getDocument();
		$document->addScript(JUri::root() . Extly::JS_LIB . 'require/require.min.js');
		$document->addScriptDeclaration("\nvar xtDependencies = [], xtPaths = [];\n");
	}

	/**
	 * _generateDeclaration
	 *
	 * @param   string  $key    Param
	 * @param   string  $value  Param
	 *
	 * @return	void
	 */
	private function _generateDeclaration($key, $value)
	{
		$jsDependencies[$key] = $value;
		$document = JFactory::getDocument();

		$deps = '{deps: ' . json_encode($value) . '}';

		$document->addScriptDeclaration("xtDependencies.push({key:'{$key}', value:{$deps}});");
	}

	/**
	 * _generateDeclaration
	 *
	 * @param   string  $key    Param
	 * @param   string  $value  Param
	 *
	 * @return	void
	 */
	private function _generatePath($key, $value)
	{
		$jsPaths[$key] = $value;
		$document = JFactory::getDocument();

		$path = $this->getFinalFilename($value);

		$document->addScriptDeclaration("xtPaths.push({key:'{$key}', value: '{$path}'});");
	}

	/**
	 * getApp
	 *
	 * @return	string
	 */
	protected function getApp()
	{
		$key = $this->getAppKey();
		F0FPlatform::getInstance()->setCache($key, $key);

		// Joomla 3.0 or superior
		$jsapp_init = self::JS_JQ_INIT;

		$root = JUri::root();
		$jsapp_init = str_replace('{DATA_URLBASE}', $root, $jsapp_init);
		$jsapp_init = str_replace('{DATA_VERSION}', $this->version, $jsapp_init);
		$jsapp_init = self::JS_SCRIPT_BEGIN . $jsapp_init . self::JS_SCRIPT_END;
		$this->addPostRequireHook($jsapp_init);

		return $jsapp_init;
	}
}
