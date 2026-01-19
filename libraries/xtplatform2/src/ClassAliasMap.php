<?php

/*
 * @package     Extly Infrastructure Support
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2022 Extly, CB. All rights reserved.
 * @license     https://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @see         https://www.extly.com
 */

if (class_exists('Psr\Http\Message\MessageInterface')) {
    class_alias(Psr\Http\Message\MessageInterface::class, 'XTP_BUILD\Psr\Http\Message\MessageInterface');
}

if (class_exists('Psr\Http\Message\RequestInterface')) {
    class_alias(Psr\Http\Message\RequestInterface::class, 'XTP_BUILD\Psr\Http\Message\RequestInterface');
}

if (class_exists('Psr\Http\Message\ResponseInterface')) {
    class_alias(Psr\Http\Message\ResponseInterface::class, 'XTP_BUILD\Psr\Http\Message\ResponseInterface');
}

if (class_exists('Psr\Http\Message\ServerRequestInterface')) {
    class_alias(Psr\Http\Message\ServerRequestInterface::class, 'XTP_BUILD\Psr\Http\Message\ServerRequestInterface');
}

if (class_exists('Psr\Http\Message\StreamInterface')) {
    class_alias(Psr\Http\Message\StreamInterface::class, 'XTP_BUILD\Psr\Http\Message\StreamInterface');
}

if (class_exists('Psr\Http\Message\UploadedFileInterface')) {
    class_alias(Psr\Http\Message\UploadedFileInterface::class, 'XTP_BUILD\Psr\Http\Message\UploadedFileInterface');
}

if (class_exists('Psr\Http\Message\UriInterface')) {
    class_alias(Psr\Http\Message\UriInterface::class, 'XTP_BUILD\Psr\Http\Message\UriInterface');
}

if (class_exists('Psr\Log\AbstractLogger')) {
    class_alias(Psr\Log\AbstractLogger::class, 'XTP_BUILD\Psr\Log\AbstractLogger');
}

if (class_exists('Psr\Log\InvalidArgumentException')) {
    class_alias(Psr\Log\InvalidArgumentException::class, 'XTP_BUILD\Psr\Log\InvalidArgumentException');
}

if (class_exists('Psr\Log\LoggerAwareInterface')) {
    class_alias(LoggerAwareInPsr\Log\LoggerAwareInterfaceterface::class, 'XTP_BUILD\Psr\Log\LoggerAwareInterface');
}

if (class_exists('Psr\Log\LoggerAwareTrait')) {
    class_alias(Psr\Log\LoggerAwareTrait::class, 'XTP_BUILD\Psr\Log\LoggerAwareTrait');
}

if (class_exists('Psr\Log\LoggerInterface')) {
    class_alias(Psr\Log\LoggerInterface::class, 'XTP_BUILD\Psr\Log\LoggerInterface');
}

if (class_exists('Psr\Log\LoggerTrait')) {
    class_alias(LoggePsr\Log\LoggerTraitrTrait::class, 'XTP_BUILD\Psr\Log\LoggerTrait');
}

if (class_exists('Psr\Log\LogLevel')) {
    class_alias(Psr\Log\LogLevel::class, 'XTP_BUILD\Psr\Log\LogLevel');
}

if (class_exists('Psr\Log\NullLogger')) {
    class_alias(Psr\Log\NullLogger::class, 'XTP_BUILD\Psr\Log\NullLogger');
}
