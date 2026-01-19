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

class OneSignalClient
{
    private OneSignalCredentials $oneSignalCredentials;

    private ?object $apiClient = null;

    public function __construct(OneSignalCredentials $oneSignalCredentials)
    {
        $this->oneSignalCredentials = $oneSignalCredentials;
    }

    public function testConnection(): bool
    {
        $this->getApiInstance()->apps()->getAll();

        return true;
    }

    public function sendNotification(OneSignalNotification $oneSignalNotification): array
    {
        $notification = $oneSignalNotification->toArray();

        return $this->getApiInstance()->notifications()->add($notification);
    }

    private function getApiInstance(): object
    {
        if (null === $this->apiClient) {
            $this->apiClient = $this->createApiClient();
        }

        return $this->apiClient;
    }

    private function createApiClient(): object
    {
        $config = new XTS_BUILD\OneSignal\Config(
            $this->oneSignalCredentials->getAppId(),
            $this->oneSignalCredentials->getAppApiAuthenticationKey(),
            $this->oneSignalCredentials->getOrgAuthenticationKey()
        );

        $psr17Factory = new XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory();
        $curl = new XTS_BUILD\Buzz\Client\Curl(
            $psr17Factory,
            ['curl' => [\CURLOPT_CAINFO => CAUTOTWEETNG_CAINFO]]
        );

        return new XTS_BUILD\OneSignal\OneSignal($config, $curl, $psr17Factory, $psr17Factory);
    }
}
