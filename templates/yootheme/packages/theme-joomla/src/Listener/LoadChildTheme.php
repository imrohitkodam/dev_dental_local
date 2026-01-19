<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\File;

class LoadChildTheme
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        if (empty(($child = $this->config->get('~theme.child_theme')))) {
            return;
        }

        if (!file_exists($childDir = "{$this->config->get('theme.rootDir')}_{$child}")) {
            return;
        }

        // add childDir to config
        $this->config->set('theme.childDir', $childDir);

        // add ~theme alias resolver
        Event::on(
            'path ~theme',
            fn($path, $file) => $file && File::find($childDir . $file) ? $childDir . $file : $path,
        );
    }
}
