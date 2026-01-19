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

namespace RegularLabs\Component\Conditions\Administrator\Helper;

use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\ObjectHelper as RL_Object;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

class Convert
{
    static $fields;

    public static function addRule(
        object  &$group,
        string  $type,
        ?string $selection,
        array   $extra_params = [],
        bool    $override = true
    ): void
    {
        if ($override)
        {
            self::removeRule($group, $type);
        }

        $selection = $selection ? trim($selection) : '';
        $selection = html_entity_decode($selection);
        $selection = RL_RegEx::replace('<br ?/?>', "\n", $selection);

        $extra_params = (object) $extra_params;
        $exclude      = self::prepareSelection($selection, $extra_params);

        $params = [
            'selection' => $selection,
            ...(array) $extra_params,
        ];

        $group->rules[] = (object) [
            'type'    => $type,
            'exclude' => $exclude ? 1 : 0,
            'params'  => (object) $params,
        ];

        self::createHashGroup($group);
    }

    public static function fromObject(
        object $object,
        string $name = '',
        string $extension = '',
        int    $item_id = 0,
        string $table = '',
        string $name_column = 'name'
    ): object
    {
        $name = $name ?: Helper::getForItemText($extension, $item_id, $table, $name_column);

        $object = RL_Object::changeKeyCase($object, 'underscore');
        $object = self::replaceKeyAliases($object);

        if (isset($object->matching_method))
        {
            $object->match_all = strtolower($object->matching_method ?? 'and') === 'and' ? 1 : 0;
        }

        $condition = (object) [
            'name'      => $name,
            'match_all' => $object->match_all ?? 1,
            'published' => 1,
            'groups'    => self::createGroups($object),
        ];

        $condition->hash = md5(json_encode([$condition->match_all, $condition->groups]));

        return $condition;
    }

    public static function hasRule(object &$group, string $type): bool
    {
        foreach ($group->rules as $rule)
        {
            if ($rule->type === $type)
            {
                return true;
            }
        }

        return false;
    }

    public static function removeRule(object &$group, string $type): void
    {
        foreach ($group->rules as $i => $rule)
        {
            if ($rule->type !== $type)
            {
                continue;
            }

            unset($group->rules[$i]);

            return;
        }
    }

    private static function addRuleBasicSelection(
        string $new_name,
        string $old_name,
        object $params,
        object $group,
        int    $id = 0
    ): void
    {
        self::addRule($group,
            $new_name,
            $params->{$old_name},
            [],
            $id
        );
    }

    private static function addRuleContentArticleDate(
        string $prefix,
        object $params,
        object $group,
        string $type = ''
    ): void
    {
        $type = $type ?: $prefix;

        if ( ! isset($params->{$prefix}))
        {
            return;
        }

        $exclude = self::prepareSelection($params->{$prefix}, $params);

        [$comparison, $date, $from, $to] = self::convertDateStringToDates($params->{$prefix});

        self::addRule($group,
            'content__article__date',
            $exclude . ($params->{$prefix . '_type'} ?? $type),
            [
                'comparison' => $comparison,
                'date'       => $date ?: '',
                'from'       => $from ?: '',
                'to'         => $to ?: '',
                'recurring'  => $params->{$prefix . '_recurring'} ?? 0,
            ],
            false
        );
    }

    private static function addRuleContentArticleFeatured(object $params, object $group): void
    {
        self::addRule($group,
            'content__article__featured',
            $params->featured ? '' : '!'
        );
    }

    private static function addRuleContentArticleFields(
        string $key,
        object $params,
        object $group
    ): void
    {
        $key    = RL_String::toUnderscoreCase($key);
        $fields = self::getFieldNames();

        if ( ! isset($fields[$key]))
        {
            return;
        }

        $value      = RL_DB::removeOperator($params->{$key});
        $operator   = RL_DB::getOperator($params->{$key});
        $comparison = self::convertOperatorToComparison($operator);

        self::addRule($group,
            'content__article__field',
            '',
            [
                'field'      => $fields[$key],
                'comparison' => $comparison,
                'value'      => $value,
            ]
        );
    }

