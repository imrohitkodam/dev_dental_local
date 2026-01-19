<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\Helper;

use RegularLabs\Library\Cache as RL_Cache;
use RegularLabs\Library\Document as RL_Document;

class Cache extends RL_Cache
{
    public function __construct(
        mixed  $id = null,
        string $group = 'rl_conditions',
        int    $class_offset = 0
    )
    {
        parent::__construct($id, $group, 1 + $class_offset);
    }

    public function resetAll(): void
    {
        parent::useFiles();
        parent::resetAll();
    }

    public function useFiles(int $time_to_life_in_minutes = 0, bool $force_caching = true): self
    {
        if (RL_Document::isAdmin())
        {
            return $this;
        }

        return parent::useFiles($time_to_life_in_minutes, $force_caching);
    }
}
