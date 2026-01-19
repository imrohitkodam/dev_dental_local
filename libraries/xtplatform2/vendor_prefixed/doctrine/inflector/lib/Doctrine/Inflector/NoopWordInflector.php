<?php /* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

declare(strict_types=1);

namespace XTP_BUILD\Doctrine\Inflector;

class NoopWordInflector implements WordInflector
{
    public function inflect(string $word): string
    {
        return $word;
    }
}
