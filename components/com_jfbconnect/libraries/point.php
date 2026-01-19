<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */
class JFBConnectPoint extends JRegistry
{
    public function award()
    {
        $data = $this->getData();
        if ($data)
            JFactory::getApplication()->triggerEvent('socialprofilesAwardPoints', $data);
    }

    public function remove()
    {
        $data = $this->getData();
        if ($data)
            JFactory::getApplication()->triggerEvent('socialprofilesRemovePoints', $data);
    }

    private function getData()
    {
        $data = array();
        $name = $this->get('name', '');
        $key = $this->get('key', '');
        if (!$name || !$key)
            return null;

        $data['name'] = $name;
        $data['data'] = $this;
        return $data;
    }
}