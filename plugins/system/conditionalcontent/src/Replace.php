<?php
/**
 * @package         Conditional Content
 * @version         5.5.7
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\System\ConditionalContent;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Api\Conditions as Api_Conditions;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\PluginTag as RL_PluginTag;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

class Replace
{
    static $article;

    public static function replaceTags(&$string, $area = 'article', $context = '', $article = null)
    {
        self::$article = $article;

        if ( ! is_string($string) || $string == '')
        {
            return false;
        }

        if ( ! RL_String::contains($string, Params::getTags(true)))
        {
            return false;
        }

        // Check if tags are in the text snippet used for the search component
        if (str_starts_with($context, 'com_search.'))
        {
            $limit = explode('.', $context, 2);
            $limit = (int) array_pop($limit);

            $string_check = substr($string, 0, $limit);

            if ( ! RL_String::contains($string_check, Params::getTags(true)))
            {
                return false;
            }
        }

        $params = Params::get();
        $regex  = Params::getRegex();

        // allow in component?
        if (RL_Protect::isRestrictedComponent($params->disabled_components ?? [], $area))
        {

            Protect::_($string);

            $string = RL_RegEx::replace($regex, '\2', $string);

            RL_Protect::unprotect($string);

            return true;
        }

        Protect::_($string);

        [$start_tags, $end_tags] = Params::getTags();

        [$pre_string, $string, $post_string] = RL_Html::getContentContainingSearches(
            $string,
            $start_tags,
            $end_tags
        );

        RL_RegEx::matchAll($regex, $string, $matches);

        foreach ($matches as $match)
        {
            self::replaceTag($string, $match);
        }

        $string = $pre_string . $string . $post_string;

        RL_Protect::unprotect($string);

        return true;
    }

    private static function getContent($match)
    {
        $params = Params::get();
        $parts  = self::getParts($match);

        foreach ($parts as $part)
        {
            $attributes = self::getTagValues($part->data);
            unset($attributes->trim);

            $has_access = self::hasAccess($attributes);
            $has_access = $match['tag'] == $params->tag_hide ? ! $has_access : $has_access;

            if ($has_access)
            {
                return $part->content;
            }
        }

        return '';
    }

    private static function getParts($data)
    {
        $params = Params::get();
        $regex  = Params::getRegexElse($data['tag'] == $params->tag_hide ? 'hide' : 'show');

        RL_RegEx::matchAll($regex, $data['content'], $matches, null, PREG_OFFSET_CAPTURE);

        if (empty($matches) || empty($matches[0]))
        {
            return [
                (object) [
                    'data'    => $data['data'],
                    'content' => $data['content'],
                ],
            ];
        }

        $parts = [
            (object) [
                'data'    => $data['data'],
                'content' => substr($data['content'], 0, $matches[0][0][1]),
            ],
        ];

        foreach ($matches[0] as $i => $match)
        {
            $offset   = $match[1] + strlen($match[0]);
            $next_pos = isset($matches[0][$i + 1])
                ? $matches[0][$i + 1][1]
                : strlen($data['content']);
            $length   = $next_pos - $offset;

            $parts[] = (object) [
                'data'    => $matches['data'][$i][0],
                'content' => substr($data['content'], $offset, $length),
            ];
        }

        return $parts;
    }

    private static function getTagValues($string)
    {
        $string = html_entity_decode($string);

        return RL_PluginTag::getAttributesFromString($string, null, [], false);
    }

    private static function hasAccess($attributes)
    {
        if (empty($attributes))
        {
            return true;
        }

        $conditions = [
            'menu__menu_item',
            'menu__home_page',
            'date__date',
            'visitor__access_level',
            'visitor__user_group',
            'visitor__language',
            'agent__device',
            'other__condition',
        ];

        return (new Api_Conditions(self::$article))
            ->setConditionByAttributes($attributes)
            ->pass($conditions);
    }

    private static function replaceTag(&$string, $match)
    {
        $params = Params::get();

        $content = self::getContent($match);

        $attributes = self::getTagValues($match['data']);
        $trim       = $attributes->trim ?? $params->trim;

        if ($trim)
        {
            $tags = RL_Html::cleanSurroundingTags([
                'start_pre'  => $match['start_pre'],
                'start_post' => $match['start_post'],
            ], ['p', 'span', 'div']);

            $match = [...$match, ...$tags];

            $tags = RL_Html::cleanSurroundingTags([
                'end_pre'  => $match['end_pre'],
                'end_post' => $match['end_post'],
            ], ['p', 'span', 'div']);

            $match = [...$match, ...$tags];

            $tags = RL_Html::cleanSurroundingTags([
                'start_pre' => $match['start_pre'],
                'end_post'  => $match['end_post'],
            ], ['p', 'span', 'div']);

            $match = [...$match, ...$tags];
        }

        if ($params->place_comments)
        {
            $content = Protect::wrapInCommentTags($content);
        }

        $replace = $match['start_pre'] . $match['start_post'] . $content . $match['end_pre'] . $match['end_post'];

        $string = str_replace($match[0], $replace, $string);
    }
}
