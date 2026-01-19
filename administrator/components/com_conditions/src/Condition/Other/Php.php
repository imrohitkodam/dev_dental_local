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
use RegularLabs\Library\Php as RL_Php;

class Php extends Condition
{
    public function pass(): bool
    {
        if ( ! is_array($this->selection))
        {
            $this->selection = [$this->selection];
        }

        $code = '<?php ' . implode(";\n", $this->selection) . '; ?>';

        return RL_Php::execute($code, $this->article, $this->module, true) ? true : false;
    }
}
