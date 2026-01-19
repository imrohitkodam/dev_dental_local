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

class JFBConnectProviderInstagramWidgetLogin extends JFBConnectProviderWidgetLogin
{
    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scInstagramLoginTag');

        $this->className = 'scInstagramLoginTag';
        $this->tagName = 'SCInstagramLogin';
    }
}
