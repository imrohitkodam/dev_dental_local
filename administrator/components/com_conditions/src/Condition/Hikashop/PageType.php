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

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

defined('_JEXEC') or die;

class PageType extends Hikashop
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ($this->request->option !== 'com_hikashop')
        {
            return false;
        }

        $type = $this->request->view;

        if (
            ($type == 'product' && in_array($this->request->task, ['contact', 'show']))
        )
        {
            $type .= '_' . $this->request->task;
        }
        elseif (
            ($type == 'product' && in_array($this->request->layout, ['contact', 'show']))
            || ($type == 'user' && in_array($this->request->layout, ['cpanel']))
        )
        {
            $type .= '_' . $this->request->layout;
        }

        return $this->passSimple($type);
    }
}
