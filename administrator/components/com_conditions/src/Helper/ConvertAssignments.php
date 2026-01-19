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

use RegularLabs\Component\Conditions\Administrator\Model\ItemModel;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\StringHelper as RL_String;

class ConvertAssignments
{
    static $article_group_id   = 0;
    static $conditions_by_hash = [];
    static $conditions_by_item = [];
    static $field_ids          = [];
    static $mirrors            = [];

    public static function convert(
        $extension,
        $params_table,
        $name_table = '',
        $name_column = 'name',
        $id_column = 'id',
        $excludes = []
    ): bool
    {
        $name_table = $name_table ?: $params_table;

        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('a.*')
            ->from($db->quoteName('#__' . $params_table, 'a'))
            ->where(RL_DB::like($db->quoteName('a.params'), '*"assignto_*'));
        $db->setQuery($query);

        $items = $db->loadObjectList();

        if (empty($items))
        {
            return true;
        }

        foreach ($items as $item)
        {
            if ( ! self::handleItem($item, $extension, $params_table, $name_table, $name_column, $id_column, $excludes))
            {
                return false;
            }
        }

        foreach (self::$mirrors as $source_id => $mirrored_ids)
        {
            if ($source_id < 0)
            {
                self::createAndMapReverseCondition($source_id * -1, $mirrored_ids, $extension, $params_table, $name_table, $name_column, $id_column);
                continue;
            }

            self::mapFromSourceCondition($source_id, $mirrored_ids, $extension, $params_table, $name_table, $name_column, $id_column);
        }

        return true;
    }

    public static function getCondition(
        object $item,
        string $extension,
        string $table,
        string $name_column = 'name',
        array  $excludes = []
    )
    {
        RL_Language::load('com_conditions');

        $name = Helper::getForItemText($extension, $item->id, $table, $name_column);

        $condition = (object) [
            'name'      => $name,
            'match_all' => ($item->params->match_method ?? 'and') === 'and' ? 1 : 0,
            'published' => 1,
            'groups'    => [
                (object) [
                    'match_all' => ($item->params->match_method ?? 'and') === 'and' ? 1 : 0,
                    'rules'     => [],
                ],
                (object) [
                    'match_all' => 1,
                    'rules'     => [],
                ],
            ],
        ];

        self::$article_group_id = $condition->match_all ? 0 : 1;

        self::addRules($item->params, $condition->groups, $excludes);

        if (empty($condition->groups[0]->rules))
        {
            unset($condition->groups[0]);
        }

        if (empty($condition->groups[1]->rules))
        {
            unset($condition->groups[1]);
        }

        $condition->hash = md5(json_encode([$condition->match_all, $condition->groups]));

        return $condition;
    }

    public static function saveConditionByItem(
        object $item,
        string $extension,
        string $name_table,
        string $name_column = 'name',
        array  $excludes = []
    ): bool
    {
        $condition = self::getCondition($item, $extension, $name_table, $name_column, $excludes);

        if (empty($condition->groups))
        {
            return true;
        }

        $model = new ItemModel;

        if (isset(self::$conditions_by_hash[$condition->hash]))
        {
            $condition_id = self::$conditions_by_hash[$condition->hash];

            $model->map(
                $condition_id,
                $extension,
                $item->id,
                $name_table,
                $name_column
            );

            self::$conditions_by_item[$item->id] = $condition_id;

            return true;
        }

        $condition_saved = $model->saveByObject(
            $condition,
            $extension,
            $item->id,
            $name_table,
            $name_column
        );

        if ( ! $condition_saved)
        {
            return false;
        }

        self::$conditions_by_item[$item->id]        = $condition->id;
        self::$conditions_by_hash[$condition->hash] = $condition->id;

        return true;
    }

    private static function addRule(
        array        &$groups,
        string       $type,
        bool|int     $exclude,
        object|array $params = [],
        int          $id = 0
    ): void
    {
        $groups[$id]->rules[] = (object) [
            'type'    => $type,
            'exclude' => $exclude ? 1 : 0,
            'params'  => (object) $params,
        ];
    }

