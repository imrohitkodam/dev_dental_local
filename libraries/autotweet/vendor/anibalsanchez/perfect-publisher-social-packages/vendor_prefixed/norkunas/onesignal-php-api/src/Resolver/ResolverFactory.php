<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\OneSignal\Resolver;

use XTS_BUILD\OneSignal\Config;

class ResolverFactory
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function createAppResolver(): AppResolver
    {
        return new AppResolver();
    }

    public function createSegmentResolver(): SegmentResolver
    {
        return new SegmentResolver();
    }

    public function createDeviceSessionResolver(): DeviceSessionResolver
    {
        return new DeviceSessionResolver();
    }

    public function createDevicePurchaseResolver(): DevicePurchaseResolver
    {
        return new DevicePurchaseResolver();
    }

    public function createDeviceFocusResolver(): DeviceFocusResolver
    {
        return new DeviceFocusResolver();
    }

    public function createNewDeviceResolver(): DeviceResolver
    {
        return new DeviceResolver($this->config, true);
    }

    public function createExistingDeviceResolver(): DeviceResolver
    {
        return new DeviceResolver($this->config, false);
    }

    public function createNotificationResolver(): NotificationResolver
    {
        return new NotificationResolver($this->config);
    }

    public function createNotificationHistoryResolver(): NotificationHistoryResolver
    {
        return new NotificationHistoryResolver($this->config);
    }
}
