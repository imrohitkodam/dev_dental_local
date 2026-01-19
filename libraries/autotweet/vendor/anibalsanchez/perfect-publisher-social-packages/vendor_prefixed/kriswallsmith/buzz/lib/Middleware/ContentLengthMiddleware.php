<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Middleware;

use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;

class ContentLengthMiddleware implements MiddlewareInterface
{
    public function handleRequest(RequestInterface $request, callable $next)
    {
        $body = $request->getBody();

        if (!$request->hasHeader('Content-Length')) {
            $request = $request->withAddedHeader('Content-Length', (string) $body->getSize());
        }

        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $next($request, $response);
    }
}
