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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Content\Article;

defined('_JEXEC') or die;

use DateTimeZone;
use Joomla\CMS\Date\Date as JDate;
use Joomla\CMS\Factory as JFactory;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper as JFieldsHelper;
use RegularLabs\Component\Conditions\Administrator\Condition\Content\Content;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

class Field extends Content
{
    public function pass(): bool
    {
        if ( ! $this->isArticle())
        {
            return false;
        }

        if (empty($this->params->field))
        {
            return false;
        }

        $item = $this->getArticle();

        if ( ! isset($item->id))
        {
            return false;
        }

        $article_fields = JFieldsHelper::getFields('com_content.article', $item, true);

        foreach ($article_fields as $article_field)
        {
            if ($article_field->id != $this->params->field)
            {
                continue;
            }

            $comparison = ($this->params->comparison ?? null) ?: 'equals';

            if ( ! self::passComparison($this->params->value, $article_field->rawvalue, $comparison)
                && ! self::passComparison($this->params->value, $article_field->value, $comparison)
            )
            {
                return false;
            }

            return true;
        }

        return false;
    }

    private static function hasTime(string $string): bool
    {
        if ( ! self::isDateTimeString($string))
        {
            return false;
        }

        return RL_RegEx::match('^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}', $string);
    }

    private static function isDateTimeString(string $string): bool
    {
        return RL_RegEx::match('^[0-9]{4}-[0-9]{2}-[0-9]{2}', $string);
    }

    private static function passComparison(
        string       $needle,
        string|array $haystack,
        string       $comparison = 'equals'
    ): bool
    {
        $haystack = RL_Array::toArray($haystack);

        if (empty($haystack))
        {
            return $comparison == 'empty';
        }

        // For list values
        if (count($haystack) > 1)
        {
            switch ($comparison)
            {
                case 'not_equals':
                    $needle = RL_Array::toArray($needle);
                    sort($needle);
                    sort($haystack);

                    return $needle != $haystack;

                case 'contains':
                    $needle = RL_Array::toArray($needle);
                    sort($needle);

                    $intersect = array_intersect($needle, $haystack);

                    return $needle == $intersect;

                case 'contains_one':
                    return RL_Array::find($needle, $haystack);

                case 'not_contains':
                    return ! RL_Array::find($needle, $haystack);

                case 'equals':
                default:
                    $needle = RL_Array::toArray($needle);
                    sort($needle);
                    sort($haystack);

                    return $needle == $haystack;
            }
        }

        $haystack = $haystack[0];

        if ($comparison == 'regex')
        {
            return RL_RegEx::match($needle, $haystack);
        }

        // Convert dynamic date values i, like date('yesterday')
        $haystack = self::valueToDateString($haystack, true);
        $has_time = self::hasTime($haystack);
        $needle   = self::valueToDateString($needle, false, $has_time);

        // make the needle and haystack lowercase, so comparisons are case insensitive
        $needle   = RL_String::strtolower($needle);
        $haystack = RL_String::strtolower($haystack);

        switch ($comparison)
        {
            case 'not_equals':
                return $needle != $haystack;

            case 'contains':
            case 'contains_one':
                return str_contains($haystack, $needle);

            case 'not_contains':
                return ! str_contains($haystack, $needle);

            case 'begins_with':
                $length = strlen($needle);

                return substr($haystack, 0, $length) === $needle;

            case 'ends_with':
                $length = strlen($needle);

                if ($length == 0)
                {
                    return true;
                }

                return substr($haystack, -$length) === $needle;

            case 'less_than':
                return $haystack < $needle;

            case 'greater_than':
                return $haystack > $needle;

            case 'less_than_or_equals':
                return $haystack <= $needle;

            case 'greater_than_or_equals':
                return $haystack >= $needle;

            case 'empty':
                return $haystack === '' || $haystack === null;

            case 'equals':
            default:
                return $needle == $haystack;
        }
    }

    private static function valueToDateString(
        string $value,
        bool   $apply_offset = true,
        bool   $add_time = false
    ): string
    {
        $value = trim($value);

        if (
            in_array($value, [
                'now()',
                'JFactory::getDate()',
            ], true)
        )
        {
            if ( ! $apply_offset)
            {
                return date('Y-m-d H:i:s', strtotime('now'));
            }

            $date = new JDate('now', JFactory::getApplication()->get('offset', 'UTC'));

            return $date->format('Y-m-d H:i:s');
        }

        if (self::isDateTimeString($value))
        {
            $format = 'Y-m-d H:i:s';
            $date   = new JDate($value, JFactory::getApplication()->get('offset', 'UTC'));

            if ($apply_offset)
            {
                $date = JFactory::getDate($value, 'UTC');
                $date->setTimezone(new DateTimeZone(JFactory::getApplication()->get('offset')));
            }

            return $date->format($format, true, false);
        }

        $regex = '^date\(\s*'
            . '(?:\'(?<datetime>.*?)\')?'
            . '(?:\\\\?,\s*\'(?<format>.*?)\')?'
            . '\s*\)$';

        if ( ! RL_RegEx::match($regex, $value, $match))
        {
            return $value;
        }

        $datetime = ($match['datetime'] ?? null) ?: 'now';
        $format   = $match['format'] ?? '';

        if (empty($format))
        {
            $time   = date('His', strtotime($datetime));
            $format = (int) $time || $add_time ? 'Y-m-d H:i:s' : 'Y-m-d';
        }

        $date = new JDate($datetime, JFactory::getApplication()->get('offset', 'UTC'));

        if ($apply_offset)
        {
            $date = JFactory::getDate($datetime, 'UTC');
            $date->setTimezone(new DateTimeZone(JFactory::getApplication()->get('offset')));
        }

        return $date->format($format, true, false);
    }
}
