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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Visitor;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

class Language extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        return $this->passSimple(JFactory::getApplication()->getLanguage()->getTag(), null, true);
    }
}
