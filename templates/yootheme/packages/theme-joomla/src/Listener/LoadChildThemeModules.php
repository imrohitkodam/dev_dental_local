<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class LoadChildThemeModules
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        $childDir = $this->config->get('theme.childDir');

        if (!$childDir || $this->config->get('app.isAdmin')) {
            return;
        }

        $modules = $event->getArgument('modules');

        foreach ($modules as $module) {
            $params = json_decode($module->params ?? '{}');
            $layout = is_string($params->layout ?? null)
                ? str_replace('_:', '', $params->layout)
                : 'default';

            if (file_exists("{$childDir}/html/{$module->module}/{$layout}.php")) {
                $params->layout = basename($childDir) . ":{$layout}";
                $module->params = json_encode($params);
            }
        }
    }
}
