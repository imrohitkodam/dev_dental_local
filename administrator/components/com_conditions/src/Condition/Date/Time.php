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
use RegularLabs\Component\Conditions\Administrator\Helper\Date as DateHelper;
use RegularLabs\Library\Date as RL_Date;

class Time extends Condition
{
    public function pass(): bool
    {
        $now = DateHelper::getTimeStamp();

        $comparison = $this->params->comparison ?? 'between';

        if ($comparison == 'before')
        {
            return $now < $this->getDateTime($this->params->time ?? '');
        }

        if ($comparison == 'after')
        {
            return $now > $this->getDateTime($this->params->time ?? '');
        }

        $from = $this->getDateTime($this->params->from);
        $to   = $this->getDateTime($this->params->to);

        if ($from > $to)
        {
            // from is after to (spans midnight)
            // current time should be:
            // - after from
            // - OR before to
            if ($now >= $from || $now < $to)
            {
                return true;
            }

            return false;
        }

        // to is after from (simple time span)
        // current time should be:
        // - after from
        // - AND before to
        if ($now >= $from && $now < $to)
        {
            return true;
        }

        return false;
    }

    private function getDateTime(string $value): int
    {
        $date = DateHelper::getString('', 'Y-m-d');
        $time = RL_Date::fixTime($value);

        return strtotime($date . ' ' . $time);
    }
}
