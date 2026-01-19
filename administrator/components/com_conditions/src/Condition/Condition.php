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

namespace RegularLabs\Component\Conditions\Administrator\Condition;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Menu\MenuItem as JMenuItem;
use RegularLabs\Component\Conditions\Administrator\Helper\Cache;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;

/**
 * Class Condition
 *
 * @package RegularLabs\Library
 */
abstract class Condition
{
    static    $_request;
    public    $article;
    public    $category_id;
    public    $date;
    public    $db;
    public    $exclude;
    public    $module;
    public    $params;
    public    $request;
    public    $selection;
    protected $request_keys = [];

    public function __construct(
        object            $rule,
        object|false|null $article = null,
        object|false|null $module = null,
        object|false|null $request = null,
        ?int              $category_id = null
    )
    {
        self::setRequest($request);

        $this->db = RL_DB::get();

        $this->params      = $rule->params ?? (object) [];
        $this->selection   = $rule->params->selection ?? [];
        $this->exclude     = $rule->exclude ?? false;
        $this->article     = $article;
        $this->module      = $module;
        $this->category_id = $category_id;

        $this->prepareSelection();
    }

    public function beforePass(): void
    {
    }

    public function getMenuItemParams(int $id = 0): object
    {
        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $query = $this->db->getQuery(true)
            ->select('m.params')
            ->from('#__menu AS m')
            ->where('m.id = ' . (int) $id);
        $this->db->setQuery($query);
        $params = $this->db->loadResult();

        return $cache->set(RL_Parameters::getObjectFromData($params));
    }

