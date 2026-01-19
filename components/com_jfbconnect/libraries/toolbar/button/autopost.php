<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectToolbarButtonAutopost extends JFBConnectToolbarButton
{
    var $order = '40';
    var $displayName = "Post Activity";
    var $systemName = "autopost";

    public function html()
    {
        $html = '
            <div id="social-autopost" style="position:fixed; bottom:45px; display:none;">';

        $autopost = $this->getAutopostList();
        $html .=
            <<<EOT
                <div>
                    <p>
                        {$autopost}
                    </p>
                </div>
EOT;
        $html .= "</div>";
        return $html;
    }

    public function ajaxGetHtml()
    {
        echo $this->html();
    }

    protected function generateJavascript()
    {
        return "display: function ()
                    {
                        if (jfbcJQuery('#social-autopost').css('display') == 'none')
                        {
                            jfbcJQuery('#social-autopost').accordion();
                            jfbcJQuery('#social-autopost').css('display', 'block');
                            jfbcJQuery('#social-autopost').accordion('refresh');
                        }
                        else
                            jfbcJQuery('#social-autopost').css('display', 'none');
                    }";
    }

    private function getAutopostList()
    {
        $input = JFactory::getApplication()->input;
        $refresh = $input->get('refresh'); //for updating autopost list
        $option = $refresh ? $input->get('ext') : $input->get('option');

        //Compare link against relative link stored in the activity table
        if(!$refresh)
        {
            $uri = JURI::getInstance();
            $uri = JFBConnectUtilities::stripQuery($uri);
            $currentUrl = $uri->toString();
            $root = JURI::root();
            $currentUrl = str_replace($root, "", $currentUrl);
        }
        else
        {
            $currentUrl = base64_decode($input->get('url'));
        }

        $autopostExt = JFBCFactory::getAllAutopostExtensions();
        if(in_array($option, $autopostExt))
        {
            $view = $refresh ? $input->get('extview') : $input->get('view');
            $itemID = $refresh ? $input->get('extid') : $input->get('id');//com_content, com_k2, com_easyblog

            if($option == 'com_community' && !$refresh)
            {
                if($view == 'groups')
                    $itemID = JRequest::getCmd('groupid');
                else if($view == 'events')
                    $itemID = JRequest::getCmd('eventid');
            }

            if($option == 'com_easyblog' && $view == 'entry' && !$refresh)
                $view = 'post';

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('act.*')
                ->from('#__jfbconnect_autopost_activity act')
                ->where('(' . $db->qn('act.ext') . '=' . $db->q($option) . ' AND ' .
                    $db->qn('act.layout') . '=' . $db->q($view) . ' AND ' .
                    $db->qn('act.item_id') . '=' . $db->q($itemID) . ' AND ' .
                    $db->qn('act.type') . '=1)'
                    , 'OR')
                ->where('(' . $db->qn('act.type') . '=0 AND ' .
                    $db->qn('act.url') . '=' . $db->q($currentUrl) .')'
                )
                ->order('act.created DESC LIMIT 10');
            $rows = $db->setQuery($query)->loadObjectList();
        }
        else
        {
            //only get the manual post
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('act.*')
                ->from('#__jfbconnect_autopost_activity act')
                ->where('(' . $db->qn('act.type') . '=0 AND ' .
                    $db->qn('act.url') . '=' . $db->q($currentUrl) .')'
                )
                ->order('act.created DESC LIMIT 10');
            $rows = $db->setQuery($query)->loadObjectList();
        }

        $html = '<table class="table table-striped" width="90%">';
        $html .= '<thead>
            <th>'.JText::_('COM_JFBCONNECT_POST_ID_LABEL').'</th>
            <th>'.JText::_('COM_JFBCONNECT_POST_TYPE_LABEL').'</th>
            <th>'.JText::_('COM_JFBCONNECT_POST_CHANNEL_PROVIDER_LABEL').'</th>
            <th>'.JText::_('COM_JFBCONNECT_POST_CHANNEL_TYPE_LABEL').'</th>
            <th>'.JText::_('COM_JFBCONNECT_POST_RESPONSE_LABEL').'</th>
            <th>'.JText::_('COM_JFBCONNECT_POST_CREATED_LABEL').'</th>
        </thead><tbody>';
        foreach($rows as $row)
        {
            $html .= '<tr>'
                . '<td>'.$row->id.'</td>'
                . '<td>'.($row->type == 1 ? JText::_('COM_JFBCONNECT_POST_TYPE_AUTOPOST_OPTION') : JText::_('COM_JFBCONNECT_POST_TYPE_MANUAL_OPTION')).'</td>'
                . '<td>'.$row->provider.'</td>'
                . '<td>'.$row->channel_type.'</td>'
                . '<td>'.$row->response.'</td>'
                . '<td>'.$row->created.'</td></tr>';
        }
        $html .= "</tbody></table>";
        return $html;
    }
}