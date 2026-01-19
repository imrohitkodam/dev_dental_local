<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * Access level field header
 *
 * @since    2.0
 */
class XTF0FFormHeaderAccesslevel extends XTF0FFormHeaderFieldselectable
{
    /**
     * Method to get the list of access levels
     *
     * @return array a list of access levels
     *
     * @since   2.0
     */
    protected function getOptions()
    {
        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();
        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true);

        $xtf0FDatabaseQuery->select('a.id AS value, a.title AS text');
        $xtf0FDatabaseQuery->from('#__viewlevels AS a');
        $xtf0FDatabaseQuery->group('a.id, a.title, a.ordering');
        $xtf0FDatabaseQuery->order('a.ordering ASC');
        $xtf0FDatabaseQuery->order($xtf0FDatabaseQuery->qn('title').' ASC');

        // Get the options.
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery);
        $options = $xtf0FDatabaseDriver->loadObjectList();

        return $options;
    }
}
