<?php

declare (strict_types=1);
namespace _JchOptimizeVendor\Laminas\Diactoros;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createRequest(string $method, $uri) : RequestInterface
    {
        return new Request($uri, $method);
    }
}