    private static function addRuleAgentBrowser(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_browsers)
            || empty($params->assignto_browsers_selection)
        )
        {
            return;
        }

        self::addRule($groups,
            'agent__browser',
            $params->assignto_browsers == 2,
            [
                'selection' => $params->assignto_browsers_selection ?? [],
            ]
        );
    }

    private static function addRuleAgentBrowserMobile(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_browsers)
            || empty($params->assignto_mobile_selection)
        )
        {
            return;
        }

        self::addRule($groups,
            'agent__browser_mobile',
            $params->assignto_browsers == 2,
            [
                'selection' => $params->assignto_mobile_selection ?? [],
            ]
        );
    }

    private static function addRuleBasic(
        string $new_name,
        string $old_name,
        object $params,
        array  &$groups,
        int    $id = 0
    ): void
    {
        if (empty($params->{$old_name}))
        {
            return;
        }

        self::addRule($groups,
            $new_name,
            $params->{$old_name} == 2,
            [
                'selection' => $params->{$old_name . '_selection'} ?? [],
            ],
            $id
        );
    }

    private static function addRuleCategory(
        string $new_name,
        string $old_name,
        object $params,
        array  &$groups
    ): void
    {
        if (empty($params->{$old_name}))
        {
            return;
        }

        self::addRule($groups,
            $new_name,
            $params->{$old_name} == 2,
            [
                'selection'        => $params->{$old_name . '_selection'} ?? [],
                'include_children' => $params->{$old_name . '_inc_children'} ?? 0,
                'page_types'       => self::correctPageTypeValues($params->{$old_name . '_inc'} ?? []),
            ]
        );
    }

    private static function addRuleContentArticleAuthor(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_articles)
            || empty($params->assignto_articles_authors)
        )
        {
            return;
        }

        self::addRule($groups,
            'content__article__author',
            $params->assignto_articles == 2,
            [
                'selection' => $params->assignto_articles_authors ?? [],
            ],
            self::$article_group_id
        );
    }

    private static function addRuleContentArticleContentKeyword(
        object $params,
        array  &$groups
    ): void
    {
        if (
            empty($params->assignto_articles)
            || empty($params->assignto_articles_authors)
        )
        {
            return;
        }

        self::addRule($groups,
            'content__article__content_keyword',
            $params->assignto_articles == 2,
            [
                'selection' => $params->assignto_articles_content_keywords ?? '',
            ],
            self::$article_group_id
        );
    }

    private static function addRuleContentArticleDate(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_articles)
            || ! isset($params->assignto_articles_date)
            || $params->assignto_articles_date == ''
        )
        {
            return;
        }

        $comparison = $params->assignto_articles_date_comparison ?? 'between';

        if ($comparison == 'fromto')
        {
            $comparison = 'between';
        }

        self::addRule($groups,
            'content__article__date',
            $params->assignto_articles == 2,
            [
                'selection'  => $params->assignto_articles_date ?? '',
                'comparison' => $comparison,
                'date'       => $params->assignto_articles_date_date ?? '',
                'from'       => $params->assignto_articles_date_from ?? '',
                'to'         => $params->assignto_articles_date_to ?? '',
                'recurring'  => 0,
            ],
            self::$article_group_id
        );
    }

    private static function addRuleContentArticleFeatured(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_articles)
            || ! isset($params->assignto_articles_featured)
            || $params->assignto_articles_featured == ''
        )
        {
            return;
        }

        $exclude = $params->assignto_articles == 2
            ? $params->assignto_articles_featured
            : ! $params->assignto_articles_featured;

        self::addRule($groups,
            'content__article__featured',
            $exclude,
            [],
            self::$article_group_id
        );
    }

    private static function addRuleContentArticleField(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_articles)
            || empty($params->assignto_articles_fields)
        )
        {
            return;
        }

        foreach ($params->assignto_articles_fields as $field)
        {
            $field_id = self::getFieldIdByName($field->field);

            if ( ! $field_id)
            {
                continue;
            }

            self::addRule($groups,
                'content__article__field',
                $params->assignto_articles == 2,
                [
                    'field'      => $field_id,
                    'comparison' => $field->field_comparison ?? '',
                    'value'      => $field->field_value ?? '',
                ],
                self::$article_group_id
            );
        }
    }

    private static function addRuleContentArticleMetaKeyword(object $params, array &$groups): void
    {
        if (
            empty($params->assignto_articles)
            || empty($params->assignto_articles_keywords)
        )
        {
            return;
        }

        self::addRule($groups,
            'content__article__meta_keyword',
            $params->assignto_articles == 2,
            [
                'selection' => $params->assignto_articles_keywords ?? '',
            ],
            self::$article_group_id
        );
    }

    private static function addRuleDateDate(object $params, array &$groups): void
    {
        if (empty($params->assignto_date))
        {
            return;
        }

        self::addRule($groups,
            'date__date',
            $params->assignto_date == 2,
            [
                'comparison' => 'between',
                'from'       => $params->assignto_date_publish_up ?? '',
                'to'         => $params->assignto_date_publish_down ?? '',
                'recurring'  => $params->assignto_date_recurring ?? 0,
            ]
        );
    }

    private static function addRuleDateSeason(object $params, array &$groups): void
    {
        if (empty($params->assignto_seasons))
        {
            return;
        }

        self::addRule($groups,
            'date__season',
            $params->assignto_seasons == 2,
            [
                'selection'  => $params->assignto_seasons_selection ?? [],
                'hemisphere' => $params->assignto_seasons_hemisphere ?? 'northern',
            ]
        );
    }

    private static function addRuleDateTime(object $params, array &$groups): void
    {
        if (empty($params->assignto_time))
        {
            return;
        }

        self::addRule($groups,
            'date__time',
            $params->assignto_time == 2,
            [
                'comparison' => 'between',
                'from'       => $params->assignto_time_publish_up ?? '',
                'to'         => $params->assignto_time_publish_down ?? '',
            ]
        );
    }

    private static function addRuleMenuHomePage(object $params, array &$groups): void
    {
        if (empty($params->assignto_homepage))
        {
            return;
        }

        self::addRule($groups,
            'menu__home_page',
            $params->assignto_homepage == 2
        );
    }

    private static function addRuleMenuMenuItem(object $params, array &$groups): void
    {
        if (empty($params->assignto_menuitems))
        {
            return;
        }

        self::addRule($groups,
            'menu__menu_item',
            $params->assignto_menuitems == 2,
            [
                'selection'        => $params->assignto_menuitems_selection ?? [],
                'include_children' => $params->assignto_menuitems_inc_children ?? 0,
            ]
        );
    }

    private static function addRuleOtherTag(object $params, array &$groups): void
    {
        if (empty($params->assignto_tags))
        {
            return;
        }

        self::addRule($groups,
            'other__tag',
            $params->assignto_tags == 2,
            [
                'selection'        => $params->assignto_tags_selection ?? [],
                'match_all'        => $params->assignto_tags_match_all ?? 0,
                'include_children' => $params->assignto_tags_inc_children ?? 0,
            ]
        );
    }

    private static function addRuleOtherUrl(object $params, array &$groups): void
    {
        if (empty($params->assignto_urls))
        {
            return;
        }

        self::addRule($groups,
            'other__url',
            $params->assignto_urls == 2,
            [
                'selection'      => $params->assignto_urls_selection ?? [],
                'case_sensitive' => $params->assignto_urls_casesensitive ?? 0,
                'regex'          => $params->assignto_urls_regex ?? 0,
            ]
        );
    }

    private static function addRuleVisitorUserGroup(object $params, array &$groups): void
    {
        if (empty($params->assignto_usergrouplevels))
        {
            return;
        }

        self::addRule($groups,
            'visitor__user_group',
            $params->assignto_usergrouplevels == 2,
            [
                'selection'        => $params->assignto_usergrouplevels_selection ?? [],
                'match_all'        => $params->assignto_usergrouplevels_match_all ?? 0,
                'include_children' => $params->assignto_usergrouplevels_inc_children ?? 0,
            ]
        );
    }

    private static function addRules(object $params, array &$groups, array $excludes = []): void
    {
        self::addRuleMenuMenuItem($params, $groups);

        empty($excludes['homepage']) && self::addRuleMenuHomepage($params, $groups);

        if (empty($excludes['date']))
        {
            self::addRuleDateDate($params, $groups);
            self::addRuleDateSeason($params, $groups);
            self::addRuleBasic('date__month', 'assignto_months', $params, $groups);
            self::addRuleBasic('date__day', 'assignto_days', $params, $groups);
            self::addRuleDateTime($params, $groups);
        }

        if (empty($excludes['content']))
        {
            self::addRuleBasic('content__page_type', 'assignto_contentpagetypes', $params, $groups);
            self::addRuleCategory('content__category', 'assignto_cats', $params, $groups);

            self::addRuleBasic('content__article__id', 'assignto_articles', $params, $groups, self::$article_group_id);
            self::addRuleContentArticleFeatured($params, $groups);
            self::addRuleContentArticleDate($params, $groups);
            self::addRuleContentArticleAuthor($params, $groups);
            self::addRuleContentArticleContentKeyword($params, $groups);
            self::addRuleContentArticleMetaKeyword($params, $groups);
            self::addRuleContentArticleField($params, $groups);
        }

        empty($excludes['users']) && self::addRuleBasic('visitor__user', 'assignto_users', $params, $groups);
        empty($excludes['usergrouplevels']) && self::addRuleVisitorUserGroup($params, $groups);
        empty($excludes['languages']) && self::addRuleBasic('visitor__language', 'assignto_languages', $params, $groups);
        empty($excludes['ips']) && self::addRuleBasic('visitor__ip', 'assignto_ips', $params, $groups);

        empty($excludes['devices']) && self::addRuleBasic('agent__device', 'assignto_devices', $params, $groups);
        empty($excludes['os']) && self::addRuleBasic('agent__os', 'assignto_os', $params, $groups);

        if (empty($excludes['browsers']))
        {
            self::addRuleAgentBrowser($params, $groups);
            self::addRuleAgentBrowserMobile($params, $groups);
        }

        if (empty($excludes['geo']))
        {
            self::addRuleBasic('geo__continent', 'assignto_geocontinents', $params, $groups);
            self::addRuleBasic('geo__country', 'assignto_geocountries', $params, $groups);
            self::addRuleBasic('geo__region', 'assignto_georegions', $params, $groups);
            self::addRuleBasic('geo__postal_code', 'assignto_geopostalcodes', $params, $groups);
        }

        empty($excludes['tags']) && self::addRuleOtherTag($params, $groups);
        empty($excludes['components']) && self::addRuleBasic('other__component', 'assignto_components', $params, $groups);
        empty($excludes['templates']) && self::addRuleBasic('other__template', 'assignto_templates', $params, $groups);
        empty($excludes['urls']) && self::addRuleOtherUrl($params, $groups);
        empty($excludes['php']) && self::addRuleBasic('other__php', 'assignto_php', $params, $groups);

        if (empty($excludes['flexicontent']))
        {
            self::addRuleBasic('flexicontent__page_type', 'assignto_flexicontentpagetypes', $params, $groups);
            self::addRuleCategory('flexicontent__tag', 'assignto_flexicontenttags', $params, $groups);
            self::addRuleBasic('flexicontent__type', 'assignto_flexicontenttypes', $params, $groups);
        }

        if (empty($excludes['hikashop']))
        {
            self::addRuleBasic('hikashop__page_type', 'assignto_hikashoppagetypes', $params, $groups);
            self::addRuleCategory('hikashop__category', 'assignto_hikashopcats', $params, $groups);
            self::addRuleBasic('hikashop__item', 'assignto_hikashopproducts', $params, $groups);
        }

        if (empty($excludes['zoo']))
        {
            self::addRuleBasic('zoo__page_type', 'assignto_zoopagetypes', $params, $groups);
            self::addRuleCategory('zoo__category', 'assignto_flexicontentcats', $params, $groups);
            self::addRuleBasic('zoo__item', 'assignto_zooitems', $params, $groups);
        }
    }

    private static function correctPageTypeValues(?array $values = []): array
    {
        if (empty($values))
        {
            return [];
        }

        $aliases = [
            'inc_cats'   => 'categories',
            'inc_arts'   => 'articles',
            'inc_others' => 'others',
            'inc_tags'   => 'tags',
            'inc_items'  => 'items',
        ];

        $page_types = [];

        foreach ($values as $page_type)
        {
            if ($page_type === 'x')
            {
                continue;
            }

            $page_types[] = $aliases[$page_type] ?? $page_type;
        }

        return $page_types;
    }

    private static function createAndMapReverseCondition(
        int    $source_id,
        array  $items,
        string $extension,
        string $params_table,
        string $name_table,
        string $name_column = 'name',
        string $id_column = 'id'
    ): void
    {
        if ( ! isset(self::$conditions_by_item[$source_id]))
        {
            return;
        }

        $model = new ItemModel;

        $first_item = array_shift($items);

        $condition = $model->getConditionById(self::$conditions_by_item[$source_id]);

        $reverse_condition = self::getReverseCondition($condition);

        $condition_saved = $model->saveByObject(
            $reverse_condition,
            $extension,
            $first_item->id,
            $name_table,
            $name_column
        );

        if ( ! $condition_saved)
        {
            return;
        }

        self::removeAssignments($first_item, $params_table, $id_column);

        foreach ($items as $item)
        {
            $model->map(
                $reverse_condition->id,
                $extension,
                $item->id,
                $name_table,
                $name_column
            );

            self::removeAssignments($item, $params_table, $id_column);
        }
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

        static::$field_ids[$name] = (int) $db->loadResult();

        return static::$field_ids[$name];
    }

    private static function getReverseCondition(object $source_condition): object
    {
        RL_Language::load('com_conditions');

        $reverse_condition = (object) [
            'name'      => 'Reverse: ' . RL_String::escape($source_condition->name),
            'match_all' => 1,
            'published' => 1,
            'groups'    => [
                (object) [
                    'match_all' => 1,
                    'rules'     => [],
                ],
            ],
        ];

        self::addRule($reverse_condition->groups, 'other__condition', true, (object) [
            'selection' => $source_condition->id,
        ]);

        return $reverse_condition;
    }

    private static function handleItem(
        object $item,
        string $extension,
        string $params_table,
        string $name_table,
        string $name_column = 'name',
        string $id_column = 'id',
        array  $excludes = []
    ): bool
    {
        if (empty($item->params))
        {
            return true;
        }

        $item->id     = $item->{$id_column};
        $item->params = json_decode($item->params);

        if ( ! empty($item->mirror_id) && ! empty($item->mirror_id))
        {
            if ( ! isset(self::$mirrors[$item->mirror_id]))
            {
                self::$mirrors[$item->mirror_id] = [];
            }

            self::$mirrors[$item->mirror_id][] = $item;

            return true;
        }

        if ( ! self::saveConditionByItem($item, $extension, $name_table, $name_column, $excludes))
        {
            return false;
        }

        self::removeAssignments($item, $params_table, $id_column);

        return true;
    }

    private static function mapFromSourceCondition(
        int    $source_id,
        array  $items,
        string $extension,
        string $params_table,
        string $name_table,
        string $name_column = 'name',
        string $id_column = 'id'
    ): void
    {
        if ( ! isset(self::$conditions_by_item[$source_id]))
        {
            return;
        }

        $condition_id = self::$conditions_by_item[$source_id];

        $model = new ItemModel;

        foreach ($items as $item)
        {
            $model->map(
                $condition_id,
                $extension,
                $item->id,
                $name_table,
                $name_column
            );

            self::removeAssignments($item, $params_table, $id_column);
        }
    }

    private static function removeAssignments(
        object $item,
        string $table,
        string $id_column = 'id'
    ): void
    {
        foreach ($item->params as $key => $value)
        {
            if (
                in_array($key, [
                    'mirror_module',
                    'mirror_moduleid',
                    'match_method',
                    'show_assignments',
                    'has_geoip_library',
                ])
                || str_starts_with($key, 'assignto_')
            )
            {
                unset($item->params->{$key});
            }
        }

        $params = json_encode(($item->params));

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__' . $table))
            ->set($db->quoteName('params') . ' = ' . $db->quote($params))
            ->where($db->quoteName($id_column) . ' = ' . (int) $item->id);

        $db->setQuery($query)->execute();
    }
}
