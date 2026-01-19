<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;
use YOOtheme\Http\Uri;

class AddCustomizeParameter
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($path, $parameters, $secure, callable $next)
    {
        /** @var Uri $uri */
        $uri = $next($path, $parameters, $secure);

        if (str_starts_with((string) $uri->getQueryParam('p'), 'theme/')) {
            $query = $uri->getQueryParams();
            $query['templateStyle'] = $this->config->get('theme.id');

            $uri = $uri->withQueryParams($query);
        }

        return $uri;
    }
}
