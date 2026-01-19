<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class LoadSplitNavbar
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        $modules = $event->getArgument('modules');

        if ($this->config->get('app.isAdmin') || !$this->config->get('theme.active')) {
            return;
        }

        if (
            in_array($this->config->get('~theme.header.layout'), [
                'stacked-center-split-a',
                'stacked-center-split-b',
            ])
        ) {
            foreach ($modules as $module) {
                if (
                    $module->module != 'mod_menu' ||
                    $module->position != 'navbar' ||
                    !in_array($this->config->get("~theme.modules.{$module->id}.menu_type"), [
                        '',
                        'nav',
                    ])
                ) {
                    continue;
                }

                $clone = clone $module;
                $clone->id = "{$module->id}-split";
                $clone->position = 'navbar-split';

                array_splice($modules, array_search($module, $modules) + 1, 0, [$clone]);
            }
        }

        $event->setArgument(0, $modules);
        $event->setArgument('modules', $modules);
    }
}
