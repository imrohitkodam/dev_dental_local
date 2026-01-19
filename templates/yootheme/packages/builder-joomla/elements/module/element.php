<?php

namespace YOOtheme;

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Document\Renderer\Html\ModuleRenderer;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

return [
    'transforms' => [
        'render' => function ($node) {
            if (empty($node->props['module'])) {
                return false;
            }

            $module = ModuleHelper::getModuleById((string) $node->props['module']);

            $config = app(Config::class);
            $index = "~theme.modules.{$module->id}";
            $props = $config->get($index, ['class' => []]);

            $node->attrs['class'] = array_merge($node->attrs['class'], $props['class']);
            $node->props = Arr::merge($props, $node->props);

            // override module config with props
            $config->set($index, $node->props);

            // make sure module gets re-rendered in Joomla 4+
            unset($module->contentRendered);

            /** @var HtmlDocument $document */
            $document = Factory::getApplication()->getDocument();
            /** @var ModuleRenderer $renderer */
            $renderer = $document->loadRenderer('module');

            // render module content
            $node->module = (object) [
                'title' => $module->title,
                'content' => $renderer->render($module),
            ];

            // reset module config
            $config->set($index, $props);

            // return false, if no module content was found
            if (empty($node->module->content)) {
                return false;
            }
        },
    ],
];
