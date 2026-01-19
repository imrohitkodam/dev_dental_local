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

class OneSignalImageSettings
{
    private object $channel;

    private string $imageUrl;

    private array $imageSettings = [
        'android' => 'big_picture',
        'adm' => 'adm_big_picture',
    ];

    public function __construct(object $channel, string $imageUrl)
    {
        $this->channel = $channel;
        $this->imageUrl = $imageUrl;
    }

    public function apply(array &$notification): void
    {
        $this->addStandardImages($notification);
        $this->addChromeImages($notification);
    }

    private function addStandardImages(array $notification): void
    {
        foreach ($this->imageSettings as $platform => $key) {
            if ($this->channel->params->get($platform)) {
                $notification[$key] = $this->imageUrl;
            }
        }
    }

    private function addChromeImages(array $notification): void
    {
        if ($this->channel->params->get('chrome')) {
            $notification['chrome_big_picture'] = $this->imageUrl;
            $notification['chrome_web_image'] = $this->imageUrl;
        }
    }
}
