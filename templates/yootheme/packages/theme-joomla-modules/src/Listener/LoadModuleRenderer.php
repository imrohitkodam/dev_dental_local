<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class LoadModuleRenderer
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        if ($this->config->get('app.isSite')) {
            $renderer = version_compare(JVERSION, '3.8', '>=')
                ? 'Joomla\CMS\Document\Renderer\Html\ModulesRenderer'
                : 'JDocumentRendererHtmlModules';

            class_alias('YOOtheme\Theme\Joomla\ModulesRenderer', $renderer);
        }
    }
}
