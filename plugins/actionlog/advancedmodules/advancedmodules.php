<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

use RegularLabs\Library\ActionLogPlugin as RL_ActionLogPlugin;

defined('_JEXEC') or die;

if (version_compare(JVERSION, 4, '<') || version_compare(JVERSION, 7, '>='))
{
    return;
}

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml')
    || ! class_exists('RegularLabs\Library\ActionLogPlugin')
)
{
    return;
}

if (true)
{
    class PlgActionlogAdvancedModules extends RL_ActionLogPlugin
    {
        public $name  = 'ADVANCEDMODULEMANAGER';
        public $alias = 'advancedmodules';

        public function addItems(): void
        {
            $this->addItem('com_advancedmodules', 'module', 'PLG_ACTIONLOG_JOOMLA_TYPE_MODULE');
        }
    }
}
