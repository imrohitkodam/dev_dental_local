<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\View\HtmlView;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\ThemeLoader;
use function YOOtheme\app;

class LoadNotFound
{
    public Config $config;
    public CMSApplication $joomla;

    public function __construct(Config $config, CMSApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle($event): void
    {
        [$result] = $event->getArguments();

        if (!$this->config->get('theme.template')) {
            app()->call([ThemeLoader::class, 'initTheme']);
        }

        $view = new HtmlView(['name' => '404', 'base_path' => '', 'template_path' => '']);

        $this->joomla->triggerEvent('onLoadTemplate', [$view, null]);

        if ($this->config->get('app.isCustomizer')) {
            $result['customizer'] = sprintf(
                '<script id="customizer-data">window.yootheme = window.yootheme || {}; var $customizer = yootheme.customizer = JSON.parse(atob("%s"));</script>',
                base64_encode(json_encode($this->config->get('customizer'))),
            );
        }

        if (!empty($view->get('_output'))) {
            $result['404'] = $view->get('_output');
        }
    }
}
