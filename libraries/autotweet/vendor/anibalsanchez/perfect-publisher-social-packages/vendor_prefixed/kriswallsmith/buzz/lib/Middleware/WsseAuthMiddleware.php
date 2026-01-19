<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Middleware;

use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;

class WsseAuthMiddleware implements MiddlewareInterface
{
    private $username;
    private $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function handleRequest(RequestInterface $request, callable $next)
    {
        $nonce = substr(sha1(uniqid('', true)), 0, 16);
        $created = date('c');
        $digest = base64_encode(sha1(base64_decode($nonce).$created.$this->password, true));

        $wsse = \sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $this->username,
            $digest,
            $nonce,
            $created
        );

        $request = $request
            ->withHeader('Authorization', 'WSSE profile="UsernameToken"')
            ->withHeader('X-WSSE', $wsse);

        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $next($request, $response);
    }
}
