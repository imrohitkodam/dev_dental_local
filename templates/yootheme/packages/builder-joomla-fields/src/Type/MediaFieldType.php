<?php

namespace YOOtheme\Builder\Joomla\Fields\Type;

use function YOOtheme\trans;

class MediaFieldType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' =>
                [
                    'imagefile' => [
                        'type' => 'String',
                        'metadata' => [
                            'label' => '',
                        ],
                    ],
                ] +
                (version_compare(JVERSION, '4.0', '>')
                    ? [
                        'alt_text' => [
                            'type' => 'String',
                            'metadata' => [
                                'label' => trans('Alt'),
                            ],
                        ],
                    ]
                    : []),
        ];
    }
}
