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

class Month extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        $current_month = DateHelper::getString('', 'm'); // 01 (for January) through 12 (for December)

        foreach ($this->selection as &$month)
        {
            $month = DateHelper::getMonthNumber($month);
        }

        return $this->passSimple((int) $current_month);
    }
}
