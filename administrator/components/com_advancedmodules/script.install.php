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
use RegularLabs\Component\Conditions\Administrator\Helper\Convert as ConditionsConvert;
use RegularLabs\Component\Conditions\Administrator\Helper\ConvertAssignments as ConditionsConvertAssignments;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel as ConditionsItemModel;
use RegularLabs\Library\DB as RL_DB;

class Com_AdvancedModulesInstallerScript
{
    static private $modules_with_existing_conditions = [];

    public function postflight($install_type, $adapter)
    {
        if ( ! in_array($install_type, ['install', 'update']))
        {
            return true;
        }

        self::createTable();
        self::fixColumns();

        self::deleteOldFiles();
        self::deleteJoomla3Files();
        self::removeAdminMenu();
        self::removeFrontendComponentFromDB();
        self::fixAssetsRules();
        self::fixOldConfig();
        self::deleteOrphanRecords();
        self::setExistingConditionsModuleIds();

        if ( ! self::convertAssignmentsToConditions())
        {
            JFactory::getApplication()->enqueueMessage(
                'There was an issue trying to convert the Joomla 3 assignments to Joomla 4 conditions.',
                'error'
            );

            return false;
        }

        self::removeOldColumns();
        self::convertCoreSettingsToConditions();
        self::handleAMMParams();

        return true;
    }

    private static function convertAllCoreSettingsToConditions(): void
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('m.*')
            ->from($db->quoteName('#__modules', 'm'))
            ->leftJoin(
                $db->quoteName('#__advancedmodules', 'amm'),
                $db->quoteName('amm.module_id') . ' = ' . $db->quoteName('m.id')
            )
            ->leftJoin(
                $db->quoteName('#__conditions_map', 'c'),
                $db->quoteName('c.item_id') . ' = ' . $db->quoteName('m.id')
                . ' AND ' . $db->quoteName('c.extension') . ' = ' . $db->quote('com_advancedmodules')
            )
            ->where(RL_DB::is('m.published', '>-1'))
            ->where(RL_DB::is('m.client_id', '0'))
            ->where(RL_DB::is('c.condition_id', 'NULL'))
            ->where(RL_DB::is('amm.module_id', 'NULL'))
            ->where(RL_DB::notIn('m.id', self::$modules_with_existing_conditions));
        $db->setQuery($query);

        $modules = $db->loadObjectList();

        if (empty($modules))
        {
            return;
        }

