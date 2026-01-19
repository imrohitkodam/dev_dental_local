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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Menu;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\LanguageHelper as JLanguageHelper;
use Joomla\CMS\Menu\MenuItem as JMenuItem;
use Joomla\CMS\Uri\Uri as JUri;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\RegEx;
use RegularLabs\Library\StringHelper;

class HomePage extends Condition
{
    public function pass(): bool
    {
        $home = JFactory::getApplication()->getMenu('site')->getDefault(JFactory::getApplication()->getLanguage()->getTag());

        // return if option or other set values do not match the homepage menu item values
        if ( ! $this->request->option)
        {
            return $this->checkPass($home) || $this->checkPass($home, true);
        }

        // check if option is different to home menu
        if ( ! $home || ! isset($home->query['option']) || $home->query['option'] != $this->request->option)
        {
            return false;
        }

        if ( ! $this->request->option)
        {
            // set the view/task/layout in the menu item to empty if not set
            $home->query['view']   ??= '';
            $home->query['task']   ??= '';
            $home->query['layout'] ??= '';
        }

        // check set values against home menu query items
        foreach ($home->query as $key => $value)
        {
            if (
                (isset($this->request->{$key}) && $this->request->{$key} != $value)
                || (
                    ( ! isset($this->request->{$key})
                        || in_array($value, [
                            'virtuemart', 'mijoshop',
                        ]))
                    && RL_Input::get($key) != $value
                )
            )
            {
                return false;
            }
        }

        // check post values against home menu params
        foreach ($home->getParams() as $key => $value)
        {
            if (
                ($value && isset($_POST[$key]) && $_POST[$key] != $value)
                || ( ! $value && isset($_POST[$key]) && $_POST[$key])
            )
            {
                return false;
            }
        }

        return $this->checkPass($home) || $this->checkPass($home, true);
    }

    private function checkPass(?JMenuItem &$home, bool $addlang = false): bool
    {
        $uri = JUri::getInstance();

        if ($addlang)
        {
            $sef = $uri->getVar('lang');

            if (empty($sef))
            {
                $langs = array_keys(JLanguageHelper::getLanguages('sef'));
                $path  = StringHelper::substr(
                    $uri->toString(['scheme', 'user', 'pass', 'host', 'port', 'path']),
                    StringHelper::strlen($uri->base())
                );
                $path  = RegEx::replace('^index\.php/?', '', $path);
                $parts = explode('/', $path);
                $part  = reset($parts);

                if (in_array($part, $langs))
                {
                    $sef = $part;
                }
            }

            if (empty($sef))
            {
                return false;
            }
        }

        $query = $uri->toString(['query']);

        if ( ! str_contains($query, 'option=') && ! str_contains($query, 'Itemid='))
        {
            $url = $uri->toString(['host', 'path']);
        }
        else
        {
            $url = $uri->toString(['host', 'path', 'query']);
        }

        // remove the www.
        $url = RegEx::replace('^www\.', '', $url);
        // replace ampersand chars
        $url = str_replace('&amp;', '&', $url);
        // remove any language vars
        $url = RegEx::replace('((\?)lang=[a-z-_]*(&|$)|&lang=[a-z-_]*)', '\2', $url);
        // remove trailing nonsense
        $url = trim(RegEx::replace('/?\??&?$', '', $url));
        // remove the index.php/
        $url = RegEx::replace('/index\.php(/|$)', '/', $url);
        // remove trailing /
        $url = trim(RegEx::replace('/$', '', $url));

        $root = JUri::root();

        // remove the http(s)
        $root = RegEx::replace('^.*?://', '', $root);
        // remove the www.
        $root = RegEx::replace('^www\.', '', $root);
        //remove the port
        $root = RegEx::replace(':[0-9]+', '', $root);
        // so also passes on urls with trailing /, ?, &, /?, etc...
        $root = RegEx::replace('(Itemid=[0-9]*).*^', '\1', $root);
        // remove trailing /
        $root = trim(RegEx::replace('/$', '', $root));

        if ($addlang)
        {
            $root .= '/' . $sef;
        }

        /* Pass urls:
         * [root]
         */
        $regex = '^' . $root . '$';

        if (RegEx::match($regex, $url))
        {
            return true;
        }

        /* Pass urls:
         * [root]?Itemid=[menu-id]
         * [root]/?Itemid=[menu-id]
         * [root]/index.php?Itemid=[menu-id]
         * [root]/[menu-alias]
         * [root]/[menu-alias]?Itemid=[menu-id]
         * [root]/index.php?[menu-alias]
         * [root]/index.php?[menu-alias]?Itemid=[menu-id]
         * [root]/[menu-link]
         * [root]/[menu-link]&Itemid=[menu-id]
         */
        $regex = '^' . $root
            . '(/('
            . 'index\.php'
            . '|'
            . '(index\.php\?)?' . RegEx::quote($home->alias)
            . '|'
            . RegEx::quote($home->link)
            . ')?)?'
            . '(/?[\?&]Itemid=' . (int) $home->id . ')?'
            . '$';

        return RegEx::match($regex, $url);
    }
}
