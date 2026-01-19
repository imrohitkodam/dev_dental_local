<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

declare(strict_types=1);

namespace XTS_BUILD\OneSignal\Resolver;

interface ResolverInterface
{
    /**
     * Resolve options array.
     */
    public function resolve(array $data): array;
}
