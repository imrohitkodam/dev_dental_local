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

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Helper\Summary;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel;
use RegularLabs\Library\Input as RL_Input;

echo (new Conditions)->render();
die;

class Conditions
{
    private $extension = '';
    private $message   = '';

    public function getCondition()
    {
        $id            = RL_Input::getInt('id');
        $enabled_types = RL_Input::getString('enabled_types');

        if ($id)
        {
            return (new ItemModel)->getConditionById($id, false, $enabled_types);
        }

        $extension = RL_Input::getCmd('extension');
        $item_id   = RL_Input::getInt('item_id');

        if ($extension && $item_id)
        {
            return (new ItemModel)->getConditionByExtensionItem($extension, $item_id, false, $enabled_types);
        }

        $data = [];

        $form = RL_Input::getRaw('form', []);

        foreach ($form as $key => $value)
        {
            $key        = str_replace('jform[', '', $key);
            $data[$key] = $value;
        }

        if (isset($data['extension']))
        {
            $this->extension = $data['extension'];
        }

        if (isset($data['message']))
        {
            $this->message = $data['message'];
        }

        if (isset($data['enabled_types']))
        {
            $enabled_types = $data['enabled_types'];
            unset($data['enabled_types']);
        }

        return (new ItemModel)->getConditionFromData($data, $enabled_types);
    }

    public function render()
    {
        $condition = $this->getCondition();

        $extension = $this->extension ?: RL_Input::getCmd('extension');
        $message   = $this->message ?: RL_Input::get('message', '');

        if ($extension && $condition && $condition->published !== 1)
        {
            $condition = null;
        }

        return json_encode((object) [
            'has_conditions' => ! empty($condition),
            'id'             => $condition->id ?? '',
            'alias'          => $condition->alias ?? '',
            'name'           => $condition->name ?? '',
            'content'        => Summary::render($condition, $extension, $message),
        ]);
    }
}
