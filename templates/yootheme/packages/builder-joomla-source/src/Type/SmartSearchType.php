<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\Component\Finder\Site\Helper\RouteHelper;
use function YOOtheme\trans;

class SmartSearchType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'searchword' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Search Word'),
                    ],
                ],

                'total' => [
                    'type' => 'Int',
                    'metadata' => [
                        'label' => trans('Item Count'),
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
            ],

            'metadata' => [
                'type' => true,
                'label' => trans('Search'),
            ],
        ];
    }

    public static function link()
    {
        return RouteHelper::getSearchRoute();
    }
}
