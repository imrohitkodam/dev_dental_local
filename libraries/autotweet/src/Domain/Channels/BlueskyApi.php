<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

use JsonException;
use RuntimeException;

final class BlueskyApi
{
    private ?string $accountDid = null;

    private ?string $apiKey = null;

    private ?string $refreshToken = null;

    private string $apiUri;

    public function __construct(string $api_uri = 'https://bsky.social/xrpc/')
    {
        $this->apiUri = $api_uri;
    }

    /**
     * @throws RuntimeException|JsonException
     */
    public function auth(string $handleOrToken, string $appPassword = null): void
    {
        if ($handleOrToken && $appPassword) {
            $data = $this->startNewSession($handleOrToken, $appPassword);
        } else {
            $data = $this->refreshSession($handleOrToken);
        }

        $this->accountDid = $data->did;
        $this->apiKey = $data->accessJwt;
        $this->refreshToken = $data->refreshJwt;
    }

    public function getAccountDid(): ?string
    {
        return $this->accountDid;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @throws JsonException
     */
    public function request(string $type, string $request, array $args = [], string $body = null, string $content_type = null): object
    {
        $url = $this->apiUri.$request;

        if (('GET' === $type) && (count($args))) {
            $url .= '?'.http_build_query($args);
        } elseif (('POST' === $type) && (!$content_type)) {
            $content_type = 'application/json';
        }

        $headers = [];
        if ($this->apiKey) {
            $headers[] = 'Authorization: Bearer '.$this->apiKey;
        }

        if ($content_type) {
            $headers[] = 'Content-Type: '.$content_type;

            if (('application/json' === $content_type) && (count($args))) {
                $body = json_encode($args, \JSON_THROW_ON_ERROR);
                $args = [];
            }
        }

        $curlHandle = curl_init();
        curl_setopt($curlHandle, \CURLOPT_URL, $url);

        if ($headers !== []) {
            curl_setopt($curlHandle, \CURLOPT_HTTPHEADER, $headers);
        }

        switch ($type) {
            case 'POST':
                curl_setopt($curlHandle, \CURLOPT_POST, 1);
                break;
            case 'GET':
                curl_setopt($curlHandle, \CURLOPT_HTTPGET, 1);
                break;
            default:
                curl_setopt($curlHandle, \CURLOPT_CUSTOMREQUEST, $type);
        }

        if ($body) {
            curl_setopt($curlHandle, \CURLOPT_POSTFIELDS, $body);
        } elseif (('GET' !== $type) && (count($args))) {
            curl_setopt($curlHandle, \CURLOPT_POSTFIELDS, json_encode($args, \JSON_THROW_ON_ERROR));
        }

        curl_setopt($curlHandle, \CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, \CURLOPT_VERBOSE, 0);
        curl_setopt($curlHandle, \CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, \CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curlHandle, \CURLOPT_CAINFO, CAUTOTWEETNG_CAINFO);

        $data = curl_exec($curlHandle);
        curl_close($curlHandle);

        return json_decode($data, false, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @throws RuntimeException|JsonException
     */
    private function startNewSession(string $handle, string $appPassword): object
    {
        $args = [
            'identifier' => $handle,
            'password' => $appPassword,
        ];
        $data = $this->request('POST', 'com.atproto.server.createSession', $args);

        if (!empty($data->error)) {
            throw new RuntimeException($data->message);
        }

        return $data;
    }

    /**
     * @throws RuntimeException|JsonException
     */
    private function refreshSession(string $api_key): object
    {
        $this->apiKey = $api_key;
        $data = $this->request('POST', 'com.atproto.server.refreshSession');
        unset($this->apiKey);

        if ($data->error) {
            throw new RuntimeException($data->message);
        }

        return $data;
    }
}
