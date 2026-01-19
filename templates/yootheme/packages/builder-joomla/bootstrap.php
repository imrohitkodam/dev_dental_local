<?php

namespace YOOtheme\Builder\Joomla;

use Joomla\CMS\HTML\Helpers\Content;
use YOOtheme\Builder;
use YOOtheme\View;

return [
    'routes' => [
        ['post', '/page', PageController::class . '@savePage'],
        ['get', '/builder/pages', PageController::class . '@getPages'],
        ['post', '/builder/image', [BuilderController::class, 'loadImage']],
    ],

    'actions' => [
        'onAfterRoute' => [
            Listener\LoadSessionUser::class => '@handle',
        ],

        'onLoadTemplate' => [
            Listener\LoadSessionUser::class => ['@reset', 10],
            Listener\RenderBuilderButton::class => ['@handle', 10],
        ],

        'onContentPrepare' => [
            Listener\RenderBuilderPage::class => '@handle',
        ],

        'onSchemaBeforeCompileHead' => [
            Listener\LoadSessionUser::class => [['@handle', 10], ['@reset', -10]],
        ],
    ],

    'extend' => [
        View::class => function (View $view) {
            $view->addLoader(function ($name, $parameters, callable $next) {
                $content = $next($name, $parameters);

                return empty($parameters['prefix']) || $parameters['prefix'] !== 'page'
                    ? Content::prepare($content)
                    : $content;
            }, '*/builder/elements/layout/templates/template.php');
        },

        Builder::class => function (Builder $builder, $app) {
            $builder->addTypePath(__DIR__ . '/elements/*/element.json');

            if ($childDir = $app->config->get('theme.childDir')) {
                $builder->addTypePath("{$childDir}/builder/*/element.json");
            }
        },
    ],

    'services' => [
        Listener\LoadSessionUser::class => '',
        Listener\RenderBuilderPage::class => '',
    ],
];
