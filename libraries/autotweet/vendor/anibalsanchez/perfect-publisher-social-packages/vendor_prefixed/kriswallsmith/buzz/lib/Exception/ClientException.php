<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Exception;

use XTS_BUILD\Http\Client\Exception as HTTPlugException;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ClientException extends \RuntimeException implements ExceptionInterface, HTTPlugException
{
}
