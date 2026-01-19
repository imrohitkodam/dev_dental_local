<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

use RegularLabs\Library\ActionLogPlugin as RL_ActionLogPlugin;
use RegularLabs\Library\Language as RL_Language;

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
    class PlgActionlogConditions extends RL_ActionLogPlugin
    {
        public $name  = 'CONDITIONS';
        public $alias = 'conditions';

        public function addItems(): void
        {
            $this->addItem('com_conditions', 'item', 'CON_ITEM');
        }

        public function onContentAfterSave($context, $table, $isNew, $data = [])
        {
            if ($context !== 'com_conditions.condition')
            {
                return;
            }

            parent::onContentAfterSave($context, $table, $isNew, $data);
        }

        public function onConditionAfterMap($data)
        {
            RL_Language::load($data->extension);

            $item        = $this->getItem($data->extension);
            $item->title = $data->item_name;
            $item->id    = $data->item_id;

            $this->option = $data->extension;
            parent::onContentAfterSave($item->context, $item, false);
            $this->option = 'com_conditions';
        }
    }
}
