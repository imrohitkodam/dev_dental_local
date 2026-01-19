<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Router\Route;
use YOOtheme\Config;

class AddSiteUrl
{
    public Config $config;
    public ?SiteApplication $joomla;

    public function __construct(Config $config, ?SiteApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        if (!$this->isThemeActive()) {
            return;
        }

        $itemId = ($item = $this->joomla->getMenu()->getDefault()) ? $item->id : 0;
        $siteUrl = Route::_("index.php?Itemid={$itemId}", false, 0, true);

        $this->config->set('~theme.site_url', $siteUrl);
    }

    protected function isThemeActive(): bool
    {
        return isset($this->joomla) &&
            $this->joomla->input->getCmd('tmpl') !== 'component' &&
            $this->joomla->input->getCmd('option') !== 'com_ajax' &&
            $this->config->get('theme.active');
    }
}
