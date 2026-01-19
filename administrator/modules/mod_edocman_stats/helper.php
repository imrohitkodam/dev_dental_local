<?php
/**
 * @version    1.0
 * @package    Edocman
 * @author     Ossolution https://www.joomdonation.com
 * @copyright  Copyright (c) 2012 - 2018 Ossolution Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

class modEdocmanStatsHelper
{
    public static function getLatestItems()
    {
        $db = JFactory::getDBO();
        $query = "SELECT i.id,i.title, i.created_user_id, i.created_time, v.name AS author FROM #__edocman_documents as i 
        LEFT JOIN #__users AS v ON v.id = i.created_user_id 
        WHERE i.published = 1 
        ORDER BY i.created_time DESC";
        $db->setQuery($query, 0, 10);
        $rows = $db->loadObjectList();
        return $rows;
    }

    public static function getPopularItems()
    {
        $db = JFactory::getDBO();
        $query = "SELECT i.id,i.title, i.created_user_id, i.created_time, i.hits, v.name AS author FROM #__edocman_documents as i 
        LEFT JOIN #__users AS v ON v.id = i.created_user_id 
        WHERE i.published = 1 
        ORDER BY i.hits DESC";
        $db->setQuery($query, 0, 10);
        $rows = $db->loadObjectList();
        return $rows;
    }

    public static function getMostDownloadItems()
    {
        $db = JFactory::getDBO();
        $query = "SELECT i.id,i.title, i.created_user_id, i.created_time, i.downloads, v.name AS author FROM #__edocman_documents as i 
        LEFT JOIN #__users AS v ON v.id = i.created_user_id 
        WHERE i.published = 1 
        ORDER BY i.downloads DESC";
        $db->setQuery($query, 0, 10);
        $rows = $db->loadObjectList();
        return $rows;
    }

    public static function getStatistics()
    {
        $statistics = new stdClass;
        $statistics->numOfItems = self::countItems();
        $statistics->numOfCategories = self::countCategories();
        $statistics->numOfTags = self::countTags();
        return $statistics;
    }

    public static function countItems()
    {
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) FROM #__edocman_documents";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    public static function countCategories()
    {
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) FROM #__edocman_categories";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    public static function countTags()
    {
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(*) FROM #__edocman_tags";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

}
