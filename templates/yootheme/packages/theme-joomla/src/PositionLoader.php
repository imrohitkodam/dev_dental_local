<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Factory;
use YOOtheme\Application;
use YOOtheme\Config;
use YOOtheme\Joomla\Platform;

class PositionLoader
{
    public Application $app;
    public Config $config;

    public function __construct(Application $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Add assets for Joomla progressive caching.
     */
    public function __invoke(string $name, array $parameters, callable $next)
    {
        $result = $next($name, $parameters);

        // Make assets cacheable (e.g. maps.min.js)
        if ((int) Factory::getApplication()->get('caching', 0) === 2) {
            $this->app->call([Platform::class, 'registerAssets']);
        }

        return $result;
    }
}
