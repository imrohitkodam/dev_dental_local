<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use YOOtheme\Config;
use YOOtheme\Metadata;

class LoadCustomizerData
{
    public Config $config;
    public Metadata $metadata;
    public CMSApplication $joomla;

    public function __construct(Config $config, CMSApplication $joomla, Metadata $metadata)
    {
        $this->config = $config;
        $this->joomla = $joomla;
        $this->metadata = $metadata;
    }

    public function handle(): void
    {
        if (
            $this->joomla->get('themeFile') !== 'offline.php' &&
            ($data = $this->config->get('customizer'))
        ) {
            $this->metadata->set(
                'script:customizer-data',
                sprintf(
                    'window.yootheme ||= {}; var $customizer = yootheme.customizer = JSON.parse(atob("%s"));',
                    base64_encode(json_encode($data)),
                ),
                ['id' => 'customizer-data'],
            );
        }
    }
}
