<?php

namespace YOOtheme\Theme\Joomla;

use YOOtheme\Theme\Updater;
use YOOtheme\View;

return [
    'actions' => [
        'onContentBeforeSave' => [Listener\SaveBuilderData::class => '@handle'],
        'onContentPrepareData' => [Listener\LoadArticleForm::class => '@handle'],
    ],

    'extend' => [
        View::class => function (View $view) {
            $view->addLoader([ViewLoader::class, 'loadArticle'], '~theme/templates/article*');
        },

        Updater::class => function (Updater $updater) {
            $updater->add(__DIR__ . '/updates.php');
        },
    ],
];
