<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

jimport('joomla.application.component.modellist');
class JFBConnectModelChannels extends JModelList
{
    function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
                ->from('#__jfbconnect_channel');
        return $query;
    }

    /**
     * Fetch object list of channels based on input query
     * @param array $where
     * @return mixed
     */
    function getChannels($where = array())
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
                ->from('#__jfbconnect_channel');
        foreach ($where as $key => $value)
            $query->where($db->qn($key) . '=' . $db->q($value));

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}