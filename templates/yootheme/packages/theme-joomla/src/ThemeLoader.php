<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use YOOtheme\Application;
use YOOtheme\Arr;
use YOOtheme\Config;
use YOOtheme\Container;
use YOOtheme\Event;
use YOOtheme\Path;
use YOOtheme\Theme\Updater;

class ThemeLoader
{
    protected static $configs = [];

    /**
     * Load theme configurations.
     */
    public static function load(Container $container, array $configs)
    {
        static::$configs = array_merge(static::$configs, $configs);
    }

    /**
     * Initialize current theme.
     */
    public static function initTheme(Application $app, Config $config)
    {
        $template = static::getTemplate($app);

        // is template active?
        if (!empty($template->params['yootheme'])) {
            static::loadConfiguration($app, $config, $template);
            Event::emit('theme.init');
        }
    }

    protected static function loadConfiguration(Application $app, Config $config, object $template)
    {
        // get theme config
        $themeConfig = $template->params->get('config', '');
        $themeConfig = json_decode($themeConfig, true) ?: [];

        // load child theme config
        if (!empty($themeConfig['child_theme'])) {
            $app->load(
                Path::get(
                    "~/templates/{$template->template}_{$themeConfig['child_theme']}/config.php",
                ),
            );
        }

        // add configurations
        $config->add('theme', [
            'id' => $template->id,
            'active' => true,
            'default' => !empty($template->home),
            'template' => $template->template,
        ]);

        foreach (static::$configs as $conf) {
            if ($conf instanceof \Closure) {
                $conf = $conf($config, $app);
            }

            $config->add('theme', (array) $conf);
        }

        // handle empty config
        if (empty($themeConfig)) {
            $themeConfig['version'] = $config('theme.version');
        }

        // merge defaults with configuration
        $config->set(
            '~theme',
            Arr::merge(
                $config('theme.defaults', []),
                static::updateConfig($app, $template, $themeConfig),
            ),
        );
    }

    /**
     * Gets the current template.
     *
     * @return object|null
     */
    protected static function getTemplate(Application $app)
    {
        /** @var CMSApplication $joomla */
        $joomla = $app(CMSApplication::class);
        $template = $joomla->getTemplate(true);

        // get site template
        if ($joomla->isClient('administrator')) {
            $view = $joomla->input->getCmd('view') === 'style';
            $option = $joomla->input->getCmd('option') === 'com_templates';
            $style = $joomla->input->getInt($view && $option ? 'id' : 'templateStyle');

            /** @var DatabaseDriver $db */
            $db = $app(DatabaseDriver::class);
            $query =
                'SELECT * FROM #__template_styles WHERE ' .
                ($style ? "id = {$style}" : "client_id = 0 AND home = '1'");

            if ($template = $db->setQuery($query)->loadObject()) {
                $template->params = new Registry($template->params);
            }
        }

        return $template;
    }

    protected static function updateConfig(Application $app, object $template, array $themeConfig)
    {
        /** @var Updater $updater */
        $updater = $app(Updater::class);
        $version = $themeConfig['version'] ?? null;
        $themeConfig = $updater->update($themeConfig, ['app' => $app, 'config' => $themeConfig]);

        if (empty($version) || $version !== $themeConfig['version']) {
            $style = (object) [
                'id' => $template->id,
                'params' => json_encode(
                    [
                        'config' => json_encode(
                            $themeConfig,
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                        ),
                    ] + $template->params->toArray(),
                ),
            ];

            /** @var DatabaseDriver $db */
            $db = $app(DatabaseDriver::class);
            $db->updateObject('#__template_styles', $style, 'id');
        }

        return $themeConfig;
    }
}
