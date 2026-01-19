<?php

namespace YOOtheme\Builder\Joomla\Search\Listener;

use Joomla\CMS\Router\SiteRouter;

class LoadTemplateUrl
{
    public SiteRouter $router;

    public function __construct(SiteRouter $router)
    {
        $this->router = $router;
    }

    public function handle(array $template): array
    {
        if (($template['type'] ?? '') === 'com_search.search') {
            $template['url'] = (string) $this->router->build(
                'index.php?option=com_search&view=search',
            );
        }

        return $template;
    }
}
