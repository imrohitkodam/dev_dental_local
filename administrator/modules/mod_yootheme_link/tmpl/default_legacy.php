<?php

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Menu\MenuHelper;

defined('_JEXEC') or die();

if (in_array($module->position, ['icon', 'cpanel'])) {
    JLoader::register(ModQuickIconHelper::class, __DIR__ . '/helper.php');

    $buttons = [
        [
            'image' => ' yo-quicklink-cpanel-j3',
            'text' => 'YOOtheme',
            'link' => "index.php?option=com_ajax&templateStyle={$templ->id}&p=customizer&format=html",
            'group' => Text::_('MOD_YOOTHEME_LINK_TEMPLATES'),
            'access' => ['core.edit', 'com_templates'],
        ],
    ];

    require ModuleHelper::getLayoutPath('mod_quickicon');
}

if ($module->position === 'menu') {
    MenuHelper::addPreset('yootheme', 'YOOtheme', __DIR__ . '/../presets/yootheme.xml');

    $params = new Registry(['preset' => 'yootheme']);
    include JPATH_ADMINISTRATOR . '/modules/mod_menu/mod_menu.php';
}
