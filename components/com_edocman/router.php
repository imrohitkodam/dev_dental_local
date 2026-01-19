<?php
/**
 * @version        1.9.5
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();
JLoader::registerPrefix('EDocman', JPATH_ROOT . '/components/com_edocman');
require_once JPATH_ROOT . '/components/com_edocman/helper/route.php';
class EdocmanRouter extends JComponentRouterBase
{
    function build(&$query)
    {
        $objectName = '';
        $segments = array();
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        $queryArr = $query;

        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $parentId = 0;
        // We need a menu item. Either the one specified in the query, or the current active one if none specified
        if (empty($query['Itemid'])) {
            $menuItem = $menu->getActive();
        } else {
            $menuItem = $menu->getItem($query['Itemid']);
        }
        if ($menuItem) {
            if (isset($menuItem->query['view']) && ($menuItem->query['view'] == 'categories' || $menuItem->query['view'] == 'category')) {
                $parentId = (int)$menuItem->query['id'];
            }
        }
        if (empty($menuItem->query['view'])) {
            $menuItem->query['view'] = '';
        }
        // Are we dealing with the current view which is attached to a menu item?
        if (($menuItem instanceof stdClass) && isset($query['view']) && isset($query['id']) && $menuItem->query['view'] == $query['view'] &&
            isset($query['id']) && $menuItem->query['id'] == intval($query['id'])
        ) {
            unset($query['view']);
            if (isset($query['catid'])) {
                unset($query['catid']);
            }
            unset($query['id']);
        }

        // Dealing with the catid parameter in the link to document.
        if (($menuItem instanceof stdClass) && ($menuItem->query['view'] == 'category') && isset($query['catid']) &&
            $menuItem->query['id'] == intval($query['catid'])
        ) {
            if (isset($query['catid'])) {
                unset($query['catid']);
            }
        }

        // Dealing with upload link
        if (($menuItem instanceof stdClass) && ($menuItem->query['view'] == 'document') && isset($menuItem->query['layout']) && ($menuItem->query['layout'] == 'edit')) {
            if (isset($query['view'])) {
                unset($query['view']);
            }

            if (isset($query['layout'])) {
                unset($query['layout']);
            }
        }

        $view = isset($query['view']) ? $query['view'] : '';
        $id = isset($query['id']) ? (int)$query['id'] : 0;
        $catId = isset($query['catid']) ? (int)$query['catid'] : 0;
        $task = isset($query['task']) ? $query['task'] : '';
        switch ($view) {
            case 'categories':
            case 'category':
                if ($query['id']) {
                    $segments = array_merge($segments, EDocmanHelperRoute::getCategoriesPath($id, 'alias', true, $parentId));
                }
                if (!empty($query['format']) && $query['format'] != 'html') {
                    $segments[] = $query['format'];
                    if (!empty($query['type'])) {
                        $segments[] = $query['type'];
                    }
                }
                unset($query['id']);
                $objectName = 'category';
                break;
            case 'document':
                if ($id) {
                    $segments[] = EDocmanHelperRoute::getDocumentTitle($id);
                }
                unset($query['id']);
                $config = EDocmanHelper::getConfig();
                if (($catId) && ($config->insert_category != 2)) {
                    $segments = array_merge(EDocmanHelperRoute::getCategoriesPath($catId, 'alias', true, $parentId), $segments);
                }
                if (isset($query['layout']) && $query['layout'] == 'edit') {
                    if ($id) {
                        $segments[] = 'Edit';
                    } else {
                        $segments[] = 'Upload Document';
                        $queryArr['id'] = 0;
                    }
                    unset($query['layout']);
                    break;
                }
                $objectName = 'document';
                break;
            case 'license':
                if ($id) {
                    $q->clear();
                    $q->select('title')
                        ->from('#__edocman_licenses')
                        ->where('id=' . $id);
                    $db->setQuery($q);
                    $segments[] = $db->loadResult();
                }
                $segments[] = 'View License';
                unset($query['id']);
                $objectName = 'license';
                break;
            case 'search':
                $segments[] = 'Search result';
                break;
            case 'documents':
                $segments[] = 'Documents List';
                break;
            case 'users':
                $segments[] = 'Users List';
                break;
        }

        switch ($task) {
            case 'document.download':
                if ($id) {
                    $segments[] = EDocmanHelperRoute::getDocumentTitle($id);
                }
                unset($query['id']);
                $segments[] = 'Download';
                unset($query['task']);
                $objectName = 'document';
                break;
            case 'document.viewdoc':
                if ($id) {
                    $segments[] = EDocmanHelperRoute::getDocumentTitle($id);
                }
                unset($query['id']);
                $segments[] = 'Viewdocument';
                unset($query['task']);
                $objectName = 'document';
                break;
			case 'document.forcedownload':
				if ($id) {
                    $segments[] = EDocmanHelperRoute::getDocumentTitle($id);
                }
                unset($query['id']);
                $segments[] = 'fdocument';
                unset($query['task']);
                $objectName = 'document';
				break;
            case 'document.edit':
                if ($id) {
                    $segments[] = EDocmanHelperRoute::getDocumentTitle($id);
                }
                unset($query['id']);
                $segments[] = 'Edit Document';
                unset($query['task']);
                $objectName = 'document';
                break;
        }
        if (isset($query['view'])) {
            unset($query['view']);
        }

        if (isset($query['catid'])) {
            unset($query['catid']);
        }
        if (count($segments)) {
            // Store the query string to use in the parseRouter method

            $unProcessedVariables = array(
                'option',
                'Itemid',
                'filter_category_id',
                'filter_search',
                'filter_tag',
                'start',
                'limitstart',
                'limit',
                'download_code'
            );


            foreach ($unProcessedVariables as $variable) {
                if (isset($queryArr[$variable])) {
                    unset($queryArr[$variable]);
                }
            }

            $queryString = http_build_query($queryArr);
            $segments = array_map('JApplicationHelper::stringURLSafe', $segments);
            $key = md5(implode('/', $segments));
            $q = $db->getQuery(true);
            $q->select('id')
                ->from('#__edocman_urls')
                ->where('md5_key="' . $key . '"');
            $db->setQuery($q);
            $urlId = $db->loadResult();
            if ($urlId) {
                $q->update('#__edocman_urls')
                    ->set('`query`="' . $queryString . '"')
                    ->where('id=' . $urlId);
                $db->setQuery($q);
                $db->execute();
            } else {
                $q->clear();
                $q->insert('#__edocman_urls')
                    ->columns('md5_key, `query`, `object_name`, `object_id`')
                    ->values("'$key', '$queryString', '$objectName', '$id'");
                $db->setQuery($q);
                $db->execute();
            }
        }

        return $segments;
    }

    /**
     *
     *
     * Parse the segments of a URL.
     *
     * @param
     *            array    The segments of the URL to parse.
     *
     * @return array URL attributes to be used by the application.
     */
    function parse( & $segments)
    {
        $vars = array();
        if (count($segments)) {
            $db = JFactory::getDbo();
            $key = md5(str_replace(':', '-', implode('/', $segments)));
            $query = $db->getQuery(true);
            $query->select('`query`')
                ->from('#__edocman_urls')
                ->where('md5_key="' . $key . '"');
            $db->setQuery($query);
            $queryString = $db->loadResult();
            if ($queryString) {
                parse_str(html_entity_decode($queryString), $vars);
            }
            if (version_compare(JVERSION, '4.0.0-dev', 'ge')) {
                $segments = [];
            }
        }

        $item = JFactory::getApplication()->getMenu()->getActive();
        if ($item) {
            if (!empty($vars['view']) && !empty($item->query['view']) && $vars['view'] == $item->query['view']) {
                foreach ($item->query as $key => $value) {
                    if ($key != 'option' && $key != 'Itemid' && !isset($vars[$key])) {
                        $vars[$key] = $value;
                    }
                }
            }
        }

        if (isset($vars['view']) && $vars['view'] == 'document') {
            if (isset($vars['layout']) && $vars['layout'] != 'edit') {
                $vars['layout'] = 'default';
            }
        }

        return $vars;
    }
}

function EdocmanBuildRoute(&$query)
{
    $router = new EdocmanRouter();

    return $router->build($query);
}

function EdocmanParseRoute($segments)
{
    $router = new EdocmanRouter();

    return $router->parse($segments);
}