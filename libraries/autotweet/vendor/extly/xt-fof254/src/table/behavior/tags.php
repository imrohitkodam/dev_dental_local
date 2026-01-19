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
 * FrameworkOnFramework table behavior class for tags
 *
 * @since    2.1
 */
class XTF0FTableBehaviorTags extends XTF0FTableBehavior
{
    /**
     * The event which runs after binding data to the table
     *
     * @param XTF0FTable   &$table  The table which calls this event
     * @param object|array &$src    The data to bind
     * @param array        $options The options of the table
     *
     * @return bool True on success
     */
    public function onAfterBind(&$table, &$src, $options = [])
    {
        // Bind tags
        if ($table->hasTags()) {
            if ((!empty($src['tags']) && '' != $src['tags'][0])) {
                $table->newTags = $src['tags'];
            }

            // Check if the content type exists, and create it if it does not
            $table->checkContentType();

            $tagsTable = clone $table;

            $jHelperTags = new JHelperTags();
            $jHelperTags->typeAlias = $table->getContentType();

            // TODO: This little guy here fails because JHelperTags
            // need a JTable object to work, while our is XTF0FTable
            // Need probably to write our own XTF0FHelperTags
            // Thank you com_tags
            if (!$jHelperTags->postStoreProcess($tagsTable)) {
                $table->setError('Error storing tags');

                return false;
            }
        }

        return true;
    }

    /**
     * The event which runs before storing (saving) data to the database
     *
     * @param XTF0FTable &$table      The table which calls this event
     * @param bool       $updateNulls Should nulls be saved as nulls (true) or just skipped over (false)?
     *
     * @return bool True to allow saving
     */
    public function onBeforeStore(&$table, $updateNulls)
    {
        if ($table->hasTags()) {
            $jHelperTags = new JHelperTags();
            $jHelperTags->typeAlias = $table->getContentType();

            // TODO: JHelperTags sucks in Joomla! 3.1, it requires that tags are
            // stored in the metadata property. Not our case, therefore we need
            // to add it in a fake object. We sent a PR to Joomla! CMS to fix
            // that. Once it's accepted, we'll have to remove the attrocity
            // here...
            $tagsTable = clone $table;
            $jHelperTags->preStoreProcess($tagsTable);
        }
    }

    /**
     * The event which runs after deleting a record
     *
     * @param XTF0FTable &$table The table which calls this event
     * @param int        $oid    The PK value of the record which was deleted
     *
     * @return bool True to allow the deletion without errors
     */
    public function onAfterDelete(&$table, $oid)
    {
        // If this resource has tags, delete the tags first
        if ($table->hasTags()) {
            $jHelperTags = new JHelperTags();
            $jHelperTags->typeAlias = $table->getContentType();

            if (!$jHelperTags->deleteTagData($table, $oid)) {
                $table->setError('Error deleting Tags');

                return false;
            }
        }

        return null;
    }
}
