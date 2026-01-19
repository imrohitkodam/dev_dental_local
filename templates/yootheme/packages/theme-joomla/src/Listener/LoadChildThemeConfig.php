<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Factory;
use YOOtheme\Config;

class LoadChildThemeConfig
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        if (
            !$this->config->get('app.isAdmin') &&
            ($childDir = $this->config->get('theme.childDir')) &&
            ($themeFile = $this->getThemeFile()) &&
            file_exists($file = "{$childDir}/{$themeFile}")
        ) {
            Factory::getApplication()->set('theme', basename(dirname($file)));
        }
    }

    /**
     * @see SiteApplication::render
     */
    protected function getThemeFile(): ?string
    {
        $joomla = Factory::getApplication();

        if ($joomla->getDocument()->getType() === 'feed') {
            return null;
        }

        $file = $joomla->input->get('tmpl', 'index');

        if ($file === 'offline' && !$joomla->get('offline')) {
            return 'index.php';
        }

        if ($joomla->get('offline') && !Factory::getUser()->authorise('core.login.offline')) {
            return 'offline.php';
        }

        return "{$file}.php";
    }
}
