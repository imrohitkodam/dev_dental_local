<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Middleware;

use XTS_BUILD\Buzz\Exception\InvalidArgumentException;
use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;

class BearerAuthMiddleware implements MiddlewareInterface
{
    private $accessToken;

    public function __construct(string $accessToken)
    {
        if (empty($accessToken)) {
            throw new InvalidArgumentException('You must supply a non empty accessToken');
        }

        $this->accessToken = $accessToken;
    }

    public function handleRequest(RequestInterface $request, callable $next)
    {
        $request = $request->withAddedHeader('Authorization', \sprintf('Bearer %s', $this->accessToken));

        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $next($request, $response);
    }
}
