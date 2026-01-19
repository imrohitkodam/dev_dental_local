<?php

namespace YOOtheme\Theme\Joomla;

return [
    'routes' => [['get', '/theme/editor', [EditorController::class, 'render']]],
    'events' => ['customizer.init' => [Listener\LoadEditorData::class => '@handle']],
];
