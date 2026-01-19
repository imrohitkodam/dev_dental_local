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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Agent;

defined('_JEXEC') or die;

class Browser extends Agent
{
    public function pass(): bool
    {
        if (empty($this->selection))
        {
            return false;
        }

        foreach ($this->selection as $browser)
        {
            if ( ! $this->passBrowser($browser))
            {
                continue;
            }

            return true;
        }

        return false;
    }
}
