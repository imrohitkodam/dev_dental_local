<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;
use YOOtheme\Metadata;

class LoadFontAwesome
{
    public Config $config;
    public Metadata $metadata;

    public function __construct(Config $config, Metadata $metadata)
    {
        $this->config = $config;
        $this->metadata = $metadata;
    }

    public function handle(): void
    {
        if (version_compare(JVERSION, '4.0', '<') || !$this->config->get('~theme.fontawesome')) {
            return;
        }

        $this->metadata->set('style:fontawesome', [
            'href' => '~/media/system/css/joomla-fontawesome.min.css',
            'defer' => true,
        ]);
    }
}