    public function getParentIds(
        int    $id = 0,
        string $table = 'menu',
        string $parent = 'parent_id',
        string $child = 'id'
    ): array
    {
        if ( ! $id)
        {
            return [];
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $parent_ids = [];

        while ($id)
        {
            $query = $this->db->getQuery(true)
                ->select('t.' . $parent)
                ->from('#__' . $table . ' as t')
                ->where('t.' . $child . ' = ' . (int) $id);
            $this->db->setQuery($query);
            $id = $this->db->loadResult();

            // Break if no parent is found or parent already found before for some reason
            if ( ! $id || in_array($id, $parent_ids))
            {
                break;
            }

            $parent_ids[] = $id;
        }

        return $cache->set($parent_ids);
    }

    public function init(): void
    {
    }

    public function initRequest(&$request): void
    {
    }

    public function makeArray(
        array|string|null $array = '',
        string            $delimiter = ',',
        bool              $trim = false
    ): array
    {
        if (empty($array))
        {
            return [];
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $array = $this->mixedDataToArray($array, $delimiter);

        if (empty($array))
        {
            return $array;
        }

        if ( ! $trim)
        {
            return $array;
        }

        foreach ($array as $k => $v)
        {
            if ( ! is_string($v))
            {
                continue;
            }

            $array[$k] = trim($v);
        }

        return $cache->set($array);
    }

    public function passByPageType(
        string $option,
        array  $selection = [],
        bool   $add_view = false,
        bool   $get_task = false,
        bool   $get_layout = true
    ): bool
    {
        if ($this->request->option != $option)
        {
            return false;
        }

        if ($get_task && $this->request->task && $this->request->task != $this->request->view && $this->request->task != 'default')
        {
            $pagetype = ($add_view ? $this->request->view . '_' : '') . $this->request->task;

            return $this->passSimple($pagetype, $selection);
        }

        if ($get_layout && $this->request->layout && $this->request->layout != $this->request->view && $this->request->layout != 'default')
        {
            $pagetype = ($add_view ? $this->request->view . '_' : '') . $this->request->layout;

            return $this->passSimple($pagetype, $selection);
        }

        return $this->passSimple($this->request->view, $selection);
    }

    public function passInRange(
        string|int|null   $value = '',
        array|string|null $selection = null
    ): bool
    {
        if (empty($value))
        {
            return false;
        }

        $selections = $this->makeArray($selection ?: $this->selection);

        $pass = false;

        foreach ($selections as $selection)
        {
            if (empty($selection))
            {
                continue;
            }

            if ( ! str_contains($selection, '-'))
            {
                if ((int) $value == (int) $selection)
                {
                    $pass = true;
                    break;
                }

                continue;
            }

            [$min, $max] = explode('-', $selection, 2);

            if ((int) $value >= (int) $min && (int) $value <= (int) $max)
            {
                $pass = true;
                break;
            }
        }

        return $pass;
    }

    public function passItemByType(bool &$pass, string $type = '', mixed $data = null): bool
    {
        $pass_type = ! empty($data) ? $this->{'pass' . $type}($data) : $this->{'pass' . $type}();

        if ($pass_type === null)
        {
            return true;
        }

        $pass = $pass_type;

        return $pass;
    }

    public function passSimple(
        mixed             $values = '',
        string|array|null $selection = null,
        bool              $caseinsensitive = false
    )
    {
        $values = $this->makeArray($values);

        $selection = $selection ?: $this->selection;
        $selection = is_array($selection) ? $selection : [$selection];

        if (empty($selection))
        {
            return false;
        }

        $pass = false;

        foreach ($values as $value)
        {
            if ($caseinsensitive)
            {
                if (in_array(strtolower($value), array_map('strtolower', $selection), true))
                {
                    $pass = true;
                    break;
                }

                continue;
            }

            if (in_array($value, $selection))
            {
                $pass = true;
                break;
            }
        }

        return $pass;
    }

    protected function prepareSelection(): void
    {
    }

    private function getActiveMenu(): object|false
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (empty($menu->id))
        {
            return false;
        }

        return $this->getMenuById($menu->id);
    }

    private function getItemId(): int
    {
        $id = RL_Input::getInt('Itemid', 0);

        if ($id)
        {
            return $id;
        }

        $menu = $this->getActiveMenu();

        return $menu->id ?? 0;
    }

    private function getMenuById(int $id = 0): object|false
    {
        $menu = JFactory::getApplication()->getMenu()->getItem($id);

        if (empty($menu->id))
        {
            return false;
        }

        if ($menu->type == 'alias')
        {
            $params = $menu->getParams();

            return $this->getMenuById($params->get('aliasoptions'));
        }

        return $menu;
    }

    private function getRequest(): object
    {
        $return_early = ! is_null(self::$_request);

        $app = JFactory::getApplication();

        $id = RL_Input::getAsArray(
            'a_id',
            RL_Input::getAsArray('id', [0])
        );

        self::$_request = (object) [
            'idname' => 'id',
            'option' => RL_Input::getCmd('option'),
            'view'   => RL_Input::getCmd('view'),
            'task'   => RL_Input::getCmd('task'),
            'layout' => RL_Input::getString('layout'),
            'Itemid' => $this->getItemId(),
            'id'     => (int) $id[0],
        ];

        switch (self::$_request->option)
        {
            case 'com_categories':
                $extension              = RL_Input::getCmd('extension');
                self::$_request->option = $extension ?: 'com_content';
                self::$_request->view   = 'category';
                break;

            case 'com_breezingforms':
                if (self::$_request->view == 'article')
                {
                    self::$_request->option = 'com_content';
                }
                break;

            default:
                break;
        }

        $this->initRequest(self::$_request);

        if ( ! self::$_request->id)
        {
            $cid                = RL_Input::getAsArray('cid', [0]);
            self::$_request->id = (int) $cid[0];
        }

        if ($return_early)
        {
            return self::$_request;
        }

        // if no id is found, check if menuitem exists to get view and id
        if ( ! RL_Document::isClient('site')
            || (self::$_request->option && self::$_request->id)
        )
        {
            return self::$_request;
        }

        /* @var JMenuItem $menu_item */
        $menu_item = empty(self::$_request->Itemid)
            ? $app->getMenu('site')->getActive()
            : $app->getMenu('site')->getItem(self::$_request->Itemid);

        if ( ! $menu_item)
        {
            return self::$_request;
        }

        if ( ! self::$_request->option)
        {
            self::$_request->option = $menu_item->query['option'] ?? null;
        }

        self::$_request->view = $menu_item->query['view'] ?? null;
        self::$_request->task = $menu_item->query['task'] ?? null;

        if ( ! self::$_request->id)
        {
            self::$_request->id = $menu_item->query[self::$_request->idname] ?? $menu_item->getParams()->get(self::$_request->idname);
        }

        unset($menu_item);

        return self::$_request;
    }

    private function getRequestKeys(): array
    {
        return ['option', 'view', 'task', 'layout', 'id', 'Itemid', ...$this->request_keys];
    }

    private function mixedDataToArray(
        array|string $array = '',
        bool         $onlycommas = false
    ): array
    {
        if ( ! is_array($array))
        {
            $delimiter = ($onlycommas || ! str_contains($array, '|')) ? ',' : '|';

            return explode($delimiter, $array);
        }

        if (empty($array))
        {
            return $array;
        }

        if (isset($array[0]) && is_array($array[0]))
        {
            return $array[0];
        }

        if (count($array) === 1 && str_contains($array[0], ','))
        {
            return explode(',', $array[0]);
        }

        return $array;
    }

    private function setRequest(object|false|null $request_overrides = null): void
    {
        $this->request = $this->getRequest();

        if (empty($request_overrides))
        {
            return;
        }

        foreach ($request_overrides as $key => $value)
        {
            $key = $key == 'cid' ? 'id' : $key;

            if ( ! in_array($key, $this->getRequestKeys()))
            {
                continue;
            }

            if ($key === 'id' && is_array($value))
            {
                $value = (int) $value[0];
            }

            $this->request->{$key} = $value;
        }
    }
}