    private static function addRuleContentCategory(object $params, object $group): void
    {
        self::addRule($group,
            'content__category',
            $params->category,
            [
                'include_children' => $params->category_include_children ?? 0,
                'page_types'       => $params->category_page_types ?? [],
            ]
        );
    }

    private static function addRuleDateDate(object $params, object $group): void
    {
        $exclude = self::prepareSelection($params->date, $params);

        [$comparison, $date, $from, $to] = self::convertDateStringToDates($params->date);

        self::addRule($group,
            'date__date',
            $exclude,
            [
                'comparison' => $comparison,
                'date'       => $date ?: '',
                'from'       => $from ?: '',
                'to'         => $to ?: '',
                'recurring'  => $params->date_recurring ?? 0,
            ]
        );
    }

    private static function addRuleDateSeason(object $params, object $group): void
    {
        self::addRule($group,
            'date__season',
            $params->season,
            [
                'hemisphere' => $params->season_hemisphere ?? 'northern',
            ]
        );
    }

    private static function addRuleDateTime(object $params, object $group): void
    {
        $exclude = self::prepareSelection($params->time, $params);

        [$comparison, $time, $from, $to] = self::convertDateStringToDates($params->time);

        self::addRule($group,
            'date__time',
            $exclude,
            [
                'comparison' => $comparison,
                'from'       => $from ?: ($comparison === 'after' ? $time : ''),
                'to'         => $to ?: ($comparison === 'before' ? $time : ''),
            ]
        );
    }

    private static function addRuleMenuHomePage(object $params, object $group): void
    {
        self::addRule($group,
            'menu__home_page',
            $params->home_page ? '' : '!'
        );
    }

    private static function addRuleMenuMenuItem(object $params, object $group): void
    {
        self::addRule($group,
            'menu__menu_item',
            $params->menu_item,
            [
                'include_children' => $params->menu_item_inc_children ?? 0,
            ]
        );
    }

    private static function addRuleOtherTag(object $params, object $group): void
    {
        if (empty($params->tag))
        {
            return;
        }

        self::addRule($group,
            'other__tag',
            $params->tag,
            [
                'match_all'        => $params->tag_match_all ?? 0,
                'include_children' => $params->tag_inc_children ?? 0,
            ]
        );
    }

    private static function addRuleOtherUrl(object $params, object $group): void
    {
        if (empty($params->url))
        {
            return;
        }

        self::addRule($group,
            'other__url',
            $params->url,
            [
                'case_sensitive' => $params->url_casesensitive ?? 0,
                'regex'          => $params->url_regex ?? 0,
            ]
        );
    }

    private static function addRuleVisitorAccessLevel(object $params, object $group): void
    {
        self::addRule($group,
            'visitor__access_level',
            $params->access_level,
            [
                'match_all' => $params->access_level_match_all ?? 0,
            ]
        );
    }

    private static function addRuleVisitorUserGroup(object $params, object $group): void
    {
        self::addRule($group,
            'visitor__user_group',
            $params->user_group,
            [
                'match_all'        => $params->user_group_match_all ?? 0,
                'include_children' => $params->user_group_inc_children ?? 0,
            ]
        );
    }

    /**
     * Convert date="... to ..." to $from/$to
     * Convert date=">..." to $from
     * Convert date="<..." to $to
     */
    private static function convertDateStringToDates(string $string): array
    {
        $string = str_replace(['&gt;', '&lt;'], ['>', '<'], $string);

        if (str_starts_with($string, '>'))
        {
            // only has a from date
            return [
                'after',
                substr($string, 1),
                null,
                null,
            ];
        }

        if (str_starts_with($string, '<'))
        {
            // only has a to date
            return [
                'before',
                substr($string, 1),
                null,
                null,
            ];
        }

        [$from, $to] = explode(' to ', $string . ' to ');

        // a from and to date
        return [
            'between',
            null,
            $from,
            $to,
        ];
    }

