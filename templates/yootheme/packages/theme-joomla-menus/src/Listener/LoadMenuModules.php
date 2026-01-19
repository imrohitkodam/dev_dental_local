<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class LoadMenuModules
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

        // create menu modules when assigned in theme settings
        foreach ($this->config->get('~theme.menu.positions', []) as $position => $menu) {
            if (empty($menu['menu'])) {
                continue;
            }

            array_unshift(
                $modules,
                (object) [
                    'id' => "menu-{$position}",
                    'name' => 'menu',
                    'module' => 'mod_menu',
                    'title' => '',
                    'showtitle' => 0,
                    'position' => $position,
                    'params' => json_encode([
                        'menutype' => $menu['menu'],
                        'showAllChildren' => true,
                        'yoo_config' => json_encode(
                            array_combine(
                                array_map(fn($key) => "menu_{$key}", array_keys($menu)),
                                $menu,
                            ),
                        ),
                    ]),
                ],
            );
        }

        $event->setArgument(0, $modules);
        $event->setArgument('modules', $modules);
    }
}
