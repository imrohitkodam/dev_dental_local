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
 * A utility class to help you quickly clean the Joomla! cache
 */
class XTF0FUtilsCacheCleaner
{
    /**
     * Clears the com_modules and com_plugins cache. You need to call this whenever you alter the publish state or
     * parameters of a module or plugin from your code.
     *
     * @return void
     */
    public static function clearPluginsAndModulesCache()
    {
        self::clearPluginsCache();
        self::clearModulesCache();
    }

    /**
     * Clears the com_plugins cache. You need to call this whenever you alter the publish state or parameters of a
     * plugin from your code.
     *
     * @return void
     */
    public static function clearPluginsCache()
    {
        self::clearCacheGroups(['com_plugins'], [0, 1]);
    }

    /**
     * Clears the com_modules cache. You need to call this whenever you alter the publish state or parameters of a
     * module from your code.
     *
     * @return void
     */
    public static function clearModulesCache()
    {
        self::clearCacheGroups(['com_modules'], [0, 1]);
    }

    /**
     * Clears the specified cache groups.
     *
     * @param array $clearGroups  Which cache groups to clear. Usually this is com_yourcomponent to clear your
     *                            component's cache.
     * @param array $cacheClients Which cache clients to clear. 0 is the back-end, 1 is the front-end. If you do not
     *                            specify anything, both cache clients will be cleared.
     *
     * @return void
     */
    public static function clearCacheGroups(array $clearGroups, array $cacheClients = [0, 1])
    {
        $conf = JFactory::getConfig();

        foreach ($clearGroups as $clearGroup) {
            foreach ($cacheClients as $cacheClient) {
                try {
                    $options = [
                        'defaultgroup' => $clearGroup,
                        'cachebase' => ($cacheClient) ? JPATH_ADMINISTRATOR.'/cache' : $conf->get('cache_path', JPATH_SITE.'/cache'),
                    ];

                    $cache = JCache::getInstance('callback', $options);
                    $cache->clean();
                } catch (Exception $e) {
                    // suck it up
                }
            }
        }
    }
}
