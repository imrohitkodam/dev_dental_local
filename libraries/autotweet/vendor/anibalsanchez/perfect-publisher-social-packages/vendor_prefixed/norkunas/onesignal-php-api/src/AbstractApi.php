<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\OneSignal;

use JsonException;
use XTS_BUILD\OneSignal\Exception\InvalidArgumentException;
use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\StreamInterface;
use const JSON_THROW_ON_ERROR;

abstract class AbstractApi
{
    /**
     * @var OneSignal
     */
    protected $client;

    public function __construct(OneSignal $client)
    {
        $this->client = $client;
    }

    protected function createRequest(string $method, string $uri): RequestInterface
    {
        $request = $this->client->getRequestFactory()->createRequest($method, OneSignal::API_URL.$uri);
        $request = $request->withHeader('Accept', 'application/json');

        return $request;
    }

    /**
     * @param mixed $value
     */
    protected function createStream($value, int $flags = null, int $maxDepth = 512): StreamInterface
    {
        $flags = $flags ?? (JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION);

        try {
            $value = json_encode($value, $flags | JSON_THROW_ON_ERROR, $maxDepth);
        } catch (JsonException $e) {
            throw new InvalidArgumentException("Invalid value for json encoding: {$e->getMessage()}.");
        }

        return $this->client->getStreamFactory()->createStream($value);
    }
}
