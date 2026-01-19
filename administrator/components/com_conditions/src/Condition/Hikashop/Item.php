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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Hikashop;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

class Item extends Hikashop
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ($this->request->option !== 'com_hikashop')
        {
            return false;
        }

        if ($this->request->view !== 'product')
        {
            return false;
        }

        return $this->passSimple($this->request->id);
    }
}
