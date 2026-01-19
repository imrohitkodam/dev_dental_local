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

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Log\Log as JLog;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Library\GeoIp\GeoIp as RL_GeoIP;

abstract class Geo extends Condition
{
    var $geo;

    public function getGeo(string $ip = ''): object|false
    {
        if ( ! is_null($this->geo))
        {
            return $this->geo;
        }


        $geo = $this->getGeoObject($ip);

        if (empty($geo))
        {
            return false;
        }

        $this->geo = $geo->get();

        if (JFactory::getApplication()->get('debug'))
        {
            JLog::addLogger(['text_file' => 'regularlabs_geoip.log.php'], JLog::ALL, ['regularlabs_geoip']);
            JLog::add(json_encode($this->geo), JLog::DEBUG, 'regularlabs_geoip');
        }

        return $this->geo;
    }

    private function getGeoObject(string $ip): RL_GeoIP|false
    {
        if ( ! class_exists('RegularLabs\\Library\\GeoIp\\GeoIp'))
        {
            return false;
        }

        return new RL_GeoIP($ip);
    }
}
