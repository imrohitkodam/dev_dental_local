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

class JFBConnectControllerAjax extends JFBConnectController
{
    function display($cachable = false, $urlparams = false)
    {
        // Not to be called directly
        JFactory::getApplication()->exit(0);
    }

    public function fetch()
    {
        if (!JSession::checkToken('get'))
            exit;

        $input = JFactory::getApplication()->input;
        $library = $input->getCmd('library', null);
        $subtask = $input->getCmd('subtask', null);
        if ($library && $subtask)
        {
            $lib = JFBCFactory::library($library);
            $task = 'ajax' . ucfirst($subtask);
            $result = $lib->$task();
        }
        exit;
    }

    /**
     * Obtain the SEF URL for provided base64_encoded url parameter.
     * Needed primarily for admin area to get SEF URLs from front-end
     */
    function sef()
    {
        $input = JFactory::getApplication()->input;
        $url = $input->getBase64('url');
        $url = base64_decode($url);
        $route = JRoute::_( $url, true, -1); /* NOTE: -1 value makes this an absolute URL */

        echo $route;
        JFactory::getApplication()->close(0);
    }

}
