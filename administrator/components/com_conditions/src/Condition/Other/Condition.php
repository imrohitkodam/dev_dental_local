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

use RegularLabs\Component\Conditions\Administrator\Api\Conditions as Api_Conditions;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition as BaseCondition;

/**
 * Class Component
 *
 * @package RegularLabs\Library\Condition
 */
class Condition extends BaseCondition
{
    public function pass(): bool
    {
        return (new Api_Conditions($this->article))
            ->setConditionByMixed($this->selection)
            ->pass();
    }
}
