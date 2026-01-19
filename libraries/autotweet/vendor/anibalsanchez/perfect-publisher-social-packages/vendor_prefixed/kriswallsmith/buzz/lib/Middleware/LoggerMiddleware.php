<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Middleware;

use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;
use XTS_BUILD\Psr\Log\LoggerInterface;
use XTS_BUILD\Psr\Log\NullLogger;

class LoggerMiddleware implements MiddlewareInterface
{
    private $logger;

    private $level;

    private $prefix;

    private $startTime;

    /**
     * @param string      $level
     * @param string|null $prefix
     */
    public function __construct(LoggerInterface $logger = null, $level = 'info', $prefix = null)
    {
        $this->logger = $logger ?: new NullLogger();
        $this->level = $level;
        $this->prefix = $prefix;
    }

    public function handleRequest(RequestInterface $request, callable $next)
    {
        $this->startTime = microtime(true);

        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $seconds = microtime(true) - $this->startTime;
        $this->logger->log($this->level, \sprintf('%sSent "%s %s" in %dms', $this->prefix, $request->getMethod(), $request->getUri(), round($seconds * 1000)));

        return $next($request, $response);
    }
}
