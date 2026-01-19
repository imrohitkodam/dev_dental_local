<?php

namespace YOOtheme;

use Joomla\CMS\Application\CMSApplication as CMSApp;
use Joomla\CMS\Application\SiteApplication as SiteApp;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Router\Router as JoomlaRouter;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\DI\Container;
use Joomla\Database\DatabaseDriver;
use Joomla\Input\Input;
use YOOtheme\Joomla\ActionLoader;
use YOOtheme\Joomla\Media;
use YOOtheme\Joomla\Platform;
use YOOtheme\Joomla\Router;

Url::setBase(Uri::root(true));
Path::setAlias('~', strtr(JPATH_ROOT, '\\', '/'));

return [
    'config' => function () {
        $joomla = Factory::getApplication();
        $normalize = fn($path) => strtr($path, '\\', '/');

        return [
            'app' => [
                'platform' => 'joomla',
                'version' => JVERSION,
                'secret' => (string) $joomla->get('secret'),
                'debug' => (bool) $joomla->get('debug'),
                'rootDir' => $normalize(JPATH_ROOT),
                'tempDir' => $normalize($joomla->get('tmp_path', JPATH_ROOT . '/tmp')),
                'adminDir' => $normalize(JPATH_ADMINISTRATOR),
                'cacheDir' => $normalize($joomla->get('cache_path', JPATH_ROOT . '/cache')),
                'uploadDir' => fn() => Media::getRoot(),
                'isSite' => $joomla->isClient('site'),
                'isAdmin' => $joomla->isClient('administrator'),
            ],

            'req' => [
                'baseUrl' => Uri::base(true),
                'rootUrl' => Uri::root(true),
                'siteUrl' => rtrim(Uri::root(), '/'),
            ],

            'locale' => [
                'rtl' => fn() => $joomla->getLanguage()->isRtl(),
                'code' => fn() => strtr($joomla->getLanguage()->getTag(), '-', '_'),
            ],

            'session' => [
                'token' => fn() => Session::getFormToken(),
            ],
        ];
    },

    'events' => [
        'url.route' => [Router::class => 'generate'],
        'app.error' => [Platform::class => ['handleError', -50]],
    ],

    'actions' => [
        'onAfterRoute' => [Platform::class => ['handleRoute', -50]],
        'onBeforeCompileHead' => [Platform::class => ['registerAssets', -50]],
    ],

    'loaders' => [
        'actions' => ActionLoader::class,
    ],

    'aliases' => [
        Document::class => HtmlDocument::class,
    ],

    'services' => array_merge(
        [
            ActionLoader::class => '',

            CsrfMiddleware::class => fn(Config $config) => new CsrfMiddleware(
                $config('session.token'),
            ),

            HttpClientInterface::class => Joomla\HttpClient::class,

            Storage::class => Joomla\Storage::class,

            DatabaseDriver::class => [
                'factory' => fn(Container $container) => $container->get(DatabaseDriver::class),
            ],

            SiteApp::class => [
                'factory' => fn(CMSApp $joomla) => $joomla->isClient('site') ? $joomla : null,
            ],

            CMSApp::class => ['factory' => fn() => Factory::getApplication()],

            Container::class => ['factory' => fn() => Factory::getContainer()],

            Document::class => [
                'shared' => false,
                'factory' => fn(CMSApp $joomla) => $joomla->getDocument(),
            ],

            Input::class => ['factory' => fn(CMSApp $joomla) => $joomla->input],

            Language::class => ['factory' => fn(CMSApp $joomla) => $joomla->getLanguage()],

            Session::class => ['factory' => fn(CMSApp $joomla) => $joomla->getSession()],

            SiteRouter::class => fn(Container $container) => $container->get(SiteRouter::class),

            User::class => [
                'shared' => false,
                'factory' => fn(CMSApp $joomla) => $joomla->getIdentity(),
            ],
        ],

        version_compare(JVERSION, '4.0', '<')
            ? [
                DatabaseDriver::class => [
                    'factory' => function () {
                        // Force autoloading (Type hints do not trigger autoloading)
                        class_exists(DatabaseDriver::class);

                        return Factory::getDbo();
                    },
                ],

                SiteRouter::class => fn() => JoomlaRouter::getInstance('site'),

                User::class => ['shared' => false, 'factory' => fn() => Factory::getUser()],
            ]
            : [],
    ),
];
