<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\Buzz\Middleware\History;

use XTS_BUILD\Psr\Http\Message\RequestInterface;
use XTS_BUILD\Psr\Http\Message\ResponseInterface;

class Entry
{
    private $request;
    private $response;
    private $duration;

    /**
     * @param float|null $duration The duration in seconds
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, float $duration = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->duration = $duration;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }
}
