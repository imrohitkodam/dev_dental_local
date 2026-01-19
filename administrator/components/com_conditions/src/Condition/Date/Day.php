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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Date;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Component\Conditions\Administrator\Helper\Date as DateHelper;

class Day extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        $current_day = (int) DateHelper::getString('', 'N');  // 1 (for Monday) though 7 (for Sunday )

        foreach ($this->selection as &$day)
        {
            $day = DateHelper::getDayNumber($day);
        }

        return $this->passSimple($current_day);
    }
}
