<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use function YOOtheme\trans;

class SmartSearchQueryType
{
    protected static $view = ['com_finder.search', '_search'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'smartSearch' => [
                    'type' => 'SmartSearch',
                    'metadata' => [
                        'label' => trans('Search'),
                        'view' => static::$view,
                        'group' => trans('Page'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['search'] ?? null;
        }
    }
}
