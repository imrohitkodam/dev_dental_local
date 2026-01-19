<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('XTF0F_INCLUDED') || exit;

/**
 * A utility class to help you fetch component parameters without going through JComponentHelper
 */
class XTF0FUtilsConfigHelper
{
    /**
     * Caches the component parameters without going through JComponentHelper. This is necessary since JComponentHelper
     * cannot be reset or updated once you update parameters in the database.
     *
     * @var array
     */
    private static $componentParams = [];

    /**
     * Loads the component's configuration parameters so they can be accessed by getComponentConfigurationValue
     *
     * @param string $component The component for loading the parameters
     * @param bool   $force     Should I force-reload the configuration information?
     */
    final public static function loadComponentConfig($component, $force = false)
    {
        if (isset(self::$componentParams[$component]) && null !== self::$componentParams[$component] && !$force) {
            return;
        }

        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();

        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
                  ->select($xtf0FDatabaseDriver->qn('params'))
                  ->from($xtf0FDatabaseDriver->qn('#__extensions'))
                  ->where($xtf0FDatabaseDriver->qn('type').' = '.$xtf0FDatabaseDriver->q('component'))
                  ->where($xtf0FDatabaseDriver->qn('element').' = '.$xtf0FDatabaseDriver->q($component));
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery);
        $config_ini = $xtf0FDatabaseDriver->loadResult();

        // OK, Joomla! 1.6 stores values JSON-encoded so, what do I do? Right!
        $config_ini = trim($config_ini);

        if (('{' === substr($config_ini, 0, 1)) && '}' === substr($config_ini, -1)) {
            $config_ini = json_decode($config_ini, true);
        } else {
            $config_ini = XTF0FUtilsIniParser::parse_ini_file($config_ini, false, true);
        }

        if (null === $config_ini || empty($config_ini)) {
            $config_ini = [];
        }

        self::$componentParams[$component] = $config_ini;
    }

    /**
     * Retrieves the value of a component configuration parameter without going through JComponentHelper
     *
     * @param string $component The component for loading the parameter value
     * @param string $key       The key to retrieve
     * @param mixed  $default   The default value to use in case the key is missing
     */
    final public static function getComponentConfigurationValue($component, $key, $default = null)
    {
        self::loadComponentConfig($component, false);

        if (array_key_exists($key, self::$componentParams[$component])) {
            return self::$componentParams[$component][$key];
        } else {
            return $default;
        }
    }
}
