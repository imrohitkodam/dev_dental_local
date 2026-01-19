<?php
/**
 * @package         Snippets
 * @version         9.3.8
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
    class PlgActionlogSnippets extends RL_ActionLogPlugin
    {
        public $name  = 'SNIPPETS';
        public $alias = 'snippets';

        public function addItems(): void
        {
            $this->addItem('com_snippets', 'item', 'SNP_ITEM');
        }
    }
}
