<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\HTML\Helpers\Menu;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\ConfigObject;

/**
 * @property array $menus
 * @property array $items
 * @property array $positions
 * @property bool  $canEdit
 * @property bool  $canCreate
 * @property bool  $canDelete
 */
class MenuConfig extends ConfigObject
{
    /**
     * Constructor.
     */
    public function __construct(Config $config, User $user)
    {
        parent::__construct([
            'menus' => $this->getMenus(),
            'items' => $this->getItems(),
            'positions' => $config->get('theme.menus'),
            'canEdit' => $user->authorise('core.edit', 'com_menus'),
            'canCreate' => $user->authorise('core.create', 'com_menus'),
            'canDelete' => $user->authorise('core.edit.state', 'com_menus'),
        ]);
    }

    protected function getMenus()
    {
        return array_map(
            fn($menu) => [
                'id' => $menu->value,
                'name' => $menu->text,
            ],
            Menu::menus(),
        );
    }

    protected function getItems()
    {
        return array_values(
            array_map(
                fn($item) => [
                    'id' => (string) $item->id,
                    'title' => $item->title,
                    'level' => $item->level - 1,
                    'menu' => $item->menutype,
                    'link' => $item->link,
                    'home' => $item->home,
                    'parent' => (string) $item->parent_id,
                    'type' => $item->type == 'separator' ? 'heading' : $item->type,
                ],
                AbstractMenu::getInstance('site')->getMenu(),
            ),
        );
    }
}