    private static function convertOperatorToComparison(string $operator): string
    {
        return match ($operator)
        {
            '!', '!=' => 'not_equals',
            '>'       => 'greater_than',
            '<'       => 'less_than',
            '>='      => 'greater_than_or_equal',
            '<='      => 'less_than_or_equal',
            '*'       => 'contains',
            default   => 'equals',
        };
    }

    private static function createGroups(object $object): array
    {
        $group = (object) [
            'match_all' => $object->match_all ?? 1,
            'rules'     => [],
        ];

        self::setRulesOnGroup($object, $group);

        if (empty($group->rules))
        {
            return [];
        }

        self::createHashGroup($group);

        return [$group];
    }

    /**
     * @param object $group
     */
    private static function createHashGroup(object &$group): void
    {
        unset($group->hash);
        $group->hash = md5(json_encode([$group->match_all, $group->rules]));
    }

    private static function getFieldIdByName(string $name): int
    {
        if (isset(static::$field_ids[$name]))
        {
            return static::$field_ids[$name];
        }

        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            ->select('a.id')
            ->from('#__fields AS a')
            ->where(RL_DB::is('a.name', $name));

        $db->setQuery($query, 0, 1);

        static::$field_ids[$name] = $db->loadResult();

        return static::$field_ids[$name];
    }

    private static function getFieldNames(): array
    {
        if ( ! is_null(static::$fields))
        {
            return static::$fields;
        }

        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            ->select(['a.id', 'a.name'])
            ->from('#__fields AS a');
        $db->setQuery($query);

        $fields         = $db->loadAssocList('name', 'id');
        static::$fields = RL_Array::changeKeyCase($fields, 'underscore');

        return static::$fields;
    }

    private static function handleMatchAllList(string &$list): bool
    {
        if ( ! str_contains($list, ' + '))
        {
            return false;
        }

        $list = RL_Array::implode(RL_Array::toArray($list, ' + '), ',');

        return true;
    }

    private static function prepareSelection(string &$selection, object &$params): bool
    {
        if (in_array($selection, ['true', 'false']))
        {
            $exclude   = $selection === 'false';
            $selection = '';

            return $exclude;
        }

        $exclude = false;

        if (strlen($selection) > 0 && $selection[0] === '!')
        {
            $exclude   = true;
            $selection = RL_RegEx::replace('^\!NOT\!', '!', $selection, 's');
            $selection = substr($selection, 1);
        }

        if ( ! isset($params->match_all))
        {
            return $exclude;
        }

        $match_all = self::handleMatchAllList($selection);

        if ( ! $match_all)
        {
            return $exclude;
        }

        $params->match_all = 1;

        return $exclude;
    }

    private static function replaceKeyAliases(object $object): object
    {
        $aliases = [
            'matching_method' => ['match_method', 'method'],
            'match_all'       => ['matchall'],

            'menu_item'       => ['menu_items', 'menuitem', 'menuitems'],
            'home_page'       => ['homepage'],
            //'date' => ['dates'],
            'season'          => ['seasons'],
            'month'           => ['months'],
            'day'             => ['days'],
            'time'            => ['times'],
            'page_type'       => [
                'page_types', 'pagetype', 'pagetypes', 'contentpagetype', 'contentpagetypes',
            ],
            'category'        => ['categories', 'cat', 'cats'],
            'article'         => ['articles'],
            //'featured' => ['featured'],
            'article_status'  => ['status', 'state', 'states', 'publish_state', 'publish_states'],
            'article_date'    => ['articles_date', 'articledate'],
            'article_author'  => ['article_authors', 'author', 'authors'],
            'content_keyword' => ['content_keywords', 'contentkeyword', 'contentkeywords'],
            'meta_keyword'    => ['meta_keywords', 'metakeyword', 'metakeywords'],
            //'field' => ['field'],
            'user'            => ['users'],
            'access_level'    => ['access_levels', 'accesslevel', 'accesslevels'],
            'user_group'      => [
                'user_groups', 'usergroup', 'usergroups', 'user_group_level', 'user_group_levels',
                'usergrouplevel', 'usergrouplevels',
            ],
            'language'        => ['languages'],
            'device'          => ['devices'],
            //'os' => ['os'],
            'browser'         => ['browsers'],
            'browser_mobile'  => [
                'browser_mobiles', 'browsermobile', 'browsermobiles', 'mobile_browser',
                'mobile_browsers', 'mobilebrowser', 'mobilebrowsers', 'mobile',
                'mobiles',
            ],
            'ip'              => ['ips', 'ip_address', 'ip_addresses', 'ipaddress', 'ipaddresses'],
            'continent'       => ['continents'],
            'country'         => ['countries'],
            'region'          => ['regions'],
            'postal_code'     => ['postal_codes', 'postalcode', 'postalcodes'],
            'tag'             => ['tags'],
            'component'       => ['components'],
            'template'        => ['templates'],
            'url'             => ['urls'],
            //'php' => ['php'],
            //'condition' => ['condition'],
        ];

        return RL_Object::replaceKeys($object, $aliases, true);
    }

