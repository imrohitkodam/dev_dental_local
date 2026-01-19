<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\Database\DatabaseInterface as JDatabaseInterface;
use Joomla\Filesystem\File as JFile;
use Joomla\Filesystem\Folder as JFolder;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;

class Com_SnippetsInstallerScript
{
    public function postflight($install_type, $adapter)
    {
        if ( ! in_array($install_type, ['install', 'update']))
        {
            return true;
        }

        self::createTable();
        self::fixColumns();
        self::fixJoomla3Format();
        self::deleteJoomla3Files();

        return true;
    }

    private static function createTable()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = "CREATE TABLE IF NOT EXISTS `#__snippets` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `alias` VARCHAR(100) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `category` VARCHAR(50) NOT NULL,
            `color` VARCHAR(8) NULL DEFAULT NULL,
            `content` MEDIUMTEXT NOT NULL,
            `params` TEXT NOT NULL,
            `published` TINYINT(1)  NOT NULL DEFAULT '0',
            `ordering` INT NOT NULL DEFAULT '0',
            `checked_out` INT UNSIGNED DEFAULT NULL,
            `checked_out_time` datetime NULL DEFAULT NULL,
            PRIMARY KEY  (`id`),
            KEY `id` (`id`,`published`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->setQuery($query);
        $db->execute();

        $db->setQuery('ALTER TABLE `#__snippets` MODIFY `checked_out` INT UNSIGNED DEFAULT NULL;');
        $db->execute();

        $db->setQuery('ALTER TABLE `#__snippets` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;');
        $db->execute();

        $db->setQuery('UPDATE `#__snippets` SET `checked_out` = NULL WHERE `checked_out` = 0;');
        $db->execute();

        $db->setQuery('UPDATE `#__snippets` SET `checked_out_time` = NULL WHERE CAST(`checked_out_time` AS CHAR(20)) = \'0000-00-00 00:00:00\';');
        $db->execute();

        $db->setQuery("ALTER TABLE `#__snippets` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $db->execute();
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
                JPATH_ADMINISTRATOR . '/components/com_snippets/controllers',
                JPATH_ADMINISTRATOR . '/components/com_snippets/helpers',
                JPATH_ADMINISTRATOR . '/components/com_snippets/models',
                JPATH_ADMINISTRATOR . '/components/com_snippets/views',
                JPATH_ADMINISTRATOR . '/components/com_snippets/tables',
                JPATH_ADMINISTRATOR . '/components/com_snippets/controller.php',
                JPATH_ADMINISTRATOR . '/components/com_snippets/item_params.xml',
                JPATH_ADMINISTRATOR . '/components/com_snippets/snippets.php',
            ]
        );
    }

    private static function fixColumns()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = 'SHOW COLUMNS FROM `#__snippets`';
        $db->setQuery($query);

        $columns = $db->loadColumn();

        if ( ! in_array('color', $columns))
        {
            $query = "ALTER TABLE `#__snippets` ADD `color` VARCHAR(8) NULL DEFAULT NULL AFTER `category`;";
            $db->setQuery($query);
            $db->execute();
        }
    }

    private static function fixJoomla3Format()
    {
    }

    private static function getTagCharacters()
    {
    }

    private static function getVariableCharacters()
    {
    }

    private static function getVariablesFromContent($content)
    {
    }

    private static function saveVariables($item)
    {
    }
}
