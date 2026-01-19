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
 * FrameworkOnFramework model behavior class
 *
 * @since    2.1
 */
class XTF0FModelBehaviorEmptynonzero extends XTF0FModelBehavior
{
    /**
     * This event runs when we are building the query used to fetch a record
     * list in a model
     *
     * @param XTF0FModel         &$model The model which calls this event
     * @param XTF0FDatabaseQuery &$query The query being built
     *
     * @return void
     */
    public function onBeforeBuildQuery(&$model, &$query)
    {
        $model->setState('_emptynonzero', '1');
    }
}
