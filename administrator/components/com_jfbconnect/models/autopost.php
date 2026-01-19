<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

jimport('joomla.application.component.model');

class JFBConnectModelAutopost extends JModelLegacy
{
    var $_pagination = null;
    var $channel_id;

    function __construct($channelId)
    {
        $this->channel_id = $channelId;

        parent::__construct();
    }

    function &getPagination()
    {
        if ($this->_pagination == null)
        {
            $this->getActivityList();
        }
        return $this->_pagination;
    }

    public function setData($autopostIds)
    {
        $this->delete();
        $this->setMapping($autopostIds);
    }

    public function getData()
    {
        $query = $this->_db->getQuery(true);
        $query->select('ap.*')
            ->from($this->_db->qn('#__jfbconnect_autopost') . ' ap')
            ->where('channel_id = ' . $this->_db->q($this->channel_id));
        $this->_db->setQuery($query);
        $this->_data = $this->_db->loadObjectList();

        return $this->_data;
    }

    public function delete()
    {
        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__jfbconnect_autopost"))
            ->where($this->_db->qn("channel_id") . '=' . $this->channel_id);
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    private function setMapping($ogTypes)
    {
        $filter = JFilterInput::getInstance();
        $columns = array('channel_id', 'opengraph_type', 'created');
        $query = $this->_db->getQuery(true);
        $query->insert($this->_db->qn("#__jfbconnect_autopost"))
            ->columns($this->_db->qn($columns));

        foreach ($ogTypes as $ogType)
        {
            $channelId = $filter->clean($this->channel_id, 'INT');
            if (is_int($channelId) && !is_null($ogType))
            {
                $query->clear('values');
                $query->values($channelId . ',' . $this->_db->q($ogType) .',NOW()');
                $this->_db->setQuery($query);
                $this->_db->query();
            }
        }
    }

    public function deleteActivity($activityId)
    {
        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__jfbconnect_autopost_activity"))
            ->where($this->_db->qn("id") . '=' . $activityId);
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    function getActivityList()
    {
        // Lets load the data if it doesn't already exist
        if (empty($this->_listData))
        {
            $app = JFactory::getApplication();

            $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
            $limitstart = $app->getUserStateFromRequest('com_jfbconnect.activity.limitstart', 'limitstart', 0, 'int');

            //Search
            $search = $app->getUserStateFromRequest('com_jfbconnect.activity.search', 'search', '', 'string');
            $search = JString::strtolower($search);
           
            //Filter
            $type = $app->getUserStateFromRequest('com_jfbconnect.activity.type', 'type', -1, 'int');
            $provider = $app->getUserStateFromRequest('com_jfbconnect.activity.provider', 'provider', '', 'string');

            // Get our row count for pagination
            $query = $this->_db->getQuery(true);
            $query->select('COUNT(*) count')
                ->from($this->_db->qn('#__jfbconnect_autopost_activity') . ' ac');

            if ( $search )
            {
                $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $search ) ) ) . '%' );
                $where = array( );
                $where[] = 'LOWER(ac.url) LIKE ' . $key;
                $where[] = 'LOWER(ac.provider) LIKE ' . $key;
                $where[] = 'LOWER(ac.channel_type) LIKE ' . $key;
                $where[] = 'LOWER(ac.ext) LIKE ' . $key;
                $where[] = 'LOWER(ac.layout) LIKE ' . $key;
                $where[] = 'LOWER(ac.response) LIKE ' . $key;
                $query->where( '(' . implode( ' OR ', $where ) . ')' );
            }
         
            if($type != -1)
            {
                $query->where( 'ac.type = ' . ( int ) $type );
            }

            if($provider)
            {
                $key = $this->_db->q( '%' . $this->_db->escape( trim( strtolower( $provider ) ) ) . '%' );
                $query->where( 'LOWER(ac.provider) LIKE ' . $key );
            }

            $this->_db->setQuery($query);
            $total = $this->_db->loadResult();

            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($total, $limitstart, $limit);

            // Get our rows
            $query->clear('select');
            $query->select('ac.*');

            $this->_listData = $this->_getList($query, $limitstart, $limit);
        }

        return $this->_listData;
    }


}