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

class Region extends Geo
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ( ! $this->getGeo())
        {
            return false;
        }

        $country = $this->geo->countryCode ?? '';
        $regions = $this->geo->regionCodes ?? [];

        if (empty($country) || empty($regions))
        {
            return false;
        }

        array_walk($regions, function (&$region, $key, $country) {
            $region = $this->getCountryRegionCode($region, $country);
        }, $country);

        return $this->passSimple($regions);
    }

    private function getCountryRegionCode(string $region, string $country): string
    {
        return match ($country . '-' . $region)
        {
            'MX-CMX' => 'MX-DIF',
            default  => $country . '-' . $region,
        };
    }
}
