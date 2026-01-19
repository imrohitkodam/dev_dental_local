<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\User\User;
use YOOtheme\Config;
use function YOOtheme\app;
use function YOOtheme\trans;

class MenuItemType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Title'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],

                'icon' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Icon'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],

                'subtitle' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Subtitle'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::data',
                    ],
                ],

                'link' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::link',
                    ],
                ],

                'active' => [
                    'type' => 'Boolean',
                    'metadata' => [
                        'label' => trans('Active'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::active',
                    ],
                ],

                'type' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Type'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::type',
                    ],
                ],
            ] +
                (version_compare(JVERSION, '4.0', '>')
                    ? [
                        'parent' => [
                            'type' => 'MenuItem',
                            'metadata' => [
                                'label' => trans('Parent Menu Item'),
                            ],
                            'extensions' => [
                                'call' => __CLASS__ . '::parent',
                            ],
                        ],

                        'children' => [
                            'type' => [
                                'listOf' => 'MenuItem',
                            ],
                            'metadata' => [
                                'label' => trans('Child Menu Items'),
                            ],
                            'extensions' => [
                                'call' => __CLASS__ . '::children',
                            ],
                        ],
                    ]
                    : []) + [
                    'alias' => [
                        'type' => 'String',
                        'metadata' => [
                            'label' => trans('Alias'),
                        ],
                    ],

                    'id' => [
                        'type' => 'String',
                        'metadata' => [
                            'label' => trans('ID'),
                        ],
                    ],
                ],
            'metadata' => [
                'type' => true,
                'label' => trans('Menu Item'),
            ],
        ];
    }

    public static function link($item)
    {
        $link = $item->link;

        if ($item->type === 'alias' && str_ends_with($link, 'Itemid=')) {
            $link .= "&Itemid={$item->getParams()->get('aliasoptions')}";
        }

        if (str_starts_with($link, 'index.php?') && !str_contains($link, 'Itemid=')) {
            $link .= "&Itemid={$item->id}";
        }

        return $link;
    }

    public static function active($item): bool
    {
        $active = Factory::getApplication()->getMenu()->getActive();

        if (!$active) {
            return false;
        }

        $alias_id = $item->getParams()->get('aliasoptions');

        // set active state
        if ($item->id == $active->id || ($item->type == 'alias' && $alias_id == $active->id)) {
            return true;
        }

        if (in_array($item->id, $active->tree)) {
            return true;
        } elseif ($item->type == 'alias') {
            if (count($active->tree) > 0 && $alias_id == $active->tree[count($active->tree) - 1]) {
                return true;
            } elseif (in_array($alias_id, $active->tree) && !in_array($alias_id, $item->tree)) {
                return true;
            }
        }
        return false;
    }

    public static function data($item, $args, $context, $info)
    {
        $value = app(Config::class)->get("~theme.menu.items.{$item->id}.{$info->fieldName}");

        if ($info->fieldName === 'image' && empty($value)) {
            return $item->getParams()['menu_image'];
        }

        return $value;
    }

    public static function type($item): string
    {
        if ($item->type === 'separator') {
            return 'divider';
        }
        if ($item->type === 'heading') {
            return 'heading';
        }

        return '';
    }

    public static function parent($item, $args, $context, $info)
    {
        return $item->getParent();
    }

    public static function children($item, $args, $context, $info)
    {
        $groups = app(User::class)->getAuthorisedViewLevels();
        $language = Multilanguage::isEnabled() ? [Factory::getLanguage()->getTag(), '*'] : false;

        return array_filter(
            $item->getChildren(),
            fn($child) => $child->getParams()->get('menu_show', true) &&
                in_array($child->access, $groups) &&
                (!$language || in_array($child->language, $language)),
        );
    }
}
