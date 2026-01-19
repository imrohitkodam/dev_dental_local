<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class AddPageLayout
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        [$view] = $event->getArguments();

        $layout = $view->getLayout();
        $context = $view->get('context');

        if (in_array($context, ['com_content.category', 'com_content.featured', 'com_tags.tag'])) {
            $this->config->set('~theme.page_layout', 'blog');
        }

        if ($context === 'com_content.article' && $layout === 'default') {
            $item = $view->get('item');

            if ($this->config->get('~theme.page_category') != $item->catid) {
                $this->config->set('~theme.page_layout', 'post');
            }
        }
    }
}
