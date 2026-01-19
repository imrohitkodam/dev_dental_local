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
 * FrameworkOnFramework query base class; for compatibility purposes
 *
 * @since       2.1
 * @deprecated  2.1
 */
abstract class XTF0FQueryAbstract
{
    /**
     * Returns a new database query class
     *
     * @param XTF0FDatabaseDriver $db The DB driver which will provide us with a query object
     *
     * @return XTF0FQueryAbstract
     */
    public static function &getNew($db = null)
    {
        XTF0FPlatform::getInstance()->logDeprecated('XTF0FQueryAbstract is deprecated. Use XTF0FDatabaseQuery instead.');

        $ret = null === $db ? XTF0FPlatform::getInstance()->getDbo()->getQuery(true) : $db->getQuery(true);

        return $ret;
    }
}
