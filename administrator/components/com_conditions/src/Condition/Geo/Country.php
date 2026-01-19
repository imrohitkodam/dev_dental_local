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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Geo;

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

defined('_JEXEC') or die;

class Country extends Geo
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ( ! $this->getGeo())
        {
            return false;
        }

        $country     = $this->geo->country ?? '';
        $countryCode = $this->geo->countryCode ?? '';

        if (empty($country) && empty($countryCode))
        {
            return false;
        }

        return $this->passSimple([$country, $countryCode]);
    }
}
