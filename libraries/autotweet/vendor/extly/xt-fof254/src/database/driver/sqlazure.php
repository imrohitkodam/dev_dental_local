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
 * SQL Server database driver
 *
 * @see    http://msdn.microsoft.com/en-us/library/ee336279.aspx
 * @since  12.1
 */
class XTF0FDatabaseDriverSqlazure extends XTF0FDatabaseDriverSqlsrv
{
    /**
     * The name of the database driver.
     *
     * @var string
     *
     * @since  12.1
     */
    public $name = 'sqlazure';
}
