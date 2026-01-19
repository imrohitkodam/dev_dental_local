<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class LoadChildThemeTemplate
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        $childDir = $this->config->get('theme.childDir');

        if (!$childDir || $this->config->get('app.isAdmin')) {
            return;
        }

        $view = $event->getArgument('subject');
        $paths = $view->get('_path');

        if ($path = $paths['template'][0] ?? false) {
            $theme = $this->config->get('theme.template');

            if (str_contains($path, DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR)) {
                array_unshift(
                    $paths['template'],
                    preg_replace("/({$theme}(?!.*{$theme}.*))/", basename($childDir), $path),
                );
            }
        }

        $view->set('_path', $paths);
    }
}
