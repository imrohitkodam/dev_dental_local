<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use function YOOtheme\trans;

class CategoryQueryType
{
    protected static $view = ['com_content.category'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'category' => [
                    'type' => 'Category',
                    'metadata' => [
                        'label' => trans('Category'),
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
            return $root['category'];
        }
    }
}
