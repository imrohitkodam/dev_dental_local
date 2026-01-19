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

class Season extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        $season = self::getSeason($this->params->hemisphere);

        return $this->passSimple($season);
    }

    private function getSeason(string $hemisphere = 'northern'): string
    {
        $now = DateHelper::getTimeStamp();

        // Get year of date specified
        $date_year = DateHelper::getString('', 'Y'); // Four digit representation for the year

        // Specify the season names
        $season_names = ['winter', 'spring', 'summer', 'fall'];

        // Declare season date ranges
        switch (strtolower($hemisphere))
        {
            case 'southern':
                if (
                    $now < strtotime($date_year . '-03-21')
                    || $now >= strtotime($date_year . '-12-21')
                )
                {
                    return $season_names[2]; // Must be in Summer
                }

                if ($now >= strtotime($date_year . '-09-23'))
                {
                    return $season_names[1]; // Must be in Spring
                }

                if ($now >= strtotime($date_year . '-06-21'))
                {
                    return $season_names[0]; // Must be in Winter
                }

                if ($now >= strtotime($date_year . '-03-21'))
                {
                    return $season_names[3]; // Must be in Fall
                }
                break;
            case 'australia':
                if (
                    $now < strtotime($date_year . '-03-01')
                    || $now >= strtotime($date_year . '-12-01')
                )
                {
                    return $season_names[2]; // Must be in Summer
                }

                if ($now >= strtotime($date_year . '-09-01'))
                {
                    return $season_names[1]; // Must be in Spring
                }

                if ($now >= strtotime($date_year . '-06-01'))
                {
                    return $season_names[0]; // Must be in Winter
                }

                if ($now >= strtotime($date_year . '-03-01'))
                {
                    return $season_names[3]; // Must be in Fall
                }
                break;
            default: // northern
                if (
                    $now < strtotime($date_year . '-03-21')
                    || $now >= strtotime($date_year . '-12-21')
                )
                {
                    return $season_names[0]; // Must be in Winter
                }

                if ($now >= strtotime($date_year . '-09-23'))
                {
                    return $season_names[3]; // Must be in Fall
                }

                if ($now >= strtotime($date_year . '-06-21'))
                {
                    return $season_names[2]; // Must be in Summer
                }

                if ($now >= strtotime($date_year . '-03-21'))
                {
                    return $season_names[1]; // Must be in Spring
                }
                break;
        }

        return 0;
    }
}