        foreach ($modules as $module)
        {
            self::convertCoreSettingsToConditionsByModule($module);
        }
    }

    private static function convertAssignmentsToConditions(): bool
    {
        $loader = include JPATH_LIBRARIES . '/vendor/autoload.php';
        $loader->setPsr4('RegularLabs\\Library\\', JPATH_LIBRARIES . '/regularlabs/src');
        $loader->setPsr4('RegularLabs\\Component\\Conditions\\Administrator\\', JPATH_ADMINISTRATOR . '/components/com_conditions/src');

        $config   = self::getConfig();
        $excludes = [];

        foreach ($config as $key => $value)
        {
            if ( ! str_starts_with($key, 'show_assignto_'))
            {
                continue;
            }

            if ( ! $value)
            {
                $excludes[] = substr($key, strlen('show_assignto_'));
            }
        }

        return ConditionsConvertAssignments::convert('com_advancedmodules', 'advancedmodules', 'modules', 'title', 'module_id', $excludes);
    }

    private static function convertCoreSettingsToConditions()
    {
        self::convertAllCoreSettingsToConditions();
        self::convertRemainingAccessLevelToConditions();
    }

    private static function convertCoreSettingsToConditionsByModule($module)
    {
        $menu_selection   = self::getModuleMenuSelection($module->id);
        $has_access_level = ! empty($module->access) && $module->access !== 1;
        $has_language     = ! empty($module->language) && $module->language !== '*';
        $has_date         = ! empty($module->publish_up) || ! empty($module->publish_down);

        $rules = (object) [];

        if ( ! empty($menu_selection))
        {
            $rules->menuitems = $menu_selection;
        }

        if ($has_access_level)
        {
            $rules->accesslevels = $module->access;
        }

        if ($has_language)
        {
            $rules->languages = $module->language;
        }

        if ($has_date)
        {
            $rules->date = self::getDateString($module->publish_up, $module->publish_down);
        }

        if (empty($rules))
        {
            return;
        }

        $condition = ConditionsConvert::fromObject($rules, '', 'com_advancedmodules', $module->id, 'modules', 'title');

        self::saveCondition($condition, $module->id);
    }

    private static function convertRemainingAccessLevelToConditions()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('m.*, c.condition_id')
            ->from($db->quoteName('#__modules', 'm'))
            ->leftJoin(
                $db->quoteName('#__conditions_map', 'c'),
                $db->quoteName('c.item_id') . ' = ' . $db->quoteName('m.id')
                . ' AND ' . $db->quoteName('c.extension') . ' = ' . $db->quote('com_advancedmodules')
            )
            ->where(RL_DB::is('m.published', '>-1'))
            ->where(RL_DB::is('m.client_id', '0'))
            ->where(RL_DB::is('m.access', '>1'))
            ->where(RL_DB::notIn('m.id', self::$modules_with_existing_conditions));
        $db->setQuery($query);

        $modules = $db->loadObjectList();

        if (empty($modules))
        {
            return;
        }

        foreach ($modules as $module)
        {
            $amm_params = self::getAMMParams($module->id);

            if (isset($amm_params->access_level_saved))
            {
                return;
            }

            self::convertRemainingAccessLevelToConditionsByModule($module);
        }
    }

    private static function convertRemainingAccessLevelToConditionsByModule($module)
    {
        if ( ! $module->access || $module->access == 1)
        {
            return;
        }

        $condition = (new ConditionsItemModel)->getConditionByExtensionItem('com_advancedmodules', $module->id, false);

        if (empty($condition))
        {
            $condition = ConditionsConvert::fromObject((object) [], '', 'com_advancedmodules', $module->id, 'modules', 'title');
        }

        if (empty($condition->groups))
        {
            $condition->groups = [
                (object) [
                    'match_all' => 1,
                    'rules'     => [],
                ],
            ];
        }

        if ( ! $condition->match_all)
        {
            $condition->groups = [
                (object) [
                    'match_all' => 1,
                    'rules'     => [],
                ],
            ];
        }

        $group_id = count($condition->groups) - 1;

        ConditionsConvert::addRule(
            $condition->groups[$group_id],
            'visitor__access_level',
            $module->access
        );

        self::saveCondition($condition, $module->id);
    }

    private static function createTable()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        // main table
        $query = "CREATE TABLE IF NOT EXISTS `#__advancedmodules` (
            `module_id` INT UNSIGNED NOT NULL DEFAULT '0',
            `category` VARCHAR(50) NOT NULL,
            `color` VARCHAR(8) NULL DEFAULT NULL,
            `params` TEXT NOT NULL,
            PRIMARY KEY (`module_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->setQuery($query);
        $db->execute();

        $db->setQuery("ALTER TABLE `#__advancedmodules` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
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
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/controllers',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/helpers/html',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/helpers/xml.php',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/layouts/joomla/edit',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/layouts/joomla/searchtools',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/layouts/toolbar/newmodule.php',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/tables',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/views',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/controller.php',
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/advancedmodules.php',
            ]
        );
    }

    private static function deleteOldFiles()
    {
        self::delete(
            [
                JPATH_ADMINISTRATOR . '/components/com_advancedmodules/src/Service/HTML/Modules.php',
            ]
        );
    }

    private static function deleteOrphanRecords()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            ->delete('#__advancedmodules')
            ->where('params = "" OR module_id NOT IN (SELECT id FROM #__modules)');
        $db->setQuery($query);
        $db->execute();
    }

    private static function fixAssetsRules()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        // Remove unused assets entry (uses com_advancedmodules)
        $query = $db->getQuery(true)
            ->delete('#__assets')
            ->where('name LIKE ' . $db->quote('com_advancedmodules.module.%'));
        $db->setQuery($query);
        $db->execute();
    }

    private static function fixColumns()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = 'SHOW COLUMNS FROM `#__advancedmodules`';
        $db->setQuery($query);

        $columns = $db->loadColumn();

        if (in_array('moduleid', $columns))
        {
            $query = "ALTER TABLE `#__advancedmodules` CHANGE `moduleid` `module_id` INT UNSIGNED NOT NULL DEFAULT '0';";
            $db->setQuery($query);
            $db->execute();
        }

        if ( ! in_array('color', $columns))
        {
            $query = "ALTER TABLE `#__advancedmodules` ADD `color` VARCHAR(8) NULL DEFAULT NULL AFTER `category`;";
            $db->setQuery($query);
            $db->execute();
        }
    }

    private static function fixOldConfig()
    {
        $params = self::getConfig();

        if (empty($params))
        {
            return;
        }

        if ( ! isset($params->show_note) || ! is_numeric($params->show_note))
        {
            return;
        }

        $params->show_note = match ($params->show_note)
        {
            0       => 'none',
            1       => 'tooltip',
            3       => 'column',
            default => 'name',
        };

        self::saveConfig($params);
    }

    private static function getAMMParams($module_id)
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('amm.params')
            ->from($db->quoteName('#__advancedmodules', 'amm'))
            ->where(RL_DB::is('amm.module_id', $module_id));

        $db->setQuery($query);

        $params = $db->loadResult();

        return $params ? json_decode($params) : null;
    }

    private static function getConfig()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_advancedmodules'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('client_id') . ' = 1');
        $db->setQuery($query);

        $params = $db->loadResult();

        return json_decode($params ?: '{}');
    }

    private static function getDateString($up, $down)
    {
        if (empty($down))
        {
            return '>' . $up;
        }

        if (empty($up))
        {
            return '<' . $down;
        }

        return $up . ' to ' . $down;
    }

    private static function getModuleMenuAssignments($module_id)
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('m.menuid')
            ->from($db->quoteName('#__modules_menu', 'm'))
            ->where(RL_DB::is('m.moduleid', $module_id));

        $db->setQuery($query);

        return $db->loadColumn();
    }

    private static function getModuleMenuSelection($module_id)
    {
        $menu_ids = self::getModuleMenuAssignments($module_id);

        if (empty($menu_ids))
        {
            return '';
        }

        if (count($menu_ids) == 1 && $menu_ids[0] == 0)
        {
            return '';
        }

        $exclude = $menu_ids[0] < 0;

        $selection = [];

        foreach ($menu_ids as $menu_id)
        {
            $selection[] = $menu_id < 0 ? $menu_id * -1 : $menu_id;
        }

        return ($exclude ? '!' : '')
            . implode(',', $selection);
    }

    private static function handleAMMParams()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('m.*')
            ->from($db->quoteName('#__modules', 'm'))
            ->leftJoin(
                $db->quoteName('#__advancedmodules', 'amm'),
                $db->quoteName('amm.module_id') . ' = ' . $db->quoteName('m.id')
            )
            ->leftJoin(
                $db->quoteName('#__conditions_map', 'c'),
                $db->quoteName('c.item_id') . ' = ' . $db->quoteName('m.id')
                . ' AND ' . $db->quoteName('c.extension') . ' = ' . $db->quote('com_advancedmodules')
            )
            ->where(RL_DB::is('m.published', '>-1'))
            ->where(RL_DB::is('m.client_id', '0'))
            ->where(RL_DB::is('amm.color', 'NULL'))
            ->where(RL_DB::is('c.condition_id', 'NULL'));

        $db->setQuery($query);

        $modules = $db->loadColumn();

        if (empty($modules))
        {
            return;
        }

        foreach ($modules as $module_id)
        {
            self::saveAMMParams($module_id);
        }
    }

    private static function removeAdminMenu()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        // hide admin menu
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__menu'))
            ->where($db->quoteName('path') . ' = ' . $db->quote('com-advancedmodules'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('client_id') . ' = 1');
        $db->setQuery($query);
        $db->execute();
    }

    private static function removeFrontendComponentFromDB()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        // remove frontend component from extensions table
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_advancedmodules'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('client_id') . ' = 0');
        $db->setQuery($query);
        $db->execute();

        /** @var CallbackController $cache */
        $cache = JFactory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('callback', ['defaultgroup' => '_system']);
        $cache->clean();
    }

    private static function removeOldColumns()
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = 'SHOW COLUMNS FROM `#__advancedmodules`';
        $db->setQuery($query);

        $columns = $db->loadColumn();

        if (in_array('asset_id', $columns))
        {
            $query = "ALTER TABLE `#__advancedmodules` DROP `asset_id`;";
            $db->setQuery($query);
            $db->execute();
        }

        if (in_array('mirror_id', $columns))
        {
            $query = "ALTER TABLE `#__advancedmodules` DROP `mirror_id`;";
            $db->setQuery($query);
            $db->execute();
        }
    }

    private static function saveAMMParams($module_id)
    {
        $amm_params = self::getAMMParams($module_id);

        if (isset($amm_params->access_level_saved))
        {
            return;
        }

        $params = [
            ...(array) ($amm_params ?? []),
            'access_level_saved' => 1,
        ];

        if (is_null($amm_params))
        {
            self::storeAMMParams($module_id, $params);

            return;
        }

        $color = $params['color'] ?? '';
        unset($params['color']);

        self::updateAMMParams($module_id, $color, $params);
    }

    private static function saveCondition($condition, $module_id)
    {
        (new ConditionsItemModel)->saveByObject(
            $condition,
            'com_advancedmodules',
            $module_id,
            'modules',
            'title'
        );
    }

    private static function saveConfig($params)
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        if (is_object($params))
        {
            $params = json_encode($params);
        }

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = ' . $db->quote($params))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_advancedmodules'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('client_id') . ' = 1');
        $db->setQuery($query);
        $db->execute();
    }

    private static function setExistingConditionsModuleIds(): void
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select('item_id')
            ->from($db->quoteName('#__conditions_map'))
            ->where('extension = ' . $db->quote('com_advancedmodules'));
        $db->setQuery($query);

        self::$modules_with_existing_conditions = $db->loadColumn();
    }

    private static function storeAMMParams($module_id, $params = [])
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $category = $db->quote('');
        $params   = $db->quote(json_encode($params));

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__advancedmodules'))
            ->columns($db->quoteName(['module_id', 'category', 'color', 'params']))
            ->values($module_id . ',' . $category . ', NULL,' . $params);
        $db->setQuery($query);
        $db->execute();
    }

    private static function updateAMMParams($module_id, $color = '', $params = [])
    {
        $db = JFactory::getContainer()->get(JDatabaseInterface::class);

        $color  = substr($color, 0, 8);
        $color  = $color ? $db->quote($color) : 'NULL';
        $params = $db->quote(json_encode($params));

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__advancedmodules'))
            ->set($db->quoteName('color') . ' = ' . $color)
            ->set($db->quoteName('params') . ' = ' . $params)
            ->where(RL_DB::is('module_id', $module_id));
        $db->setQuery($query);
        $db->execute();
    }
}
