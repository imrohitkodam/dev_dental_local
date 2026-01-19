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

class Device extends Agent
{
    public function pass(): bool
    {
        return (in_array('mobile', $this->selection) && $this->isMobile())
            || (in_array('tablet', $this->selection) && $this->isTablet())
            || (in_array('desktop', $this->selection) && $this->isDesktop());
    }
}
