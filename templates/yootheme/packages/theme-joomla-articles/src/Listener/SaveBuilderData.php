<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;

class SaveBuilderData
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        $context = $event->getArgument('context');
        $article = $event->getArgument('subject');

        if (!in_array($context, ['com_content.form', 'com_content.article'], true)) {
            return;
        }

        $articletext = $this->config->get('req.body.jform.articletext', '');

        // use "jform.articletext" from request to keep builder data, when JText filters are active
        if (preg_match('/<!--\s{.*}\s-->\s*$/', $articletext, $matches)) {
            $article->fulltext = $matches[0];
        }
    }
}
