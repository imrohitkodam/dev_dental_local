<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Theme\Joomla\ApiKey;

class LoadCustomizer
{
    public User $user;
    public Config $config;
    public ApiKey $apiKey;

    public function __construct(Config $config, ApiKey $apiKey, User $user)
    {
        $this->user = $user;
        $this->config = $config;
        $this->apiKey = $apiKey;
    }

    public function handle(): void
    {
        $this->config->addFile('customizer', Path::get('../../config/customizer.json', __DIR__));
        $this->config->add('customizer', [
            'config' => ['yootheme_apikey' => $this->apiKey->get()],
            'user_id' => $this->user->id,
        ]);

        // Joomla 4 does not distribute com_search
        if (!ComponentHelper::isEnabled('com_search')) {
            $this->config->del('customizer.panels.advanced.fields.search_module');
        }
    }
}
