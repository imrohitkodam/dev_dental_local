<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;
use YOOtheme\Theme\Joomla\FinderConfig;

class LoadFinderData
{
    public Config $config;
    public FinderConfig $finder;

    public function __construct(Config $config, FinderConfig $finder)
    {
        $this->config = $config;
        $this->finder = $finder;
    }

    public function handle(): void
    {
        $this->config->add('customizer', ['media' => $this->finder->getArrayCopy()]);
    }
}
