<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

if (class_exists('JComponentRouterBase')) {
    abstract class AcymailingRouterBase extends JComponentRouterBase
    {
    }
} else {
    class AcymailingRouterBase
    {
        var $app;
        var $menu;

        public function __construct($app = null, $menu = null)
        {
            $this->app = empty($app) ? JFactory::getApplication('site') : $app;
            $this->menu = empty($menu) ? $this->app->getMenu() : $menu;
        }
    }
}

