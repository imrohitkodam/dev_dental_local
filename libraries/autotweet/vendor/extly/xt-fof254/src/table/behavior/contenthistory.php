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
 * FrameworkOnFramework table behavior class for content History
 *
 * @since    2.2.0
 */
class XTF0FTableBehaviorContenthistory extends XTF0FTableBehavior
{
    /**
     * The event which runs after storing (saving) data to the database
     *
     * @param XTF0FTable &$table The table which calls this event
     *
     * @return bool True to allow saving without an error
     */
    public function onAfterStore(&$table)
    {
        $aliasParts = explode('.', $table->getContentType());
        $table->checkContentType();

        if (JComponentHelper::getParams($aliasParts[0])->get('save_history', 0)) {
            $jHelperContenthistory = new JHelperContenthistory($table->getContentType());
            $jHelperContenthistory->store($table);
        }

        return true;
    }

    /**
     * The event which runs before deleting a record
     *
     * @param XTF0FTable &$table The table which calls this event
     * @param int        $oid    The PK value of the record to delete
     *
     * @return bool True to allow the deletion
     */
    public function onBeforeDelete(&$table, $oid)
    {
        $aliasParts = explode('.', $table->getContentType());

        if (JComponentHelper::getParams($aliasParts[0])->get('save_history', 0)) {
            $jHelperContenthistory = new JHelperContenthistory($table->getContentType());
            $jHelperContenthistory->deleteHistory($table);
        }

        return true;
    }
}
