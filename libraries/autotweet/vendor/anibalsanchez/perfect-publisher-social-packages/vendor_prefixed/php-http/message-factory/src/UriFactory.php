<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

namespace XTS_BUILD\Http\Message;

use XTS_BUILD\Psr\Http\Message\UriInterface;

/**
 * Factory for PSR-7 URI.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated since version 1.1, use Psr\Http\Message\UriFactoryInterface instead.
 */
interface UriFactory
{
    /**
     * Creates an PSR-7 URI.
     *
     * @param string|UriInterface $uri
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException if the $uri argument can not be converted into a valid URI
     */
    public function createUri($uri);
}
