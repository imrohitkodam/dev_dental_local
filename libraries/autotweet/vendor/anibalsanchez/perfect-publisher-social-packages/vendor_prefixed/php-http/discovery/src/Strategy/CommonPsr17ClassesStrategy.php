<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

namespace XTS_BUILD\Http\Discovery\Strategy;

use XTS_BUILD\Psr\Http\Message\RequestFactoryInterface;
use XTS_BUILD\Psr\Http\Message\ResponseFactoryInterface;
use XTS_BUILD\Psr\Http\Message\ServerRequestFactoryInterface;
use XTS_BUILD\Psr\Http\Message\StreamFactoryInterface;
use XTS_BUILD\Psr\Http\Message\UploadedFileFactoryInterface;
use XTS_BUILD\Psr\Http\Message\UriFactoryInterface;

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
            'XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory',
            'XTS_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTS_BUILD\Http\Factory\Diactoros\RequestFactory',
            'XTS_BUILD\Http\Factory\Guzzle\RequestFactory',
            'XTS_BUILD\Http\Factory\Slim\RequestFactory',
            'Laminas\Diactoros\RequestFactory',
            'Slim\Psr7\Factory\RequestFactory',
            'HttpSoft\Message\RequestFactory',
        ],
        ResponseFactoryInterface::class => [
            'Phalcon\Http\Message\ResponseFactory',
            'XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory',
            'XTS_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTS_BUILD\Http\Factory\Diactoros\ResponseFactory',
            'XTS_BUILD\Http\Factory\Guzzle\ResponseFactory',
            'XTS_BUILD\Http\Factory\Slim\ResponseFactory',
            'Laminas\Diactoros\ResponseFactory',
            'Slim\Psr7\Factory\ResponseFactory',
            'HttpSoft\Message\ResponseFactory',
        ],
        ServerRequestFactoryInterface::class => [
            'Phalcon\Http\Message\ServerRequestFactory',
            'XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory',
            'XTS_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTS_BUILD\Http\Factory\Diactoros\ServerRequestFactory',
            'XTS_BUILD\Http\Factory\Guzzle\ServerRequestFactory',
            'XTS_BUILD\Http\Factory\Slim\ServerRequestFactory',
            'Laminas\Diactoros\ServerRequestFactory',
            'Slim\Psr7\Factory\ServerRequestFactory',
            'HttpSoft\Message\ServerRequestFactory',
        ],
        StreamFactoryInterface::class => [
            'Phalcon\Http\Message\StreamFactory',
            'XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory',
            'XTS_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTS_BUILD\Http\Factory\Diactoros\StreamFactory',
            'XTS_BUILD\Http\Factory\Guzzle\StreamFactory',
            'XTS_BUILD\Http\Factory\Slim\StreamFactory',
            'Laminas\Diactoros\StreamFactory',
            'Slim\Psr7\Factory\StreamFactory',
            'HttpSoft\Message\StreamFactory',
        ],
        UploadedFileFactoryInterface::class => [
            'Phalcon\Http\Message\UploadedFileFactory',
            'XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory',
            'XTS_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTS_BUILD\Http\Factory\Diactoros\UploadedFileFactory',
            'XTS_BUILD\Http\Factory\Guzzle\UploadedFileFactory',
            'XTS_BUILD\Http\Factory\Slim\UploadedFileFactory',
            'Laminas\Diactoros\UploadedFileFactory',
            'Slim\Psr7\Factory\UploadedFileFactory',
            'HttpSoft\Message\UploadedFileFactory',
        ],
        UriFactoryInterface::class => [
            'Phalcon\Http\Message\UriFactory',
            'XTS_BUILD\Nyholm\Psr7\Factory\Psr17Factory',
            'XTS_BUILD\GuzzleHttp\Psr7\HttpFactory',
            'XTS_BUILD\Http\Factory\Diactoros\UriFactory',
            'XTS_BUILD\Http\Factory\Guzzle\UriFactory',
            'XTS_BUILD\Http\Factory\Slim\UriFactory',
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
