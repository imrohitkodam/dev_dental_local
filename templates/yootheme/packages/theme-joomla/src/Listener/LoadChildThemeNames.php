<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class LoadChildThemeNames
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        $this->config->set(
            'theme.child_themes',
            array_merge(
                ['None' => ''],
                $this->getChildThemes($this->config->get('theme.rootDir', '')),
            ),
        );
    }

    protected function getChildThemes(string $root): array
    {
        $dir = dirname($root);
        $name = basename($root);
        $themes = [];

        foreach (glob("{$dir}/{$name}_*") as $child) {
            $child = str_replace("{$name}_", '', basename($child));
            $themes[ucfirst($child)] = $child;
        }

        return $themes;
    }
}
