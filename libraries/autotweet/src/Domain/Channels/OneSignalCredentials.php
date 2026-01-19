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

class OneSignalCredentials
{
    private ?string $appId;

    private ?string $appApiAuthenticationKey;

    private ?string $orgApiAuthenticationKey;

    private function __construct(?string $appId, ?string $appApiAuthenticationKey, ?string $orgApiAuthenticationKey)
    {
        $this->appId = $appId;
        $this->appApiAuthenticationKey = $appApiAuthenticationKey;
        $this->orgApiAuthenticationKey = $orgApiAuthenticationKey;
    }

    public static function fromChannel(object $channel): self
    {
        $params = null;

        if (isset($channel->params)) {
            $params = json_decode($channel->params);
        } elseif (isset($channel->xtform)) {
            $params = $channel->xtform->jsonSerialize();
        }

        return new self(
            $params->app_id,
            $params->app_api_authentication_key,
            $params->org_api_authentication_key
        );
    }

    public static function fromParameters(?string $appId, ?string $appApiAuthenticationKey, ?string $orgApiAuthenticationKey): self
    {
        return new self($appId, $appApiAuthenticationKey, $orgApiAuthenticationKey);
    }

    public function isValid(): bool
    {
        return null !== $this->appId && '' !== $this->appId && '0' !== $this->appId;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function getAppApiAuthenticationKey(): ?string
    {
        return $this->appApiAuthenticationKey;
    }

    public function getOrgAuthenticationKey(): ?string
    {
        return $this->orgApiAuthenticationKey;
    }
}
