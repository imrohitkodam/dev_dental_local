<?php

namespace YOOtheme\Builder\Joomla\Fields;

use YOOtheme\Builder\BuilderConfig;

return [
    'events' => [
        'source.init' => [
            Listener\LoadSourceTypes::class => ['handle', -10],
        ],

        BuilderConfig::class => [
            Listener\LoadBuilderConfig::class => ['handle', -10],
        ],
    ],
];
