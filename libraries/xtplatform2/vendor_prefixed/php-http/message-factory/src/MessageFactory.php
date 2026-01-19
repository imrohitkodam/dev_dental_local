<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

namespace XTP_BUILD\Http\Message;

/**
 * Factory for PSR-7 Request and Response.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated since version 1.1, use Psr\Http\Message\RequestFactoryInterface and Psr\Http\Message\ResponseFactoryInterface instead.
 */
interface MessageFactory extends RequestFactory, ResponseFactory
{
}
