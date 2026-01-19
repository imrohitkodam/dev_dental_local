<?php

declare (strict_types=1);
namespace _JchOptimizeVendor\Laminas\Diactoros;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUri(string $uri = '') : UriInterface
    {
        return new Uri($uri);
    }
}