    private static function setRuleOnGroupByKey(string $key, object $params, object &$group): void
    {
        $basic_selection_types = [
            'month'           => 'date__month',
            'day'             => 'date__day',
            'page_type'       => 'content__page_type',
            'article'         => 'content__article__id',
            'article_status'  => 'content__article__status',
            'article_author'  => 'content__article__author',
            'content_keyword' => 'content__article__content_keyword',
            'meta_keyword'    => 'content__article__meta_keyword',
            'user'            => 'visitor__user',
            'language'        => 'visitor__language',
            'ip'              => 'visitor__ip',
            'device'          => 'agent__device',
            'os'              => 'agent__os',
            'browser'         => 'agent__browser',
            'browser_mobile'  => 'agent__browser_mobile',
            'continent'       => 'geo__continent',
            'country'         => 'geo__country',
            'region'          => 'geo__region',
            'postal_code'     => 'geo__postal_code',
            'component'       => 'other__component',
            'template'        => 'other__template',
            'php'             => 'other__php',
            'condition'       => 'other__condition',
        ];

        if (isset($basic_selection_types[$key]))
        {
            self::addRule($group,
                $basic_selection_types[$key],
                $params->{$key},
                [],
                false
            );

            return;
        }

        switch ($key)
        {
            case 'menu_item':
                self::addRuleMenuMenuItem($params, $group);

                return;

            case 'home_page':
                self::addRuleMenuHomepage($params, $group);

                return;

            case 'date':
                self::addRuleDateDate($params, $group);

                return;

            case 'season':
                self::addRuleDateSeason($params, $group);

                return;

            case 'time':
                self::addRuleDateTime($params, $group);

                return;

            case 'category':
                self::addRuleContentCategory($params, $group);

                return;

            case 'featured':
                self::addRuleContentArticleFeatured($params, $group);

                return;

            case 'article_date':
                self::addRuleContentArticleDate('article_date', $params, $group, 'created');

                return;

            case 'created':
            case 'modified':
            case 'publish_up':
            case 'publish_down':
                self::addRuleContentArticleDate($key, $params, $group);

                return;

            case 'access_level':
                self::addRuleVisitorAccessLevel($params, $group);

                return;

            case 'user_group':
                self::addRuleVisitorUserGroup($params, $group);

                return;

            case 'tag':
                self::addRuleOtherTag($params, $group);

                return;

            case 'url':
                self::addRuleOtherUrl($params, $group);

                return;

            default:
                self::addRuleContentArticleFields($key, $params, $group);

                return;
        }
    }

    private static function setRulesOnGroup(object $params, object &$group): void
    {
        if ( ! isset($group->rules))
        {
            $group->rules = [];
        }

        foreach ($params as $key => $value)
        {
            self::setRuleOnGroupByKey($key, $params, $group);
        }
    }
}
