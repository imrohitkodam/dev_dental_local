<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Factory;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\StreamWrapper;

class LoadTemplate
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        if ($this->config->get('app.isAdmin') || !$this->config->get('theme.active')) {
            return;
        }

        $view = $event->getArgument('subject');

        // loader callback for template event
        $loader = function ($path) use ($view) {
            // Clone view to avoid mutations on the original view
            $copy = clone $view;
            $copy->set('_output', null);
            $copy->set('context', basename($copy->get('_basePath')) . ".{$copy->getName()}");
            $tpl = substr(basename($path, '.php'), strlen($copy->getLayout()) + 1) ?: null;

            Factory::getApplication()->triggerEvent('onLoadTemplate', [$copy, $tpl]);

            return $copy->get('_output');
        };

        // register the stream wrapper
        if (!in_array('views', stream_get_wrappers())) {
            stream_wrapper_register('views', StreamWrapper::class);
        }

        // add loader using a stream reference
        // check if path is available (StackIdeas com_payplan)
        if ($path = $view->get('_path')) {
            array_unshift($path['template'], 'views://' . StreamWrapper::setObject($loader));
            $view->set('_path', $path);
        }
    }
}
