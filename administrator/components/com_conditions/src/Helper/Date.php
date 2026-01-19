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

namespace RegularLabs\Component\Conditions\Administrator\Helper;

use DateTimeZone;
use Joomla\CMS\Date\Date as JDate;
use Joomla\CMS\Factory as JFactory;
use RegularLabs\Library\Date as RL_Date;

class Date
{
    static $dates = [];
    static $timezone;

    public static function get(string $date = '', bool $ignore_time_zone = false): JDate
    {
        $date = RL_Date::fix($date);

        $id = 'date_' . $date . '_' . $ignore_time_zone;

        if (isset(self::$dates[$id]))
        {
            return self::$dates[$id];
        }

        self::$dates[$id] = JFactory::getDate($date);

        if ( ! $ignore_time_zone)
        {
            self::$dates[$id]->setTimeZone(self::getTimeZone());
        }

        return self::$dates[$id];
    }

    public static function getDayNumber(string $string): int
    {
        if ( ! is_string($string))
        {
            return false;
        }

        if (is_numeric($string))
        {
            return $string;
        }

        $string = strip_tags($string);

        return (int) date('N', strtotime($string));
    }

    public static function getMonthNumber(string $string): int
    {
        if ( ! is_string($string))
        {
            return false;
        }

        if (is_numeric($string))
        {
            return $string;
        }

        return (int) date('m', strtotime($string));
    }

    public static function getNow(): string
    {
        return self::getString();
    }

    public static function getString(
        string $date = '',
        string $format = 'Y-m-d H:i:s',
        bool   $ignore_time_zone = false
    ): string
    {
        $date = self::get($date, $ignore_time_zone);

        return $date->format($format, true);
    }

    public static function getTimeStamp(string $date = '', bool $ignore_time_zone = false): int
    {
        return strtotime(self::getString($date, 'Y-m-d H:i:s', $ignore_time_zone));
    }

    public static function pass(object $params, ?string $date = null): bool
    {
        $datetime = strtotime($date ?: self::getNow());

        $comparison = $params->comparison ?? 'between';

        if ($comparison == 'before')
        {
            return $datetime < self::getTimeStamp($params->date ?? '', true);
        }

        if ($comparison == 'after')
        {
            return $datetime > self::getTimeStamp($params->date ?? '', true);
        }

        $from = $params->from ?? false;
        $to   = $params->to ?? false;

        // no date range set
        if ( ! $from && ! $to)
        {
            return true;
        }

        if (empty($params->recurring))
        {
            $from = $from ? self::getTimeStamp($from, true) : false;
            $to   = $to ? self::getTimeStamp($to, true) : false;

            return ( ! $from || $datetime >= $from)
                && ( ! $to || $datetime <= $to);
        }

        $from = strtotime(date('Y') . self::getString($from, '-m-d H:i:s', true));
        $to   = strtotime(date('Y') . self::getString($to, '-m-d H:i:s', true));

        // pass: from is before to
        if ($from <= $to)
        {
            // and now is between from and to
            return ($from < $datetime && $to > $datetime);
        }

        // pass: from is later in year than to and:
        // - to is after up
        // - or now is before from
        return ($to > $datetime || $from < $datetime);
    }

    private static function getTimeZone(): DateTimeZone
    {
        if ( ! is_null(self::$timezone))
        {
            return self::$timezone;
        }

        self::$timezone = new DateTimeZone(JFactory::getApplication()->get('offset'));

        return self::$timezone;
    }
}
