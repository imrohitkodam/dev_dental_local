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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Other;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\Input as RL_Input;

/**
 * Class Component
 *
 * @package RegularLabs\Library\Condition
 */
class Component extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        $option = RL_Input::getCmd('option') == 'com_categories'
            ? 'com_categories'
            : $this->request->option;

        $options = [
            strtolower($option),
            strtolower(str_replace('com_', '', $option)),
        ];

        return $this->passSimple($options);
    }
}
