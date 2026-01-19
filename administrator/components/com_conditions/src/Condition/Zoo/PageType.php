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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Zoo;

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

defined('_JEXEC') or die;

class PageType extends Zoo
{
    use HasArraySelection;

    public function pass(): bool
    {
        return $this->passByPageType('com_zoo', $this->selection, true);
    }
}
