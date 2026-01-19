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

namespace XTP_BUILD\Extly\Infrastructure\Support\HttpClient;

use XTP_BUILD\MyCLabs\Enum\Enum;

/**
 * Class Request.
 */
final class RequestMethodEnum extends Enum
{
    public const DELETE = 'DELETE';

    public const GET = 'GET';

    public const OPTIONS = 'OPTIONS';

    public const POST = 'POST';

    public const PUT = 'PUT';
}
