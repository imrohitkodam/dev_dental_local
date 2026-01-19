<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Config;
use YOOtheme\Theme\Joomla\ApiKey;
use YOOtheme\Url;

class LoadCustomizerContext
{
    public Config $config;
    public ApiKey $apiKey;

    public function __construct(Config $config, ApiKey $apiKey)
    {
        $this->config = $config;
        $this->apiKey = $apiKey;
    }

    public function handle($event): void
    {
        $context = $event->getArgument('context');
        $data = $event->getArgument('data');

        if ($context !== 'com_templates.style') {
            return;
        }

        $this->config->add('customizer', [
            'context' => $context,
            'apikey' => $this->apiKey->get(),
            'url' => Url::route('customizer', [
                'templateStyle' => $data->id,
                'format' => 'html',
            ]),
        ]);
    }
}
