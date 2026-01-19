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

include_once 'config.php';

include_once 'browserdm.php';
include_once 'sitedm.php';
include_once 'simplescriptmanager.php';

/**
 * This is the base class for the Extly framework.
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class Extly
{
	const JS_LIB = 'media/lib_extly/js/';

	/**
	 * loadMeta.
	 *
	 * @return	void
	 */
	public static function loadMeta()
	{
		$document = JFactory::getDocument();
		$document->setMetaData('X-UA-Compatible', 'IE=edge,chrome=1');
	}

	/**
	 * loadStyle.
	 *
	 * @param   bool  $frontendMode  Param
	 * @param   bool  $loadChosen    Param
	 *
	 * @return	void
	 */
	public static function loadStyle($frontendMode = false, $loadChosen = true)
	{
		JHtml::_('bootstrap.framework');

		if (!$frontendMode)
		{
			if ($loadChosen)
			{
				JHtml::_('formbehavior.chosen', 'select');
			}

			JHtml::_('bootstrap.tooltip');
		}

		JHtml::stylesheet('lib_extly/extly-base-' . EXTLY_BASE . '.css', false, true);
	}

	/**
	 * loadStyle.
	 *
	 * @return	void
	 */
	public static function loadAwesome()
	{
		JHtml::stylesheet('lib_extly/extly-font-awesome-' . EXTLY_BASE . '.min.css', false, true);
	}

	/**
	 * getSimpleScriptManager.
	 *
	 * @return	object
	 */
	public static function getSimpleScriptManager()
	{
		return new SimpleScriptManager();
	}

	/**
	 * getScriptManager.
	 *
	 * @param   bool  $loadExtlyAdminMode  Param
	 * @param   bool  $ownJqueryDisabled   Param
	 * @param   bool  $loadBootstrap       Param
	 *
	 * @return	object
	 */
	public static function getScriptManager($loadExtlyAdminMode = null, $ownJqueryDisabled = false, $loadBootstrap = false)
	{
		static $scriptManager = null;

		if (!$scriptManager)
		{
			if (defined('XTD_SERVER_SIDE_SCRIPT_MODE') && (XTD_SERVER_SIDE_SCRIPT_MODE))
			{
				$scriptManager = new SiteDependencyManager($loadExtlyAdminMode, $ownJqueryDisabled, $loadBootstrap);
			}
			else
			{
				$scriptManager = new BrowserDependencyManager($loadExtlyAdminMode, $ownJqueryDisabled, $loadBootstrap);
			}
		}

		return $scriptManager;
	}

	/**
	 * loadHtml
	 *
	 * @return	void
	 */
	public static function loadHtml()
	{
		JHtml::addIncludePath(JPATH_ROOT . '/libraries/extly/helpers/html');
	}

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
	public static function initApp($version = null, $extensionmainjs = null, $dependencies = array(), $paths = array())
	{
		self::getScriptManager()->initApp($version, $extensionmainjs, $dependencies, $paths);
	}

	/**
	 * hasApp
	 *
	 * @return	bool
	 */
	public static function hasApp()
	{
		return self::getScriptManager()->hasApp();
	}

	/**
	 * getAppName
	 *
	 * @param   string  $file  Param
	 *
	 * @return	string
	 */
	public static function getAppName($file)
	{
		return self::getScriptManager()->getAppName($file);
	}

	/**
	 * addAppDependency.
	 *
	 * @param   string  $extensionmainjs  Param
	 * @param   array   $dependencies     {key2 => {key1, keyi}}
	 * @param   array   $paths            {key1 => pathjs1, key2 => pathjs2}
	 *
	 * @return	void
	 */
	public static function addAppDependency($extensionmainjs, $dependencies = array(), $paths = array())
	{
		self::getScriptManager()->addAppDependency($extensionmainjs, $dependencies, $paths);
	}

	/**
	 * insertDependencyManager
	 *
	 * @param   string  &$body  Param
	 *
	 * @return	void
	 *
	 * @deprecated
	 */
	public static function insertDependencyManager(&$body)
	{
		return self::insertApp($body);
	}

	/**
	 * insertApp.
	 *
	 * @param   string  &$body  Param
	 *
	 * @return	void
	 */
	public static function insertApp(&$body)
	{
		return self::getScriptManager()->insertApp($body);
	}

	/**
	 * addPostRequireScript.
	 *
	 * @param   string  $script  Param
	 *
	 * @return	void
	 */
	public static function addPostRequireScript($script)
	{
		return self::getScriptManager()->addPostRequireScript($script);
	}

	/**
	 * getFormId.
	 *
	 * @return	void
	 */
	public static function getFormId()
	{
		return 'adminForm';
	}

	/**
	 * showInvalidFormAlert.
	 *
	 * @return	void
	 */
	public static function showInvalidFormAlert()
	{
		?>
<div id="invalid-form" class="alert alert-block alert-error"
	style="display: none;">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<h4 class="alert-heading">
		<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED'); ?>
	</h4>
</div>
<?php
	}

	/**
	 * getHost.
	 *
	 * @return	string
	 */
	public static function getHost()
	{
		$baseurl = JUri::root();

		$uri = new JUri;

		if ($uri->parse($baseurl))
		{
			$host = $uri->toString(
				array(
							'scheme',
							'host',
							'port'
				)
			);

			return $host;
		}

		return null;
	}

	/**
	 * _getDirectory.
	 *
	 * @return	string
	 */
	public static function getDirectory()
	{
		$uri = JFactory::getUri();
		$host = $uri->getHost();
		$root = $uri->root();
		$parts = explode($host, $root);
		$path = $parts[1];

		return $path;
	}

	/**
	 * loadComponentLanguage.
	 *
	 * @param   string  $option  Param
	 *
	 * @return	void
	 */
	public static function loadComponentLanguage($option)
	{
		// Component Language Load
		$jlang = JFactory::getLanguage();
		$paths = array(
						JPATH_ADMINISTRATOR,
						JPATH_ROOT
		);
		$jlang->load($option, $paths [0], 'en-GB', true);
		$jlang->load($option, $paths [0], null, true);
		$jlang->load($option, $paths [1], 'en-GB', true);
		$jlang->load($option, $paths [1], null, true);

		return $jlang;
	}
}
