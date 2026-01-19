<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use function YOOtheme\trans;

class ContactQueryType
{
    protected static $view = ['com_contact.contact'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'contact' => [
                    'type' => 'Contact',

                    'metadata' => [
                        'group' => trans('Page'),
                        'label' => trans('Contact'),
                        'view' => static::$view,
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
            return $root['item'];
        }
    }
}
