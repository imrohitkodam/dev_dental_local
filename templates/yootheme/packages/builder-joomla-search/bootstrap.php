<?php

namespace YOOtheme\Builder\Joomla\Search;

use Joomla\CMS\Component\ComponentHelper;

if (!ComponentHelper::isEnabled('com_search')) {
    return [];
}

return [
    'events' => [
        'source.init' => [Listener\LoadSourceTypes::class => 'handle'],
        'builder.template' => [Listener\MatchTemplate::class => '@handle'],
        'builder.template.load' => [Listener\LoadTemplateUrl::class => '@handle'],
    ],

    'actions' => [
        'onContentPrepare' => [Listener\LoadSearchArticle::class => 'handle'],
    ],
];
