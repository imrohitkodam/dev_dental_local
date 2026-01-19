<?php

namespace YOOtheme\Builder\Joomla\RegularLabs\Listener;

return [
    'events' => [
        'source.com_fields.field' => [
            ArticlesField::class => '@config',
        ],
    ],
];
