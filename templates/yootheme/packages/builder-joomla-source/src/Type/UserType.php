<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Access\Access;
use Joomla\Component\Users\Administrator\Helper\UsersHelper;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use function YOOtheme\trans;

class UserType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Name'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'username' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Username'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'email' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Email'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'registerDate' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Registered Date'),
                        'filters' => ['date'],
                    ],
                ],

                'lastvisitDate' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Last Visit Date'),
                        'filters' => ['date'],
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

                'userGroupString' => [
                    'type' => 'String',
                    'args' => [
                        'separator' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('User Groups'),
                        'arguments' => [
                            'separator' => [
                                'label' => trans('Separator'),
                                'description' => trans('Set the separator between user groups.'),
                                'default' => ', ',
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::userGroupString',
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
                'label' => trans('User'),
            ],
        ];
    }

    public static function link($user)
    {
        return UserHelper::getContactLink($user->id);
    }

    public static function userGroupString($user, $args)
    {
        $result = [];
        $groups = Access::getGroupsByUser($user->id);
        foreach (UsersHelper::getGroups() as $group) {
            if (in_array($group->value, $groups)) {
                $result[] = $group->title;
            }
        }
        return implode($args['separator'] ?? ', ', $result);
    }
}
