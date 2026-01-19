<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Factory as JFactory;
use Joomla\Database\DatabaseInterface as JDatabaseInterface;
use Joomla\Filesystem\File as JFile;
use Joomla\Filesystem\Folder as JFolder;

class PlgSystemAdvancedModulesInstallerScript
{
    public function postflight($install_type, $adapter)
    {
        if ( ! in_array($install_type, ['install', 'update']))
        {
            return true;
        }

        self::setPluginOrdering();
        self::deleteJoomla3Files();

        return true;
    }

    private static function delete($files = [])
    {
        foreach ($files as $file)
        {
            if (is_dir($file))
            {
                JFolder::delete($file);
            }

            if (is_file($file))
            {
                JFile::delete($file);
            }
        }
    }

    private static function deleteJoomla3Files()
    {
        self::delete(
            [
                JPATH_SITE . '/plugins/system/advancedmodules/vendor',
            ]
        );
    }

    private static function setPluginOrdering()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            ->update('#__extensions')
            ->set($db->quoteName('ordering') . ' = -1')
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('advancedmodules'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'));
        $db->setQuery($query);
        $db->execute();

        /** @var CallbackController $cache */
        $cache = JFactory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('callback', ['defaultgroup' => '_system']);
        $cache->clean();
    }
}
