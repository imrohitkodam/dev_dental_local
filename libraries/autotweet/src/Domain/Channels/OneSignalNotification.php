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

class OneSignalNotification
{
    private string $message;

    private object $data;

    private object $channel;

    private XTJoomlaCompatibility $xtJoomlaCompatibility;

    public function __construct(
        string $message,
        object $data,
        object $channel,
        XTJoomlaCompatibility $xtJoomlaCompatibility
    ) {
        $this->message = $message;
        $this->data = $data;
        $this->channel = $channel;
        $this->xtJoomlaCompatibility = $xtJoomlaCompatibility;
    }

    public function toArray(): array
    {
        $notification = $this->createBaseNotification();
        $this->addPlatformSettings($notification);
        $this->addUrlIfPresent($notification);
        $this->addImageIfPresent($notification);

        return $notification;
    }

    private function createBaseNotification(): array
    {
        $notification = [
            'contents' => ['en' => $this->message],
            'headings' => ['en' => $this->xtJoomlaCompatibility->getSiteName()],
            'included_segments' => ['All'],
        ];

        // Web Push
        if ($this->channel->params->get('chrome')) {
            $notification['isChromeWeb'] = true;
        }

        if ($this->channel->params->get('firefox')) {
            $notification['isFirefox'] = true;
        }

        if ($this->channel->params->get('safari')) {
            $notification['isSafari'] = true;
        }

        // Push Notifications
        if ($this->channel->params->get('ios')) {
            $notification['isIos'] = true;
        }

        if ($this->channel->params->get('android')) {
            $notification['isAndroid'] = true;
        }

        if ($this->channel->params->get('adm')) {
            $notification['isAdm'] = true;
        }

        if ($this->channel->params->get('wp')) {
            $notification['isWP'] = true;
            $notification['isWP_WNS'] = true;
        }

        if (!empty($this->data->org_url)) {
            $notification['url'] = $this->data->org_url;
        }

        if (
            SelectControlHelper::MEDIA_MODE_TEXT_ONLY_POST !== $this->channel->media_mode &&
            !empty($this->data->image_url)
        ) {
            // https://documentation.onesignal.com/docs/push#image
            if ($this->channel->params->get('android')) {
                $notification['big_picture'] = $this->data->image_url;
            }

            if ($this->channel->params->get('adm')) {
                $notification['adm_big_picture'] = $this->data->image_url;
            }

            if ($this->channel->params->get('chrome')) {
                $notification['chrome_big_picture'] = $this->data->image_url;
                $notification['chrome_web_image'] = $this->data->image_url;
            }

            if ($this->channel->params->get('ios')) {
                $notification['ios_attachments']['id'] = $this->data->image_url;
            }
        }

        return $notification;
    }

    private function addPlatformSettings(array &$notification): void
    {
        $oneSignalPlatformSettings = new OneSignalPlatformSettings($this->channel);
        $oneSignalPlatformSettings->apply($notification);
    }

    private function addUrlIfPresent(array &$notification): void
    {
        if (!empty($this->data->org_url)) {
            $notification['url'] = $this->data->org_url;
        }
    }

    private function addImageIfPresent(array &$notification): void
    {
        if (!$this->hasImageToAdd()) {
            return;
        }

        $oneSignalImageSettings = new OneSignalImageSettings($this->channel, $this->data->image_url);
        $oneSignalImageSettings->apply($notification);
    }

    private function hasImageToAdd(): bool
    {
        return !empty($this->data->image_url);
    }
}
