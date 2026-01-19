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
 * SimpleScriptManager
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class SimpleScriptManager
{
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
		JHtml::_('jquery.framework');
		JHtml::_('bootstrap.framework');

		if (defined('XTD_SERVER_SIDE_SCRIPT_MODE'))
		{
			$this->_addScript($extensionmainjs . '?' . $version);

			return;
		}

		$this->_addScript('media/lib_extly/js/extlycorejq.js' . '?' . $version);

		if (!empty($paths))
		{
			foreach ($paths as $script_min)
			{
				$script = str_replace('.min', '.js', $script_min);

				if (strpos($script, 'media/') === 0)
				{
					$file = JPATH_ROOT . '/' . $script;

					if (file_exists($file))
					{
						$this->_addScript($script . '?' . $version);

						continue;
					}

					$file = JPATH_ROOT . '/' . $script_min . '.js';

					if (file_exists($file))
					{
						$this->_addScript($script_min . '.js?' . $version);
					}
				}

			}
		}

		$extensionmainjs = str_replace('.min', '', $extensionmainjs);
		$this->_addScript($extensionmainjs . '?' . $version);
	}

	/**
	 * _addScript.
	 *
	 * @param   string  $file  Param
	 *
	 * @return	void
	 */
	private function _addScript($file)
	{
		static $add_postRequireHook = true;

		if (preg_match('#^media/([^/]+)/js/([^\?]+)(\?[0-9]\.[0-9]\.[0-9])?#', $file, $matches))
		{
			$extension = $matches[1];
			$localfile = $matches[2];
			$version = null;

			if (count($matches) == 4)
			{
				$version = $matches[3];
			}

			$include = JHtml::_('script', $extension . '/' . $localfile, false, true, true);

			if ($include)
			{
				JFactory::getDocument()->addScript($include . $version, 'text/javascript', false, false);
			}
			else
			{
				JFactory::getDocument()->addScript($file);
			}
		}
		else
		{
			JFactory::getDocument()->addScript($file);
		}

		if ($add_postRequireHook)
		{
			$add_postRequireHook = false;
			JFactory::getDocument()->addScriptDeclaration(
				'if ((window.postRequireHook) && (!window.run_postRequireHook)) {window.run_postRequireHook = true;jQuery(document).ready(window.postRequireHook);}'
			);
		}
	}
}
