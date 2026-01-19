<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Client;

use XTS_BUILD\Http\Client\HttpClient;
use XTS_BUILD\Psr\Http\Client\ClientInterface;
use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface BuzzClientInterface extends ClientInterface, HttpClient
{
    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request, array $options = []): ResponseInterface;
}
