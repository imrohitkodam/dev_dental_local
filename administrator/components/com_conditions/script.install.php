<?php
/**
 * @package         Conditions
 * @version         25.11.2254
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

if ( ! class_exists('Com_ConditionsInstallerScript'))
{
    class Com_ConditionsInstallerScript
    {
        public function postflight($install_type, $adapter)
        {
            if ( ! in_array($install_type, ['install', 'update']))
            {
                return true;
            }

            self::createTables();
            self::fixColumns();
            self::deleteOldFiles();

            return true;
        }

        private static function createTables()
        {
            $db = JFactory::getContainer()->get(JDatabaseInterface::class);

            $query = "CREATE TABLE IF NOT EXISTS `#__conditions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `alias` VARCHAR(100) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `category` VARCHAR(50) NOT NULL DEFAULT '',
            `color` VARCHAR(8) NULL DEFAULT NULL,
            `match_all` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
            `published` TINYINT(1) NOT NULL DEFAULT '0',
            `hash` VARCHAR(32) NOT NULL DEFAULT '',
            `checked_out` INT UNSIGNED DEFAULT NULL,
            `checked_out_time` datetime NULL DEFAULT NULL,
            PRIMARY KEY  (`id`),
            KEY `published` (`published`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $db->setQuery($query);
            $db->execute();

            $query = "CREATE TABLE IF NOT EXISTS `#__conditions_groups` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `condition_id` INT UNSIGNED NOT NULL,
            `match_all` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
            `ordering` INT UNSIGNED NOT NULL DEFAULT '0',
            PRIMARY KEY  (`id`),
            KEY `condition_id` (`condition_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $db->setQuery($query);
            $db->execute();

            $query = "CREATE TABLE IF NOT EXISTS `#__conditions_map` (
            `condition_id` INT UNSIGNED NOT NULL,
            `extension` VARCHAR(50) NOT NULL,
            `item_id` INT UNSIGNED NOT NULL,
            `table` VARCHAR(50) NOT NULL,
            `name_column` VARCHAR(50) NOT NULL,
            UNIQUE KEY `condition_id` (`condition_id`, `item_id`, `extension`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $db->setQuery($query);
            $db->execute();

            $query = "CREATE TABLE IF NOT EXISTS `#__conditions_rules` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `group_id` INT UNSIGNED NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `exclude` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
            `params` TEXT NOT NULL,
            `ordering` INT UNSIGNED NOT NULL DEFAULT '0',
            PRIMARY KEY  (`id`),
            KEY `group_id` (`group_id`),
            KEY `type` (`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $db->setQuery($query);
            $db->execute();

            $db->setQuery("ALTER TABLE `#__conditions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $db->execute();

            $db->setQuery("ALTER TABLE `#__conditions_groups` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $db->execute();

            $db->setQuery("ALTER TABLE `#__conditions_map` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $db->execute();

            $db->setQuery("ALTER TABLE `#__conditions_rules` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
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

        private static function deleteOldFiles()
        {
            self::delete(
                [
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Form.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/FormField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/FormFieldGroup.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/AgentsField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/ComponentsField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/ContentArticlesField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/ContentCategoriesField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/FieldField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/GeoField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/GeoInformationField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/LanguagesField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/MenuItemsField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/RulesField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/TagsField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/TemplatesField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/UserGroupsField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/UsersField.php',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Form/Field/geo.iso.regions.txt',
                    JPATH_ADMINISTRATOR . '/components/com_conditions/src/Helper/MobileDetect.php',
                ]
            );
        }

        private static function fixColumns()
        {
            $db = JFactory::getContainer()->get(JDatabaseInterface::class);

            $query = 'SHOW COLUMNS FROM `#__conditions`';
            $db->setQuery($query);

            $columns = $db->loadColumn();

            if ( ! in_array('color', $columns))
            {
                $query = "ALTER TABLE `#__conditions` ADD `color` VARCHAR(8) NULL DEFAULT NULL AFTER `category`;";
                $db->setQuery($query);
                $db->execute();
            }

            $query = 'SHOW COLUMNS FROM `#__conditions_groups`';
            $db->setQuery($query);

            $columns_groups = $db->loadColumn();

            if (in_array('name', $columns_groups))
            {
                $query = "ALTER TABLE `#__conditions_groups` DROP `name`;";
                $db->setQuery($query);
                $db->execute();
            }

            if (in_array('description', $columns_groups))
            {
                $query = "ALTER TABLE `#__conditions_groups` DROP `description`;";
                $db->setQuery($query);
                $db->execute();
            }
        }
    }
}
