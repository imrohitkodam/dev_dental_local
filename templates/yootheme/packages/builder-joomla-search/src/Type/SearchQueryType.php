<?php

namespace YOOtheme\Builder\Joomla\Search\Type;

use function YOOtheme\trans;

class SearchQueryType
{
    protected static $view = ['com_search.search'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'search' => [
                    'type' => 'Search',
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
            return $root['search'];
        }
    }
}
