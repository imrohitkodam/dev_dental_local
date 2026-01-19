<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

namespace XTP_BUILD\Http\Discovery\Strategy;

use XTP_BUILD\Psr\Http\Message\RequestFactoryInterface;
use XTP_BUILD\Psr\Http\Message\ResponseFactoryInterface;
use XTP_BUILD\Psr\Http\Message\ServerRequestFactoryInterface;
use XTP_BUILD\Psr\Http\Message\StreamFactoryInterface;
use XTP_BUILD\Psr\Http\Message\UploadedFileFactoryInterface;
use XTP_BUILD\Psr\Http\Message\UriFactoryInterface;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [
        RequestFactoryInterface::class => [
            'Phalcon\Http\Message\RequestFactory',
            'Nyholm\Psr7\Factory\Psr17Factory',
            'XTP_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTP_BUILD\Http\Factory\Diactoros\RequestFactory',
            'XTP_BUILD\Http\Factory\Guzzle\RequestFactory',
            'XTP_BUILD\Http\Factory\Slim\RequestFactory',
            'Laminas\Diactoros\RequestFactory',
            'Slim\Psr7\Factory\RequestFactory',
            'HttpSoft\Message\RequestFactory',
        ],
        ResponseFactoryInterface::class => [
            'Phalcon\Http\Message\ResponseFactory',
            'Nyholm\Psr7\Factory\Psr17Factory',
            'XTP_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTP_BUILD\Http\Factory\Diactoros\ResponseFactory',
            'XTP_BUILD\Http\Factory\Guzzle\ResponseFactory',
            'XTP_BUILD\Http\Factory\Slim\ResponseFactory',
            'Laminas\Diactoros\ResponseFactory',
            'Slim\Psr7\Factory\ResponseFactory',
            'HttpSoft\Message\ResponseFactory',
        ],
        ServerRequestFactoryInterface::class => [
            'Phalcon\Http\Message\ServerRequestFactory',
            'Nyholm\Psr7\Factory\Psr17Factory',
            'XTP_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTP_BUILD\Http\Factory\Diactoros\ServerRequestFactory',
            'XTP_BUILD\Http\Factory\Guzzle\ServerRequestFactory',
            'XTP_BUILD\Http\Factory\Slim\ServerRequestFactory',
            'Laminas\Diactoros\ServerRequestFactory',
            'Slim\Psr7\Factory\ServerRequestFactory',
            'HttpSoft\Message\ServerRequestFactory',
        ],
        StreamFactoryInterface::class => [
            'Phalcon\Http\Message\StreamFactory',
            'Nyholm\Psr7\Factory\Psr17Factory',
            'XTP_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTP_BUILD\Http\Factory\Diactoros\StreamFactory',
            'XTP_BUILD\Http\Factory\Guzzle\StreamFactory',
            'XTP_BUILD\Http\Factory\Slim\StreamFactory',
            'Laminas\Diactoros\StreamFactory',
            'Slim\Psr7\Factory\StreamFactory',
            'HttpSoft\Message\StreamFactory',
        ],
        UploadedFileFactoryInterface::class => [
            'Phalcon\Http\Message\UploadedFileFactory',
            'Nyholm\Psr7\Factory\Psr17Factory',
            'XTP_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTP_BUILD\Http\Factory\Diactoros\UploadedFileFactory',
            'XTP_BUILD\Http\Factory\Guzzle\UploadedFileFactory',
            'XTP_BUILD\Http\Factory\Slim\UploadedFileFactory',
            'Laminas\Diactoros\UploadedFileFactory',
            'Slim\Psr7\Factory\UploadedFileFactory',
            'HttpSoft\Message\UploadedFileFactory',
        ],
        UriFactoryInterface::class => [
            'Phalcon\Http\Message\UriFactory',
            'Nyholm\Psr7\Factory\Psr17Factory',
            'XTP_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTP_BUILD\Http\Factory\Diactoros\UriFactory',
            'XTP_BUILD\Http\Factory\Guzzle\UriFactory',
            'XTP_BUILD\Http\Factory\Slim\UriFactory',
            'Laminas\Diactoros\UriFactory',
            'Slim\Psr7\Factory\UriFactory',
            'HttpSoft\Message\UriFactory',
        ],
    ];

    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }

        return $candidates;
    }
}
