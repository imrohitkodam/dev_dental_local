<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\User\User;
use function YOOtheme\app;
use function YOOtheme\trans;

class SiteQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'site' => [
                    'type' => 'Site',
                    'metadata' => [
                        'label' => trans('Site'),
                        'group' => trans('Global'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve()
    {
        /** @var User $user */
        $user = app(User::class);

        /** @var CMSApplication $joomla */
        $joomla = app(CMSApplication::class);
        $params = $joomla instanceof SiteApplication ? $joomla->getParams() : [];

        return [
            'title' => $joomla->get('sitename'),
            'page_title' => $params['page_heading'] ?? '',
            'user' => $user,
            'is_guest' => $user->guest,
        ];
    }
}
