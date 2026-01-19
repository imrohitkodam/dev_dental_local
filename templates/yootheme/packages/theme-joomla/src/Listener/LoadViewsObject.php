<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Component\ComponentHelper;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\ViewsObject;

class LoadViewsObject
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        // register views cache array
        if (version_compare(JVERSION, '4.0', '<') && $this->config->get('app.isSite')) {
            ViewsObject::register();
        }

        // Joomla 4 does not distribute com_search
        if (!ComponentHelper::isEnabled('com_search')) {
            $this->config->set('~theme.search_module', 'mod_finder');
        }
    }
}
