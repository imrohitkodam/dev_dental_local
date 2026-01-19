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

defined('_JEXEC') or die;

class PostalCode extends Geo
{
    public function pass(): bool
    {
        if ( ! $this->getGeo() || empty($this->geo->postalCode))
        {
            return false;
        }

        // replace dashes with dots: 730-0011 => 730.0011
        $postalcode = str_replace('-', '.', $this->geo->postalCode);

        return $this->passInRange($postalcode);
    }
}
