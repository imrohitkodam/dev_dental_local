<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\User\User;
use function YOOtheme\app;
use function YOOtheme\trans;

class CustomMenuItemQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'customMenuItem' => [
                    'type' => 'MenuItem',

                    'args' => [
                        'menu' => [
                            'type' => 'String',
                        ],
                        'id' => [
                            'type' => 'String',
                        ],
                    ],

                    'metadata' => [
                        'label' => trans('Custom Menu Item'),
                        'group' => trans('Custom'),
                        'fields' => [
                            'menu' => [
                                'label' => trans('Menu'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['evaluate' => 'yootheme.customizer.menu.menusSelect()'],
                                ],
                            ],
                            'id' => [
                                'label' => trans('Menu Item'),
                                'description' => trans('Select menu item.'),
                                'type' => 'select',
                                'defaultIndex' => 0,
                                'options' => [
                                    ['evaluate' => 'yootheme.customizer.menu.itemsSelect(menu)'],
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
        $item = Factory::getApplication()
            ->getMenu('site')
            ->getItem($args['id'] ?? 0);

        return $item &&
            in_array($item->access, app(User::class)->getAuthorisedViewLevels()) &&
            (!Multilanguage::isEnabled() ||
                in_array($item->language, [Factory::getLanguage()->getTag(), '*']))
            ? $item
            : null;
    }
}
