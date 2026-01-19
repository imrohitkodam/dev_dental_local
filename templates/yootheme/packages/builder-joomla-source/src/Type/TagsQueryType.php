<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use function YOOtheme\trans;

class TagsQueryType
{
    protected static $view = ['com_tags.tag', 'com_tags.tags'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'tagsSingle' => [
                    'type' => 'Tag',
                    'args' => [
                        'offset' => [
                            'type' => 'Int',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Tag'),
                        'view' => static::$view,
                        'group' => trans('Page'),
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
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveSingle',
                    ],
                ],
                'tags' => [
                    'type' => [
                        'listOf' => 'Tag',
                    ],
                    'args' => [
                        'offset' => [
                            'type' => 'Int',
                        ],
                        'limit' => [
                            'type' => 'Int',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Tags'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                        'fields' => [
                            '_offset' => [
                                'description' => trans(
                                    'Set the starting point and limit the number of tags.',
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
            'offset' => 0,
            'limit' => null,
        ];

        if (in_array($root['template'] ?? '', static::$view)) {
            $tags = $root['tags'];

            if ($args['offset'] || $args['limit']) {
                $tags = array_slice($tags, (int) $args['offset'], (int) $args['limit'] ?: null);
            }

            return $tags;
        }
    }

    public static function resolveSingle($root, array $args)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['tags'][$args['offset'] ?? 0] ?? null;
        }
    }
}
