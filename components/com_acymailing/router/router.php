<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class AcymailingRouter extends AcymailingRouterBase
{
    private $pagesNotSef;
    private $paramsNotSef;
    private $separator = '-';

    public function __construct($app = null, $menu = null)
    {
        parent::__construct($app, $menu);

        $this->pagesNotSef = [
            'stats',
            'moduleloader',
            'cron',
            'fronteditor',
            'frontfilter',
            'sub',
            'captcha',
        ];

        $this->paramsNotSef = [
            'option',
            'Itemid',
            'start',
            'format',
            'limitstart',
            'no_html',
            'val',
            'key',
            'acyformname',
            'subid',
            'tmpl',
            'lang',
            'limit',
            'acm',
            'idU',
        ];
    }

    public function build(&$query)
    {
        $segments = [];

        if (isset($query['ctrl']) && in_array($query['ctrl'], $this->pagesNotSef)) {
            return $segments;
        }

        $ctrl = '';
        $task = '';

        if (isset($query['ctrl'])) {
            $ctrl = $query['ctrl'];
            if ($ctrl != 'archive' || (!empty($query['task']) && $query['task'] != 'view')) $segments[] = $query['ctrl'];
            unset($query['ctrl']);
            if (isset($query['task'])) {
                $task = $query['task'];
                if ($ctrl != 'archive' || $task != 'view') $segments[] = $query['task'];
                unset($query['task']);
            }
        } elseif (isset($query['view'])) {
            $ctrl = $query['view'];
            $segments[] = $query['view'];
            unset($query['view']);
            if (isset($query['layout'])) {
                $task = $query['layout'];
                $segments[] = $query['layout'];
                unset($query['layout']);
            }
        }

        if (empty($query)) return $segments;

        foreach ($query as $name => $value) {
            if (in_array($name, $this->paramsNotSef)) continue;
            if (strlen($name) > 25) continue;

            if ($ctrl == 'user' && $name == 'mailid') continue;

            $segments[] = $name.$this->separator.$value;
            unset($query[$name]);
        }

        return $segments;
    }

    public function parse(&$segments)
    {
        if (empty($segments)) return [];

        if (strpos(current($segments), $this->separator) === false) {
            $vars = [];
            $vars['ctrl'] = array_shift($segments);
            $vars['task'] = '';
        } else {
            $jsite = JFactory::getApplication('site');
            $menus = $jsite->getMenu();
            $menu = $menus->getActive();
            if (!empty($menu) && !empty($menu->query)) {
                $vars = $menu->query;
            } else {
                $vars = [];
            }

            if (!isset($vars['ctrl'])) {
                $vars['ctrl'] = isset($vars['view']) ? $vars['view'] : '';
            }
            if (!isset($vars['task'])) {
                $vars['task'] = isset($vars['layout']) ? $vars['layout'] : '';
            }
        }

        if (!empty($segments)) {
            if (strpos(current($segments), $this->separator) === false) {
                $vars['task'] = array_shift($segments);
            } elseif ($vars['ctrl'] === 'archive' && empty($vars['task'])) {
                $vars['task'] = 'view';
                $mail = array_shift($segments);
                list($id, $alias) = explode($this->separator, $mail, 2);
                $vars['id'] = $id;
            }
        }

        foreach ($segments as $i => $name) {
            if (strpos($name, $this->separator) === false) continue;

            list($arg, $val) = explode($this->separator, $name, 2);
            $vars[$arg] = $val;

            if ($arg === 'listid') {
                $vars[$arg] = intval($val);
            }
            unset($segments[$i]);
        }

        if ((empty($vars['ctrl']) || $vars['ctrl'] === 'lists') && (!empty($vars['listid']) || !empty($vars['mailid']))) {
            $vars['ctrl'] = 'archive';
            if (!empty($vars['mailid'])) $vars['task'] = 'view';
        }

        if (!empty($vars['ctrl']) && $vars['ctrl'] === 'archive' && !empty($vars['mailid']) && (empty($vars['task']) || $vars['task'] === 'listing')) {
            $vars['task'] = 'view';
        }

        return $vars;
    }
}

