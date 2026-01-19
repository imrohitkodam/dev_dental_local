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
 * EExtensionHelper
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class EExtensionHelper
{
	/**
	 * getComponentId
	 *
	 * @param   string  $element  Params
	 *
	 * @return	int.
	 */
	public static function getComponentId($element)
	{
		$extensionsModel = F0FModel::getTmpInstance('Extensions', 'ExtlyModel');
		$extensionsModel->set('type', 'component');
		$extensionsModel->set('element', $element);
		$extensionsModel->set('limit', 1);
		$extensions = $extensionsModel->getItemList();

		if (count($extensions) == 1)
		{
			$extension = $extensions[0];

			return $extension->extension_id;
		}

		return null;
	}

	/**
	 * getExtensionId
	 *
	 * @param   string  $folder   Params
	 * @param   string  $element  Params
	 *
	 * @return	int.
	 */
	public static function getExtensionId($folder, $element)
	{
		return self::getExtensionParam($folder, $element, 'extension_id');
	}

	/**
	 * getExtensionParam.
	 *
	 * @param   string  $folder   Params
	 * @param   string  $element  Params
	 * @param   string  $key      Params
	 * @param   string  $default  Params
	 *
	 * @return	string.
	 */
	public static function getExtensionParam($folder, $element, $key, $default = null)
	{
		$extensionsModel = F0FModel::getTmpInstance('Extensions', 'ExtlyModel');
		$extensionsModel->set('folder', $folder);
		$extensionsModel->set('element', $element);
		$extensionsModel->set('limit', 1);
		$extensions = $extensionsModel->getItemList();

		if (count($extensions) == 1)
		{
			$extension = $extensions[0];

			if (isset($extension->$key))
			{
				return $extension->$key;
			}

			$extension->xtform = EForm::paramsToRegistry($extension);

			return $extension->xtform->get($key, $default);
		}

		return $default;
	}

	/**
	 * setExtensionParam.
	 *
	 * @param   string  $folder   Params
	 * @param   string  $element  Params
	 * @param   string  $key      Params
	 * @param   string  $value    Params
	 *
	 * @return	string.
	 */
	public static function setExtensionParam($folder, $element, $key, $value)
	{
		$extensionsModel = F0FModel::getTmpInstance('Extensions', 'ExtlyModel');
		$extensionsModel->set('folder', $folder);
		$extensionsModel->set('element', $element);
		$extensionsModel->set('limit', 1);
		$extensions = $extensionsModel->getItemList();

		if (count($extensions) == 1)
		{
			$extension = $extensions[0];

			if (isset($extension->$key))
			{
				$extension->$key = $value;

				return $extensionsModel->save($extension);
			}

			$extension->xtform = EForm::paramsToRegistry($extension);
			$extension->xtform->set($key, $value);

			return $extensionsModel->save($extension);
		}

		return false;
	}

	/**
	 * Custom clean cache method, plugins are cached in 2 places for different clients
	 *
	 * @return	void
	 *
	 * @since   1.6
	 */
	public static function cleanCache()
	{
		$conf = JFactory::getConfig();

		if (EXTLY_J3)
		{
			$dispatcher = JEventDispatcher::getInstance();
		}
		else
		{
			$dispatcher = JDispatcher::getInstance();
		}

		$options = array(
						'defaultgroup' => 'com_plugins',
						'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$dispatcher->trigger('onContentCleanCache', $options);
	}
}
