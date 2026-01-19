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

class JFBConnectProviderFacebookWidgetLogin extends JFBConnectProviderWidgetLogin
{
    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'jfbcLogin');

        $this->examples = array (
            '{JFBCLogin}',
            '{JFBCLogin text=Login With Facebook logout=true logout_url=http://www.sourcecoast.com}'
        );

        $this->className = 'jfbcLogin';
        $this->tagName = 'JFBCLogin';
    }
}
