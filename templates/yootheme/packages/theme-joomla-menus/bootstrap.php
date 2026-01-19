<?php

namespace YOOtheme\Theme\Joomla;

return [
    'routes' => [['get', '/items', [MenuController::class, 'getItems']]],

    'events' => [
        'customizer.init' => [Listener\LoadMenuData::class => '@handle'],
    ],

    'actions' => [
        'onAfterCleanModuleList' => [
            Listener\LoadMenuModules::class => '@handle',
            Listener\LoadSplitNavbar::class => ['@handle', -20],
        ],
    ],
];
