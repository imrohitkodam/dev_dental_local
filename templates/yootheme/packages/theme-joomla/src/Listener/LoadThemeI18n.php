<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Language\Text;
use YOOtheme\Config;

class LoadThemeI18n
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        $this->config->add('theme.data.i18n', [
            'close' => ['label' => Text::_('TPL_YOOTHEME_CLOSE')],
            'totop' => ['label' => Text::_('TPL_YOOTHEME_BACK_TO_TOP')],
            'marker' => ['label' => Text::_('TPL_YOOTHEME_OPEN')],
            'navbarToggleIcon' => ['label' => Text::_('TPL_YOOTHEME_OPEN_MENU')],
            'paginationPrevious' => ['label' => Text::_('TPL_YOOTHEME_PREVIOUS_PAGE')],
            'paginationNext' => ['label' => Text::_('TPL_YOOTHEME_NEXT_PAGE')],
            'searchIcon' => [
                'toggle' => Text::_('TPL_YOOTHEME_OPEN_SEARCH'),
                'submit' => Text::_('TPL_YOOTHEME_SUBMIT_SEARCH'),
            ],
            'slider' => [
                'next' => Text::_('TPL_YOOTHEME_NEXT_SLIDE'),
                'previous' => Text::_('TPL_YOOTHEME_PREVIOUS_SLIDE'),
                'slideX' => Text::_('TPL_YOOTHEME_SLIDE_%S'),
                'slideLabel' => Text::_('TPL_YOOTHEME_%S_OF_%S'),
            ],
            'slideshow' => [
                'next' => Text::_('TPL_YOOTHEME_NEXT_SLIDE'),
                'previous' => Text::_('TPL_YOOTHEME_PREVIOUS_SLIDE'),
                'slideX' => Text::_('TPL_YOOTHEME_SLIDE_%S'),
                'slideLabel' => Text::_('TPL_YOOTHEME_%S_OF_%S'),
            ],
            'lightboxPanel' => [
                'next' => Text::_('TPL_YOOTHEME_NEXT_SLIDE'),
                'previous' => Text::_('TPL_YOOTHEME_PREVIOUS_SLIDE'),
                'slideLabel' => Text::_('TPL_YOOTHEME_%S_OF_%S'),
                'close' => Text::_('TPL_YOOTHEME_CLOSE'),
            ],
        ]);
    }
}
