<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Theme\Joomla\MenuConfig;

class LoadMenuData
{
    public User $user;
    public Config $config;
    public MenuConfig $menu;

    public function __construct(Config $config, MenuConfig $menu, User $user)
    {
        $this->menu = $menu;
        $this->user = $user;
        $this->config = $config;
    }

    public function handle()
    {
        $this->config->add('customizer', ['menu' => $this->menu->getArrayCopy()]);

        if ($this->user->authorise('core.manage', 'com_menus')) {
            $this->config->addFile(
                'customizer',
                Path::get('../../config/customizer.json', __DIR__),
            );
        }
    }
}
