<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

/*
 * @package     Extly Infrastructure Support
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 * @license     https://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @see         https://www.extly.com
 */

namespace XTP_BUILD\Extly\Infrastructure\Support\HttpClient;

use XTP_BUILD\Extly\Infrastructure\Creator\CreatorTrait;
use XTP_BUILD\Extly\Infrastructure\Support\SupportException;
use XTP_BUILD\Http\Client\Common\HttpMethodsClient;
use XTP_BUILD\Http\Client\Common\Plugin\RedirectPlugin;
use XTP_BUILD\Http\Client\Common\Plugin\RetryPlugin;
use XTP_BUILD\Http\Client\Common\PluginClient;
use XTP_BUILD\Http\Client\Curl\Client;
use XTP_BUILD\Http\Message\Authentication\BasicAuth;
use XTP_BUILD\Http\Message\MessageFactory\GuzzleMessageFactory;
use XTP_BUILD\Http\Message\StreamFactory\GuzzleStreamFactory;

final class Helper
{
    use CreatorTrait;

    private $authentication;

    private $httpClient;

    private $messageFactory;

    public function __construct()
    {
        $this->messageFactory = new GuzzleMessageFactory();
        $guzzleStreamFactory = new GuzzleStreamFactory();

        $this->httpClient = new Client($this->messageFactory, $guzzleStreamFactory);
    }

    public function authWithBasicAuth($username, $password)
    {
        $this->authentication = new BasicAuth($username, $password);

        return $this;
    }

    public function get($uri, $processAllHttpCases = true)
    {
        try {
            if ($processAllHttpCases) {
                return $this->checkResponse($this->processAllHttpCases($uri));
            }

            return $this->checkResponse($this->rawHttpGet($uri));
        } catch (\Exception $exception) {
            throw new SupportException($exception->getMessage());
        }
    }

    public function getLocationHeader($response)
    {
        if ($response->hasHeader('Location')) {
            return $response->getHeader('Location');
        }

        return null;
    }

    public function isOk($response)
    {
        $httpStatusCode = $response->getStatusCode();

        return StatusCodeEnum::HTTP_STATUS_OK === $httpStatusCode;
    }

    public function isRedirection($response)
    {
        $httpStatusCode = $response->getStatusCode();

        return ($httpStatusCode >= StatusCodeEnum::HTTP_STATUS_MOVED_PERMANENTLY)
            && ($httpStatusCode <= StatusCodeEnum::HTTP_STATUS_PERMANENT_REDIRECT);
    }

    public function rawHttpGet($uri)
    {
        $httpMethodsClient = new HttpMethodsClient($this->httpClient, $this->messageFactory);

        return $httpMethodsClient->get($uri);
    }

    private function processAllHttpCases($uri)
    {
        $request = $this->messageFactory->createRequest(RequestMethodEnum::GET, $uri);

        if ($this->authentication) {
            $request = $this->authentication->authenticate($request);
        }

        return $this->getRedirectHttpClient()->sendRequest($request);
    }

    private function getRedirectHttpClient()
    {
        $retryPlugin = new RetryPlugin();
        $redirectPlugin = new RedirectPlugin();

        return new PluginClient(
            $this->httpClient,
            [
                $retryPlugin,
                $redirectPlugin,
            ]
        );
    }

    private function checkResponse($response)
    {
        if ($this->isOk($response)) {
            return $response;
        }

        $httpStatusCode = $response->getStatusCode();

        throw new SupportException(StatusCodeEnum::search($httpStatusCode), $httpStatusCode);
    }
}
