<?php

declare(strict_types=1);

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

class OneSignalPlatformSettings
{
    private object $channel;

    private array $platforms = [
        'chrome' => 'isChromeWeb',
        'firefox' => 'isFirefox',
        'safari' => 'isSafari',
        'ios' => 'isIos',
        'android' => 'isAndroid',
        'adm' => 'isAdm',
    ];

    public function __construct(object $channel)
    {
        $this->channel = $channel;
    }

    public function apply(array &$notification): void
    {
        $this->addStandardPlatforms($notification);
        $this->addWindowsPhone($notification);
    }

    private function addStandardPlatforms(array &$notification): void
    {
        foreach ($this->platforms as $param => $key) {
            if ($this->channel->params->get($param)) {
                $notification[$key] = true;
            }
        }
    }

    private function addWindowsPhone(array &$notification): void
    {
        if ($this->channel->params->get('wp')) {
            $notification['isWP'] = true;
            $notification['isWP_WNS'] = true;
        }
    }
}
