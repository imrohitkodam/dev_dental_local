<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Helper\TagsHelper;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
use YOOtheme\Builder\Joomla\Source\TagHelper;
use function YOOtheme\trans;

class TagType
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

                'description' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Description'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'images' => [
                    'type' => 'Images',
                    'metadata' => [
                        'label' => '',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::images',
                    ],
                ],

                'hits' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Hits'),
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

                'tags' => [
                    'type' => [
                        'listOf' => 'Tag',
                    ],
                    'metadata' => [
                        'label' => trans('Child Tags'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::tags',
                    ],
                ],

                'items' => [
                    'type' => [
                        'listOf' => 'TagItem',
                    ],
                    'args' => [
                        'include_children' => [
                            'type' => 'Boolean',
                        ],
                        'typesr' => [
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
                        'order' => [
                            'type' => 'String',
                        ],
                        'order_direction' => [
                            'type' => 'String',
                        ],
                        'order_alphanum' => [
                            'type' => 'Boolean',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Tagged Items'),
                        'arguments' => [
                            'typesr' => [
                                'label' => trans('Type'),
                                'type' => 'select',
                                'description' => trans('Set the type of tagged items.'),
                                'options' => array_merge(
                                    [trans('All types') => ''],
                                    ...array_map(
                                        fn($type) => [$type->type_title => (string) $type->type_id],
                                        TagsHelper::getTypes('array'),
                                    ),
                                ),
                            ],
                            'include_children' => [
                                'label' => trans('Filter'),
                                'text' => trans('Include items from child tags'),
                                'type' => 'checkbox',
                            ],
                            '_offset' => [
                                'description' => trans(
                                    'Set the starting point and limit the number of tagged items.',
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
                                        'default' => 10,
                                        'attrs' => [
                                            'min' => 1,
                                        ],
                                    ],
                                ],
                            ],
                            '_order' => [
                                'type' => 'grid',
                                'width' => '1-2',
                                'fields' => [
                                    'order' => [
                                        'label' => trans('Order'),
                                        'type' => 'select',
                                        'default' => 'core_publish_up',
                                        'options' => [
                                            trans('Published') => 'core_publish_up',
                                            trans('Unpublished') => 'core_publish_down',
                                            trans('Created') => 'core_created_time',
                                            trans('Modified') => 'core_modified_time',
                                            trans('Alphabetical') => 'core_title',
                                            trans('Hits') => 'core_hits',
                                            trans('Ordering') => 'core_ordering',
                                            trans('Random') => 'rand',
                                        ],
                                    ],
                                    'order_direction' => [
                                        'label' => trans('Direction'),
                                        'type' => 'select',
                                        'default' => 'DESC',
                                        'options' => [
                                            trans('Ascending') => 'ASC',
                                            trans('Descending') => 'DESC',
                                        ],
                                    ],
                                ],
                            ],
                            'order_alphanum' => [
                                'text' => trans('Alphanumeric Ordering'),
                                'type' => 'checkbox',
                            ],
                        ],
                        'directives' => [],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::items',
                    ],
                ],

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
                'label' => trans('Tag'),
            ],
        ];
    }

    public static function images($tag)
    {
        return json_decode($tag->images);
    }

    public static function link($tag)
    {
        return RouteHelper::getTagRoute("{$tag->id}:{$tag->alias}");
    }

    public static function tags($tag)
    {
        return TagHelper::query(['parent_id' => $tag->id]);
    }

    public static function items($tag, $args)
    {
        $items = TagHelper::getItems($tag->id, $args);

        foreach ($items as $item) {
            if (($item->content_type_title ?? '') === 'Article') {
                $item->id = $item->content_item_id ?? '0';
                $item->catid = $item->core_catid ?? '0';
                $item->language = $item->core_language ?? '*';
            }
        }

        return $items;
    }
}
