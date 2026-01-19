<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

/*
 * @package     Extly Infrastructure Support
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 * @license     https://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @see         https://www.extly.com
 */

if (class_exists(\Extly\Infrastructure\Support\HttpClient\Helper::class)) {
    class_alias(
        Extly\Infrastructure\Support\HttpClient\Helper::class,
        'XTP_BUILD\Extly\Infrastructure\Support\HttpClient\HttpClientHelper'
    );
}

if (class_exists(\Extly\Infrastructure\Support\UrlTools\Helper::class)) {
    class_alias(
        Extly\Infrastructure\Support\UrlTools\Helper::class,
        'XTP_BUILD\Extly\Infrastructure\Support\UrlHelper'
    );
}

if (class_exists('XTP_BUILD\Extly\Infrastructure\Support\HttpClient\Helper')) {
    class_alias(
        XTP_BUILD\Extly\Infrastructure\Support\HttpClient\Helper::class,
        'XTP_BUILD\Extly\Infrastructure\Support\HttpClient\HttpClientHelper'
    );
}

if (class_exists('XTP_BUILD\Extly\Infrastructure\Support\UrlTools\Helper')) {
    class_alias(
        XTP_BUILD\Extly\Infrastructure\Support\UrlTools\Helper::class,
        'XTP_BUILD\Extly\Infrastructure\Support\UrlHelper'
    );
}
