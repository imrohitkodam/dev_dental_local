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

namespace RegularLabs\Plugin\System\AdvancedModules;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Log\Log as JLog;
use RegularLabs\Component\Conditions\Administrator\Api\Conditions as Api_Conditions;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\ObjectHelper as RL_Object;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\PluginTag as RL_PluginTag;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\User as RL_User;

class Helper
{
    public static function prepareModuleList(?array &$modules)
    {
        $modules = empty($modules) ? self::getModuleList() : $modules;

        if (empty($modules))
        {
            return;
        }

        $filtered_modules = [];


        foreach ($modules as $module)
        {
            if (empty($module->id))
            {
                continue;
            }


            $module->name = substr($module->module, 4);

            if (RL_Input::getCmd('option') == 'com_ajax')
            {
                $filtered_modules[] = $module;
                continue;
            }

            if ( ! self::pass($module))
            {
                continue;
            }


            $filtered_modules[] = $module;
        }

        $modules = array_values($filtered_modules);
        unset($filtered_modules);

        if (empty($modules))
        {
            $modules = [(object) []];
        }
    }

    public static function renderModule(?object &$module)
    {
        // Module already nulled
        if (is_null($module))
        {
            return;
        }

        // return true if module is empty (this will empty the content)
        if (self::isEmpty($module))
        {
            // weird fix for J5 to prevent empty modules from showing
            unset($module->content);

            $module = null;

            return;
        }

    }

    public static function setExtraFields(&$module)
    {
    }

    public static function setExtraParams(&$module)
    {
        if (empty($module->id))
        {
            return;
        }

        if (isset($module->extra) && is_object($module->extra))
        {
            return;
        }

        if ( ! isset($module->extra))
        {
            $module->extra = self::getExtraParams($module->id);
        }

        $xml_file = JPATH_ADMINISTRATOR . '/components/com_advancedmodules/form/extra.xml';

        $use_cache = $module->use_amm_cache ?? true;

        $module->extra = RL_Parameters::getObjectFromData($module->extra, $xml_file, $use_cache);
    }

    private static function addHTML(&$module)
    {
    }

    private static function getConfig()
    {
        static $instance;

        if (is_object($instance))
        {
            return $instance;
        }

        $instance = RL_Parameters::getComponent('advancedmodules');

        return $instance;
    }

    private static function getExtraParams($id = 0)
    {
        if ( ! $id)
        {
            return (object) [];
        }

        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            ->select('a.params')
            ->from('#__advancedmodules AS a')
            ->where('a.module_id = ' . (int) $id);
        $db->setQuery($query);

        $params = $db->loadResult();

        if (empty($params))
        {
            return (object) [];
        }

        return json_decode($params);
    }

    private static function getModuleList()
    {
        $app      = JFactory::getApplication();
        $groups   = implode(',', RL_User::getAuthorisedViewLevels());
        $clientId = (int) $app->getClientId();

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
            ->select('am.params AS extra, 0 AS menuid, m.publish_up, m.publish_down')
            ->from('#__modules AS m')
            ->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
            ->join('LEFT', '#__advancedmodules as am ON am.module_id = m.id')
            ->where('m.published = 1')
            ->where('e.enabled = 1')
            ->where('m.client_id = ' . $clientId)
            ->order('m.position, m.ordering');

        if ($clientId)
        {
            // Only for admin modules
            $query->where('m.access IN (' . $groups . ')');
        }

        $db->setQuery($query);

        try
        {
            $modules = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), JLog::WARNING, 'jerror');

            return [];
        }

        return array_values($modules);
    }

    private static function getRemovePositionsAndModules()
    {
    }

    private static function isEmpty(&$module)
    {
        if ( ! isset($module->content))
        {
            return true;
        }

        self::setExtraParams($module);

        // return false if module params are not found
        if (empty($module->extra))
        {
            return false;
        }


        // return false if hideempty is off in module params
        if (empty($module->extra->hideempty) && empty($module->extra->if_empty_html))
        {
            return false;
        }

        $config = self::getConfig();

        // return false if use_hideempty is off in main config
        if ( ! $config->use_hideempty)
        {
            return false;
        }

        $content = trim($module->content);

        // remove empty tags to see if module can be considered as empty
        if ( ! empty($content))
        {
            // remove html and hidden whitespace
            $content = str_replace(chr(194) . chr(160), ' ', $content);
            $content = str_replace(['&nbsp;', '&#160;'], ' ', $content);
            // remove comment tags
            $content = RL_RegEx::replace('<\!--.*?-->', '', $content);
            // remove all closing tags
            $content = RL_RegEx::replace('</[^>]+>', '', $content);
            // remove tags to be ignored
            $tags   = 'p|div|span|strong|b|em|i|ul|font|br|h[0-9]|fieldset|label|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|form';
            $search = '<(?:' . $tags . ')(?:\s[^>]*)?>';

            if (RL_RegEx::match($search, $content))
            {
                $content = RL_RegEx::replace($search, '', $content);
            }

            $content = trim($content);
        }

        if ( ! empty($content))
        {
            return false;
        }


        return true;
    }

    private static function pass(&$module, $article = null)
    {
        $conditions = [
            'menu__menu_item',
            'menu__home_page',
            'date__date',
            'content__page_type',
            'content__category',
            'content__article__id',
            'content__article__featured',
            'content__article__status',
            'content__article__date',
            'content__article__author',
            'content__article__content_keyword',
            'content__article__meta_keyword',
            'visitor__access_level',
            'visitor__user_group',
            'visitor__language',
            'agent__device',
            'agent__os',
            'agent__browser',
            'agent__browser_mobile',
            'other__tag',
            'other__component',
            'other__template',
            'other__url',
            'other__condition',
        ];

        return (new Api_Conditions($article))
            ->setConditionByExtensionItem('com_advancedmodules', $module->id)
            ->pass($conditions);
    }
}
