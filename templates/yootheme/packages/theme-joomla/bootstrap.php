<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use YOOtheme\Config;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use YOOtheme\Theme\Updater;
use YOOtheme\Theme\ViewHelper as BaseViewHelper;
use YOOtheme\View;

return [
    'theme' => function (Config $config) {
        $config->set('theme.styles.vars.@internal-joomla-version', (string) Version::MAJOR_VERSION);

        return $config->loadFile(__DIR__ . '/config/theme.json');
    },

    'routes' => [
        ['get', '/customizer', [CustomizerController::class, 'index'], ['customizer' => true]],
        ['post', '/customizer', [CustomizerController::class, 'save']],
    ],

    'events' => [
        'app.request' => [Listener\CheckUserPermission::class => '@handle'],
        'url.resolve' => [Listener\AddCustomizeParameter::class => '@handle'],

        'theme.head' => [
            Listener\LoadThemeI18n::class => '@handle',
            Listener\LoadFontAwesome::class => '@handle',
        ],
        'theme.init' => [
            Listener\LoadViewsObject::class => ['@handle', 20],
            Listener\AddPageCategory::class => ['@handle', 10],
            Listener\LoadChildTheme::class => ['@handle', -10],
            Listener\LoadCustomizerSession::class => ['@handle', -20],
        ],

        'customizer.init' => [
            Listener\LoadCustomizer::class => ['@handle', 10],
            Listener\LoadCustomizerScript::class => ['@handle', 30],
            Listener\LoadChildThemeNames::class => ['@handle', 20],
        ],

        'config.save' => [
            Listener\AlterParamsColumnType::class => '@handle',
            Listener\SaveInstallerApiKey::class => '@handle',
        ],

        'styler.imports' => [Listener\LoadStylerImports::class => '@handle'],

        // Joomla 3.x only (see ViewsObject)
        'view.init' => [
            Listener\LoadTemplate::class => ['@handle', -10],
            Listener\LoadChildThemeTemplate::class => '@handle',
        ],
    ],

    'actions' => [
        'onAfterRoute' => [ThemeLoader::class => ['initTheme', 50]],

        'onBeforeDisplay' => [
            Listener\LoadTemplate::class => ['@handle', -10],
            Listener\LoadChildThemeTemplate::class => '@handle',
        ],

        'onLoadTemplate' => [
            Listener\AddPageLayout::class => '@handle',
            Listener\LoadAssets::class => ['@handle', -20],
            Listener\LoadConfigCache::class => ['@handle', -20],
        ],

        'onAfterDispatch' => [
            Listener\LoadConfigCache::class => '@load',
            Listener\LoadThemeHead::class => '@handle',
            Listener\LoadChildThemeConfig::class => '@handle',
        ],

        'onBeforeCompileHead' => [Listener\LoadCustomizerData::class => '@handle'],
        'onContentPrepareData' => [Listener\LoadCustomizerContext::class => '@handle'],

        'onAfterCleanModuleList' => [
            Listener\AddSiteUrl::class => '@handle',
            Listener\LoadChildThemeModules::class => ['@handle', -5],
        ],
    ],

    'extend' => [
        View::class => function (View $view, $app) {
            $view->addLoader([UrlLoader::class, 'resolveRelativeUrl']);
            $view->addLoader($app(PositionLoader::class), '~theme/templates/position');

            $view->addFunction('trans', [Text::class, '_']);
            $view->addFunction(
                'formatBytes',
                fn($bytes, $precision = 0) => HTMLHelper::_(
                    'number.bytes',
                    $bytes,
                    'auto',
                    $precision,
                ),
            );

            // cleanImageURL shim for Joomla 3.x
            if (version_compare(JVERSION, '4.0', '<')) {
                HTMLHelper::register('cleanImageURL', fn($url) => (object) ['url' => $url]);
            }
        },

        Updater::class => function (Updater $updater) {
            $updater->add(__DIR__ . '/updates.php');
        },
    ],

    'services' => [
        ThemeLoader::class => '',
        BaseViewHelper::class => ViewHelper::class,
        BaseSystemCheck::class => SystemCheck::class,
        Listener\AddCustomizeParameter::class => '',
    ],

    'loaders' => [
        'theme' => [ThemeLoader::class, 'load'],
    ],
];
