<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Categories\Categories;
use function YOOtheme\trans;

class SmartSearchItemsQueryType
{
    protected static $view = ['com_finder.search', '_search'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'smartSearchItem' => [
                    'type' => 'SmartSearchItem',
                    'args' => [
                        'catid' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                        'offset' => [
                            'type' => 'Int',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Item'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                        'fields' => [
                            'catid' => [
                                'label' => trans('Filter by Root Categories'),
                                'description' => trans(
                                    'Filter items visually by the selected root categories.',
                                ),
                                'type' => 'select',
                                'default' => [],
                                'options' => [['evaluate' => 'yootheme.builder.root_categories']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                            'offset' => [
                                'label' => trans('Start'),
                                'type' => 'number',
                                'default' => 0,
                                'modifier' => 1,
                                'attrs' => [
                                    'min' => 1,
                                    'required' => true,
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveSingle',
                    ],
                ],
                'smartSearchItems' => [
                    'type' => [
                        'listOf' => 'SmartSearchItem',
                    ],
                    'args' => [
                        'catid' => [
                            'type' => [
                                'listOf' => 'String',
                            ],
                        ],
                        'offset' => [
                            'type' => 'Int',
                        ],
                        'limit' => [
                            'type' => 'Int',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Items'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                        'fields' => [
                            'catid' => [
                                'label' => trans('Filter by Root Categories'),
                                'description' => trans(
                                    'Filter items visually by the selected root categories.',
                                ),
                                'type' => 'select',
                                'default' => [],
                                'options' => [['evaluate' => 'yootheme.builder.root_categories']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ],
                            '_offset' => [
                                'description' => trans(
                                    'Set the starting point and limit the number of items.',
                                ),
                                'type' => 'grid',
                                'width' => '1-2',
                                'fields' => [
                                    'offset' => [
                                        'label' => trans('Start'),
                                        'type' => 'number',
                                        'default' => 0,
                                        'modifier' => 1,
                                        'attrs' => [
                                            'min' => 1,
                                            'required' => true,
                                        ],
                                    ],
                                    'limit' => [
                                        'label' => trans('Quantity'),
                                        'type' => 'limit',
                                        'attrs' => [
                                            'placeholder' => trans('No limit'),
                                            'min' => 0,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root, array $args)
    {
        $args += [
            'catid' => null,
            'offset' => 0,
            'limit' => null,
        ];

        if (in_array($root['template'] ?? '', static::$view)) {
            $items = $root['items'] ?? [];

            if ($args['catid']) {
                $rootCategories = array_map(
                    fn($catId) => Categories::getInstance('content', ['countItems' => true])->get(
                        $catId,
                    ),
                    $args['catid'],
                );

                if (!$rootCategories) {
                    return [];
                }

                $items = array_filter($items, function ($item) use ($rootCategories) {
                    $id = $item->getElement('catid');

                    if (!$id || $item->getElement('context') !== 'com_content.article') {
                        return false;
                    }

                    $category = Categories::getInstance('content', ['countItems' => true])->get(
                        $id,
                    );

                    if (!$category) {
                        return false;
                    }

                    foreach ($rootCategories as $rootCategory) {
                        if (array_key_exists($rootCategory->id, $category->getPath())) {
                            return true;
                        }
                    }

                    return false;
                });
            }

            if ($args['offset'] || $args['limit']) {
                $items = array_slice($items, (int) $args['offset'], (int) $args['limit'] ?: null);
            }

            return $items;
        }
    }

    public static function resolveSingle($root, array $args)
    {
        return static::resolve($root, $args + ['limit' => 1])[0] ?? null;
    }
}
