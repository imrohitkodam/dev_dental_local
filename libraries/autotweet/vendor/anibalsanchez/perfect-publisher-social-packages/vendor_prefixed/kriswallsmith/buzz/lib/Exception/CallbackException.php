<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Exception;

use XTS_BUILD\Psr\Http\Client\RequestExceptionInterface as PsrRequestException;
use XTS_BUILD\Psr\Http\Message\RequestInterface;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class CallbackException extends ClientException implements PsrRequestException
{
    private $request;

    public function __construct(RequestInterface $request, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, $code, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
