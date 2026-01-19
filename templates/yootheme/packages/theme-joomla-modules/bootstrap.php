<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Document\Document;
use YOOtheme\View;

return [
    'routes' => [
        ['get', '/module', ModuleController::class . '@getModule'],
        ['post', '/module', ModuleController::class . '@saveModule'],
        ['get', '/modules', ModuleController::class . '@getModules'],
        ['get', '/positions', ModuleController::class . '@getPositions'],
    ],

    'events' => [
        'theme.init' => [Listener\LoadModuleRenderer::class => '@handle'],
        'customizer.init' => [Listener\LoadModuleData::class => '@handle'],
    ],

    'actions' => [
        'onContentPrepareForm' => [Listener\LoadModuleForm::class => 'handle'],
        'onAfterCleanModuleList' => [Listener\LoadModules::class => ['@handle', -10]],
    ],

    'extend' => [
        View::class => function (View $view, $app) {
            $view->addFunction('countModules', $app->wrap(Document::class . '@countModules'));
        },
    ],
];
