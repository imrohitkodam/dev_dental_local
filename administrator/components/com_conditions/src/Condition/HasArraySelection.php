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

namespace RegularLabs\Component\Conditions\Administrator\Condition;

defined('_JEXEC') or die;

use RegularLabs\Library\ArrayHelper as RL_Array;

/**
 * Class ConditionList
 *
 * @package RegularLabs\Library
 */
trait HasArraySelection
{
    protected function prepareSelection(): void
    {
        if ( ! is_array($this->selection))
        {
            $this->selection = RL_Array::toArray($this->selection);
            $this->selection = RL_Array::clean($this->selection);
        }
    }
}
