<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\User\User;
use YOOtheme\Theme\Joomla\ApiKey;

class SaveInstallerApiKey
{
    public User $user;
    public ApiKey $apiKey;

    public function __construct(User $user, ApiKey $apiKey)
    {
        $this->user = $user;
        $this->apiKey = $apiKey;
    }

    public function handle(array $values): array
    {
        if (!isset($values['yootheme_apikey'])) {
            return $values;
        }

        if (
            $this->user->authorise('core.edit', 'com_installer') &&
            $this->user->authorise('core.manage', 'com_installer')
        ) {
            $this->apiKey->set($values['yootheme_apikey']);
        }

        unset($values['yootheme_apikey']);

        return $values;
    }
}
