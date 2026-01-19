<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\System\Snippets;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Component\Snippets\Administrator\Model\ItemsModel as ItemsModel;
use RegularLabs\Library\Alias as RL_Alias;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Date as RL_Date;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\PluginTag as RL_PluginTag;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\User as RL_User;

class Replace
{
    static $items = [];

    public static function getAlias($snippet)
    {
        if (isset($snippet->alias))
        {
            return $snippet->alias;
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('a.alias')
            ->from($db->quoteName('#__snippets', 'a'))
            ->order('a.published DESC');

        if (isset($snippet->title))
        {
            $query->where(RL_DB::in('a.name', $snippet->title));
        }

        if (isset($snippet->id))
        {
            $query->where(RL_DB::in('a.id', $snippet->id));
        }

        $db->setQuery($query);

        return $db->loadResult();
    }

    public static function getItem($alias = '')
    {
        if (empty($alias) || ! $alias)
        {
            return null;
        }

        if (isset(self::$items[$alias]))
        {
            return self::$items[$alias];
        }

        $list  = new ItemsModel;
        $items = $list->getItems(false, [$alias]);

        if ( ! isset($items[$alias]))
        {
            $items = $list->getItems(false, [RL_String::html_entity_decoder($alias)]);
        }

        if ( ! isset($items[$alias]))
        {
            return null;
        }

        self::$items = [...self::$items, ...$items];

        return $items[$alias];
    }

    public static function handleIfStructures(&$content, $variables)
    {
    }

    public static function handleVariableTags(&$content, $variables)
    {
    }

    public static function replaceTags(&$string, $area = 'article', $context = '', $article = null)
    {
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

            $string = RL_RegEx::replace($regex, '', $string);

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

        if (empty($matches))
        {
            $string = $pre_string . $string . $post_string;

            RL_Protect::unprotect($string);

            return false;
        }

        $break_count = 0;

        while ($break_count++ < 20
            && ! empty($matches))
        {
            self::renderSnippets($string, $matches, $area);
            RL_RegEx::matchAll($regex, $string, $matches);
        }

        $string = $pre_string . $string . $post_string;

        RL_Protect::unprotect($string);

        if (Params::get()->fix_html && $area != 'head')
        {
            $string = RL_Html::fix($string);
        }

        return true;
    }

    protected static function getIfStructureResult($statements, $variables)
    {
    }

    protected static function passIfCondition($condition, $variables)
    {
    }

    protected static function passIfStatement($statement, $variables)
    {
    }

    protected static function variableExists($condition, $variables)
    {
    }

    private static function flattenObject(&$object, &$flat = null)
    {
    }

    private static function geUserValue($key)
    {
    }

    private static function getArticleValue($key)
    {
    }

    private static function getContact()
    {
    }

    private static function getDateFromFormat($date)
    {
    }

    private static function getDateValue($value)
    {
    }

    private static function getEscapeValue($value)
    {
    }

    private static function getLowercaseValue($value)
    {
    }

    private static function getProfile()
    {
    }

    private static function getRandomValue($value)
    {
    }

    private static function getRandomValueFromRange($range)
    {
    }

    private static function getSnippetFromTag($match)
    {
        $attributes = RL_PluginTag::getAttributesFromString($match['id'], 'title', [], '', null, false);

        $snippet = (object) [
            'match' => $match,
        ];

        if (isset($attributes->id))
        {
            $snippet->id = (int) $attributes->id;
        }

        if (isset($attributes->alias))
        {
            $snippet->alias = $attributes->alias;
        }

        if (isset($attributes->title))
        {
            $snippet->title = $attributes->title;
        }

        unset($attributes->id);
        unset($attributes->alias);
        unset($attributes->title);

        $snippet->variables = $attributes;

        return $snippet;
    }

    private static function getSnippetValue($key)
    {
    }

    private static function getUppercaseValue($value)
    {
    }

    private static function getUser()
    {
    }

    private static function getVariables($item_variables, $tag_variables)
    {
        foreach ($item_variables as &$variable)
        {
            $key_underscore = RL_String::toUnderscoreCase($variable->key);
            $key_dash       = RL_String::toDashCase($variable->key);

            $variable = (object) [
                'key'     => $variable->key,
                'value'   => $tag_variables->{$key_underscore} ?? $tag_variables->{$key_dash} ?? null,
                'default' => $variable->default,
            ];
        }

        return $item_variables;
    }

    private static function processSnippet($snippet, $area = 'article')
    {
        $params = Params::get();
        $item   = self::getItem($snippet->alias);

        if ( ! $item)
        {
            if ($params->place_comments)
            {
                return Protect::getMessageCommentTag(JText::_('SNP_OUTPUT_REMOVED_NOT_FOUND'));
            }

            return '';
        }

        if ( ! $item->published)
        {
            if ($params->place_comments)
            {
                return Protect::getMessageCommentTag(JText::_('SNP_OUTPUT_REMOVED_NOT_ENABLED'));
            }

            return '';
        }

        if ($area == 'head')
        {
            if ($item->enable_in_head == 0 || $item->enable_in_head == -1 && $params->enable_in_head == 0)
            {
                return false;
            }

            if ($item->enable_in_head == 2 || $item->enable_in_head == -1 && $params->enable_in_head == 2)
            {
                return '';
            }
        }

        $html = $item->content;


        if ($item->remove_paragraphs == 1 || $item->remove_paragraphs == -1 && $params->remove_paragraphs)
        {
            return self::removeParagraphs($html);
        }

        return $html;
    }

    private static function removeParagraphs($string)
    {
        // Remove leading paragraph tags
        $string = RL_RegEx::replace('^(\s*</?p[^>]*>)+', '', $string);
        // Remove trailing paragraph tags
        $string = RL_RegEx::replace('(</?p[^>]*>\s*)+$', '', $string);
        // Replace paragraph tags with double breaks
        $string = RL_RegEx::replace('(</p>\s*<p[^>]*>|</?p[^>]*>)', '<br><br>', $string);

        return $string;
    }

    private static function renderSnippet($snippet, $area = 'article')
    {
        if (empty($snippet->alias))
        {
            return false;
        }

        $params = Params::get();

        $content = self::processSnippet($snippet, $area);

        if ($content === false)
        {
            return false;
        }

        $item = self::getItem($snippet->alias);

        $place_comments = $params->place_comments;

        if (isset($item->place_comments))
        {
            $place_comments = $item->place_comments == 1 || $params->place_comments && $item->place_comments == -1;
        }

        if ($place_comments && $area != 'head')
        {
            $content = Protect::wrapInCommentTags($content);
        }

        $same_surrounding_tags = isset($snippet->match['pretag'])
            && isset($snippet->match['posttag'])
            && $snippet->match['pretag'] == $snippet->match['posttag'];

        if ( ! RL_Html::containsBlockElements($content) || ! $same_surrounding_tags || ! $params->strip_surrounding_tags)
        {
            $content = $snippet->match['pre']
                . $content
                . $snippet->match['post'];
        }

        return $content;
    }

    private static function renderSnippets(&$string, $matches, $area = 'article')
    {
        $snippets = [];
        $aliases  = [];

        foreach ($matches as &$match)
        {
            $snippet        = self::getSnippetFromTag($match);
            $snippet->alias = self::getAlias($snippet);

            if ( ! $snippet->alias)
            {
                continue;
            }

            $snippets[] = $snippet;
            $aliases[]  = trim($snippet->alias);
        }

        $aliases = array_unique($aliases);

        $list = new ItemsModel;

        self::$items = [...self::$items, ...$list->getItems(false, $aliases)];

        foreach ($snippets as $snippet)
        {
            $output = self::renderSnippet($snippet, $area);

            if ($output === false)
            {
                continue;
            }

            $string = RL_String::replaceOnce($snippet->match[0], $output, $string);
        }
    }

    /**
     * double [[tag]]...[[/tag]] style tag on multiple lines
     */
    private static function replaceDoubleTagByType(&$string, $type)
    {
    }

    private static function replaceMatchByType($type, &$string, $match)
    {
    }

    /**
     * single [[tag:...]] style tag on single line
     */
    private static function replaceTagByType(&$string, $type)
    {
    }

    private static function replaceThIndDate(&$date, $th = '[TH]')
    {
    }

    private static function setParam(&$object, $key, $value)
    {
    }
}
